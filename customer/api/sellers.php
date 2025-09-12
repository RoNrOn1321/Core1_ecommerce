<?php
// Customer Sellers API

// Headers are set in index.php
require_once __DIR__ . '/../config/database.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// These variables are passed from index.php routing
// $requestMethod, $action, $id are available from the router

switch ($action) {
    case 'products':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $sellerId = $_GET['seller_id'] ?? null;
        
        if (!$sellerId || !is_numeric($sellerId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid seller ID required']);
            break;
        }
        
        // Get query parameters
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(50, intval($_GET['limit'] ?? 12)));
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $sortBy = $_GET['sort_by'] ?? 'created_at';
        $sortOrder = strtoupper($_GET['sort_order'] ?? 'DESC');
        
        // Validate sort order
        if (!in_array($sortOrder, ['ASC', 'DESC'])) {
            $sortOrder = 'DESC';
        }
        
        // Validate sort field
        $allowedSortFields = ['name', 'price', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        $offset = ($page - 1) * $limit;
        
        try {
            // Build query
            $where = ["p.status = 'published'", "p.seller_id = ?"];
            $params = [$sellerId];
            
            if (!empty($search)) {
                $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if (!empty($category)) {
                $where[] = "c.slug = ?";
                $params[] = $category;
            }
            
            $whereClause = implode(' AND ', $where);
            
            // Get total count
            $countSql = "
                SELECT COUNT(DISTINCT p.id) as total
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $whereClause
            ";
            
            $stmt = $pdo->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch()['total'];
            
            // Get products
            $sql = "
                SELECT 
                    p.id,
                    p.name,
                    p.description,
                    p.price,
                    p.stock_quantity,
                    p.stock_status,
                    p.sku,
                    p.created_at,
                    c.name as category_name,
                    c.slug as category_slug,
                    GROUP_CONCAT(pi.image_url) as images
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images pi ON p.id = pi.product_id
                WHERE $whereClause
                GROUP BY p.id
                ORDER BY p.$sortBy $sortOrder
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
            
            // Format products
            foreach ($products as &$product) {
                $product['price'] = floatval($product['price']);
                $product['stock_quantity'] = intval($product['stock_quantity']);
                $product['images'] = $product['images'] ? explode(',', $product['images']) : [];
                $product['in_stock'] = $product['stock_quantity'] > 0 && $product['stock_status'] === 'in_stock';
            }
            
            $totalPages = ceil($total / $limit);
            
            echo json_encode([
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => intval($total),
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch seller products: ' . $e->getMessage()]);
        }
        break;
        
    case 'categories':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $sellerId = $_GET['seller_id'] ?? $id;
        
        if (!$sellerId || !is_numeric($sellerId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid seller ID required']);
            break;
        }
        
        try {
            $sql = "
                SELECT 
                    c.*,
                    COUNT(p.id) as product_count
                FROM categories c
                JOIN products p ON c.id = p.category_id 
                WHERE p.seller_id = ? AND p.status = 'published' AND c.is_active = 1
                GROUP BY c.id
                HAVING product_count > 0
                ORDER BY c.name
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sellerId]);
            $categories = $stmt->fetchAll();
            
            foreach ($categories as &$category) {
                $category['product_count'] = intval($category['product_count']);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $categories
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch seller categories: ' . $e->getMessage()]);
        }
        break;
        
    case '':
    case 'list':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Get query parameters
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, min(50, intval($_GET['limit'] ?? 10)));
        $search = $_GET['search'] ?? '';
        
        $offset = ($page - 1) * $limit;
        
        try {
            // Build query
            $where = ["s.status = 'approved'"];
            $params = [];
            
            if (!empty($search)) {
                $where[] = "(s.store_name LIKE ? OR s.store_description LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            $whereClause = implode(' AND ', $where);
            
            // Get total count
            $countSql = "
                SELECT COUNT(*) as total
                FROM sellers s
                WHERE $whereClause
            ";
            
            $stmt = $pdo->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch()['total'];
            
            // Get sellers
            $sql = "
                SELECT 
                    s.id,
                    s.store_name,
                    s.store_description,
                    s.store_logo,
                    s.business_type,
                    s.created_at,
                    COUNT(p.id) as total_products
                FROM sellers s
                LEFT JOIN products p ON s.id = p.seller_id AND p.status = 'published'
                WHERE $whereClause
                GROUP BY s.id
                ORDER BY s.store_name
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $sellers = $stmt->fetchAll();
            
            // Format sellers
            foreach ($sellers as &$seller) {
                $seller['total_products'] = intval($seller['total_products']);
            }
            
            $totalPages = ceil($total / $limit);
            
            echo json_encode([
                'success' => true,
                'data' => $sellers,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => intval($total),
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch sellers: ' . $e->getMessage()]);
        }
        break;
        
    default:
        // Check if action is a numeric seller ID
        if (is_numeric($action)) {
            if ($requestMethod !== 'GET') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                break;
            }
            
            $sellerId = intval($action);
            
            try {
                $sql = "
                    SELECT 
                        s.*,
                        u.first_name,
                        u.last_name,
                        u.email,
                        COUNT(p.id) as total_products
                    FROM sellers s
                    LEFT JOIN users u ON s.user_id = u.id
                    LEFT JOIN products p ON s.id = p.seller_id AND p.status = 'published'
                    WHERE s.id = ? AND s.status = 'approved'
                    GROUP BY s.id
                ";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$sellerId]);
                $seller = $stmt->fetch();
                
                if (!$seller) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Seller not found']);
                    break;
                }
                
                // Format data
                $seller['total_products'] = intval($seller['total_products']);
                
                // Remove sensitive information
                unset($seller['tax_id']);
                unset($seller['email']); // Don't expose seller email to customers
                
                echo json_encode([
                    'success' => true,
                    'data' => $seller
                ]);
                
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to fetch seller: ' . $e->getMessage()]);
            }
            break;
        }
        
        // If not numeric, return 404
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Sellers endpoint not found']);
}
?>