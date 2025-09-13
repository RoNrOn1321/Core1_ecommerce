<?php
require_once __DIR__ . '/../config/database.php';

class OrderManager {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    public function getOrders($sellerId, $filters = []) {
        try {
            $where = "WHERE oi.seller_id = ?";
            $params = [$sellerId];
            
            if (!empty($filters['status'])) {
                $where .= " AND o.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['payment_status'])) {
                $where .= " AND o.payment_status = ?";
                $params[] = $filters['payment_status'];
            }
            
            if (!empty($filters['search'])) {
                $where .= " AND (o.order_number LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($filters['date_from'])) {
                $where .= " AND DATE(o.created_at) >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $where .= " AND DATE(o.created_at) <= ?";
                $params[] = $filters['date_to'];
            }
            
            $limit = isset($filters['limit']) ? (int)$filters['limit'] : 20;
            $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
            
            $sql = "
                SELECT o.*, 
                       u.first_name, u.last_name, u.email,
                       COUNT(oi.id) as item_count,
                       SUM(oi.total_price) as seller_total
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN users u ON o.user_id = u.id
                $where
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT $limit OFFSET $offset
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll();
            
            // Get total count
            $countSql = "
                SELECT COUNT(DISTINCT o.id) as total 
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN users u ON o.user_id = u.id
                $where
            ";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            return [
                'success' => true,
                'orders' => $orders,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getOrder($sellerId, $orderId) {
        try {
            // Get order details
            $stmt = $this->pdo->prepare("
                SELECT o.*, u.first_name, u.last_name, u.email, u.phone
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND EXISTS (
                    SELECT 1 FROM order_items WHERE order_id = o.id AND seller_id = ?
                )
            ");
            $stmt->execute([$orderId, $sellerId]);
            $order = $stmt->fetch();
            
            if (!$order) {
                return ['success' => false, 'message' => 'Order not found or access denied'];
            }
            
            // Get order items for this seller
            $stmt = $this->pdo->prepare("
                SELECT oi.*, p.name as product_name, 
                       pi.image_url as product_image
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE oi.order_id = ? AND oi.seller_id = ?
            ");
            $stmt->execute([$orderId, $sellerId]);
            $order['items'] = $stmt->fetchAll();
            
            // Get status history
            $stmt = $this->pdo->prepare("
                SELECT * FROM order_status_history 
                WHERE order_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$orderId]);
            $order['status_history'] = $stmt->fetchAll();
            
            return ['success' => true, 'order' => $order];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateOrderStatus($sellerId, $orderId, $status, $notes = '') {
        try {
            // Verify seller has access to this order
            $stmt = $this->pdo->prepare("
                SELECT o.id FROM orders o
                WHERE o.id = ? AND EXISTS (
                    SELECT 1 FROM order_items WHERE order_id = o.id AND seller_id = ?
                )
            ");
            $stmt->execute([$orderId, $sellerId]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Order not found or access denied'];
            }
            
            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                return ['success' => false, 'message' => 'Invalid status'];
            }
            
            $this->pdo->beginTransaction();
            
            // Update order status
            $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $orderId]);
            
            // Add to status history
            $stmt = $this->pdo->prepare("
                INSERT INTO order_status_history (order_id, status, notes, created_by)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$orderId, $status, $notes, $sellerId]);
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => 'Order status updated successfully'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateTrackingInfo($sellerId, $orderId, $trackingData) {
        try {
            // Verify seller has access to this order
            $stmt = $this->pdo->prepare("
                SELECT o.id FROM orders o
                WHERE o.id = ? AND EXISTS (
                    SELECT 1 FROM order_items WHERE order_id = o.id AND seller_id = ?
                )
            ");
            $stmt->execute([$orderId, $sellerId]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Order not found or access denied'];
            }
            
            $updateFields = [];
            $params = [];
            
            if (!empty($trackingData['courier_company'])) {
                $updateFields[] = "courier_company = ?";
                $params[] = $trackingData['courier_company'];
            }
            
            if (!empty($trackingData['tracking_number'])) {
                $updateFields[] = "tracking_number = ?";
                $params[] = $trackingData['tracking_number'];
            }
            
            if (!empty($trackingData['estimated_delivery_date'])) {
                $updateFields[] = "estimated_delivery_date = ?";
                $params[] = $trackingData['estimated_delivery_date'];
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No tracking information provided'];
            }
            
            $params[] = $orderId;
            $sql = "UPDATE orders SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Tracking information updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getOrderStats($sellerId) {
        try {
            $stats = [];
            
            // Total orders
            $stmt = $this->pdo->prepare("
                SELECT COUNT(DISTINCT o.id) as total
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.seller_id = ?
            ");
            $stmt->execute([$sellerId]);
            $stats['total_orders'] = $stmt->fetch()['total'];
            
            // Orders by status
            $stmt = $this->pdo->prepare("
                SELECT o.status, COUNT(DISTINCT o.id) as count
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.seller_id = ?
                GROUP BY o.status
            ");
            $stmt->execute([$sellerId]);
            $statusCounts = $stmt->fetchAll();
            
            $stats['by_status'] = [];
            foreach ($statusCounts as $row) {
                $stats['by_status'][$row['status']] = $row['count'];
            }
            
            // Total revenue
            $stmt = $this->pdo->prepare("
                SELECT SUM(oi.total_price) as total_revenue
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE oi.seller_id = ? AND o.payment_status = 'paid'
            ");
            $stmt->execute([$sellerId]);
            $stats['total_revenue'] = $stmt->fetch()['total_revenue'] ?? 0;
            
            // Recent orders (last 30 days)
            $stmt = $this->pdo->prepare("
                SELECT COUNT(DISTINCT o.id) as recent_orders
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.seller_id = ? AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stmt->execute([$sellerId]);
            $stats['recent_orders'] = $stmt->fetch()['recent_orders'];
            
            return ['success' => true, 'stats' => $stats];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>