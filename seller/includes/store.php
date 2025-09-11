<?php
require_once __DIR__ . '/../config/database.php';

class StoreManager {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    public function getStoreProfile($sellerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.*, u.email, u.first_name, u.last_name, u.phone
                FROM sellers s
                JOIN users u ON s.user_id = u.id
                WHERE s.id = ?
            ");
            $stmt->execute([$sellerId]);
            $store = $stmt->fetch();
            
            if (!$store) {
                return ['success' => false, 'message' => 'Store not found'];
            }
            
            return ['success' => true, 'store' => $store];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateStoreProfile($sellerId, $storeData) {
        try {
            $this->pdo->beginTransaction();
            
            // Update seller table
            $sellerFields = [];
            $sellerParams = [];
            
            $allowedSellerFields = [
                'store_name', 'store_description', 'store_logo', 'store_banner',
                'business_type', 'tax_id'
            ];
            
            foreach ($allowedSellerFields as $field) {
                if (array_key_exists($field, $storeData)) {
                    $sellerFields[] = "$field = ?";
                    $sellerParams[] = $storeData[$field];
                }
            }
            
            if (!empty($sellerFields)) {
                $sellerParams[] = $sellerId;
                $sql = "UPDATE sellers SET " . implode(', ', $sellerFields) . " WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($sellerParams);
            }
            
            // Update user table if user data is provided
            $userFields = [];
            $userParams = [];
            
            $allowedUserFields = ['first_name', 'last_name', 'phone'];
            
            foreach ($allowedUserFields as $field) {
                if (array_key_exists($field, $storeData)) {
                    $userFields[] = "$field = ?";
                    $userParams[] = $storeData[$field];
                }
            }
            
            if (!empty($userFields)) {
                // Get user_id for this seller
                $stmt = $this->pdo->prepare("SELECT user_id FROM sellers WHERE id = ?");
                $stmt->execute([$sellerId]);
                $userId = $stmt->fetch()['user_id'];
                
                $userParams[] = $userId;
                $sql = "UPDATE users SET " . implode(', ', $userFields) . " WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($userParams);
            }
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => 'Store profile updated successfully'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getStoreDashboard($sellerId) {
        try {
            $dashboard = [];
            
            // Product count
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total_products,
                    COUNT(CASE WHEN status = 'published' THEN 1 END) as published_products,
                    COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft_products,
                    COUNT(CASE WHEN stock_quantity <= low_stock_threshold THEN 1 END) as low_stock_products
                FROM products 
                WHERE seller_id = ?
            ");
            $stmt->execute([$sellerId]);
            $dashboard['products'] = $stmt->fetch();
            
            // Order statistics
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(DISTINCT o.id) as total_orders,
                    COUNT(CASE WHEN o.status = 'pending' THEN 1 END) as pending_orders,
                    COUNT(CASE WHEN o.status = 'processing' THEN 1 END) as processing_orders,
                    COUNT(CASE WHEN o.status = 'shipped' THEN 1 END) as shipped_orders,
                    COUNT(CASE WHEN o.status = 'delivered' THEN 1 END) as delivered_orders
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.seller_id = ?
            ");
            $stmt->execute([$sellerId]);
            $dashboard['orders'] = $stmt->fetch();
            
            // Revenue statistics
            $stmt = $this->pdo->prepare("
                SELECT 
                    SUM(oi.total_price) as total_revenue,
                    SUM(CASE WHEN DATE(o.created_at) = CURDATE() THEN oi.total_price ELSE 0 END) as today_revenue,
                    SUM(CASE WHEN DATE(o.created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN oi.total_price ELSE 0 END) as week_revenue,
                    SUM(CASE WHEN DATE(o.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN oi.total_price ELSE 0 END) as month_revenue
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.seller_id = ? AND o.payment_status = 'paid'
            ");
            $stmt->execute([$sellerId]);
            $dashboard['revenue'] = $stmt->fetch();
            
            // Recent orders
            $stmt = $this->pdo->prepare("
                SELECT o.id, o.order_number, o.status, o.total_amount, o.created_at,
                       u.first_name, u.last_name,
                       COUNT(oi.id) as item_count
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN users u ON o.user_id = u.id
                WHERE oi.seller_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$sellerId]);
            $dashboard['recent_orders'] = $stmt->fetchAll();
            
            // Top selling products
            $stmt = $this->pdo->prepare("
                SELECT p.name, p.price, SUM(oi.quantity) as total_sold, SUM(oi.total_price) as total_revenue
                FROM products p
                JOIN order_items oi ON p.id = oi.product_id
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.seller_id = ? AND o.payment_status = 'paid'
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT 5
            ");
            $stmt->execute([$sellerId]);
            $dashboard['top_products'] = $stmt->fetchAll();
            
            return ['success' => true, 'dashboard' => $dashboard];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getStoreAnalytics($sellerId, $period = '30') {
        try {
            $analytics = [];
            
            // Sales over time
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE(o.created_at) as date,
                    COUNT(DISTINCT o.id) as orders,
                    SUM(oi.total_price) as revenue,
                    SUM(oi.quantity) as items_sold
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.seller_id = ? 
                    AND o.payment_status = 'paid' 
                    AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(o.created_at)
                ORDER BY date DESC
            ");
            $stmt->execute([$sellerId, $period]);
            $analytics['sales_over_time'] = $stmt->fetchAll();
            
            // Product performance
            $stmt = $this->pdo->prepare("
                SELECT 
                    p.name,
                    p.price,
                    SUM(oi.quantity) as total_sold,
                    SUM(oi.total_price) as revenue,
                    COUNT(DISTINCT oi.order_id) as order_count
                FROM products p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
                WHERE p.seller_id = ?
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT 20
            ");
            $stmt->execute([$sellerId]);
            $analytics['product_performance'] = $stmt->fetchAll();
            
            // Customer insights
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(DISTINCT o.user_id) as unique_customers,
                    AVG(order_totals.order_total) as avg_order_value,
                    COUNT(o.id) / COUNT(DISTINCT o.user_id) as avg_orders_per_customer
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN (
                    SELECT oi2.order_id, SUM(oi2.total_price) as order_total
                    FROM order_items oi2
                    WHERE oi2.seller_id = ?
                    GROUP BY oi2.order_id
                ) order_totals ON o.id = order_totals.order_id
                WHERE oi.seller_id = ? 
                    AND o.payment_status = 'paid'
                    AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            ");
            $stmt->execute([$sellerId, $sellerId, $period]);
            $analytics['customer_insights'] = $stmt->fetch();
            
            return ['success' => true, 'analytics' => $analytics];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>