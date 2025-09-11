<?php
// Customer Products API

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

switch ($action) {
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
        $category = $_GET['category'] ?? '';
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
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
            $where = ["p.status = 'published'"];
            $params = [];
            
            if (!empty($search)) {
                $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if (!empty($category)) {
                $where[] = "c.slug = ?";
                $params[] = $category;
            }
            
            if ($minPrice !== null) {
                $where[] = "p.price >= ?";
                $params[] = floatval($minPrice);
            }
            
            if ($maxPrice !== null) {
                $where[] = "p.price <= ?";
                $params[] = floatval($maxPrice);
            }
            
            $whereClause = implode(' AND ', $where);
            
            // Get total count
            $countSql = "
                SELECT COUNT(DISTINCT p.id) as total
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
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
                    s.store_name as seller_name,
                    GROUP_CONCAT(pi.image_url) as images
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
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
                ],
                'filters' => [
                    'search' => $search,
                    'category' => $category,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch products: ' . $e->getMessage()]);
        }
        break;
        
    case 'detail':
    case $id:
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $productId = $action === 'detail' ? $id : $action;
        
        if (!$productId || !is_numeric($productId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid product ID required']);
            break;
        }
        
        try {
            $sql = "
                SELECT 
                    p.*,
                    c.name as category_name,
                    c.slug as category_slug,
                    s.store_name as seller_name,
                    s.id as seller_id,
                    AVG(pr.rating) as average_rating,
                    COUNT(pr.id) as review_count
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
                LEFT JOIN product_reviews pr ON p.id = pr.product_id
                WHERE p.id = ? AND p.status = 'published'
                GROUP BY p.id
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            
            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                break;
            }
            
            // Get product images
            $stmt = $pdo->prepare("SELECT image_url, alt_text FROM product_images WHERE product_id = ? ORDER BY sort_order");
            $stmt->execute([$productId]);
            $images = $stmt->fetchAll();
            
            // Get recent reviews
            $stmt = $pdo->prepare("
                SELECT 
                    pr.*,
                    u.first_name,
                    u.last_name
                FROM product_reviews pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.product_id = ? AND pr.is_approved = 1
                ORDER BY pr.created_at DESC
                LIMIT 10
            ");
            $stmt->execute([$productId]);
            $reviews = $stmt->fetchAll();
            
            // Format data
            $product['price'] = floatval($product['price']);
            $product['stock_quantity'] = intval($product['stock_quantity']);
            $product['average_rating'] = $product['average_rating'] ? round(floatval($product['average_rating']), 1) : null;
            $product['review_count'] = intval($product['review_count']);
            $product['in_stock'] = $product['stock_quantity'] > 0 && $product['stock_status'] === 'in_stock';
            $product['images'] = $images;
            
            foreach ($reviews as &$review) {
                $review['rating'] = intval($review['rating']);
                $review['customer_name'] = $review['first_name'] . ' ' . substr($review['last_name'], 0, 1) . '.';
                $review['review_images'] = $review['images'] ? json_decode($review['images'], true) : [];
                unset($review['first_name'], $review['last_name'], $review['images']);
            }
            
            $product['reviews'] = $reviews;
            
            echo json_encode([
                'success' => true,
                'data' => $product
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch product: ' . $e->getMessage()]);
        }
        break;
        
    case 'categories':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            $sql = "
                SELECT 
                    c.*,
                    COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 'published'
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.name
            ";
            
            $stmt = $pdo->query($sql);
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
            echo json_encode(['success' => false, 'message' => 'Failed to fetch categories: ' . $e->getMessage()]);
        }
        break;
        
    case 'search':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $query = $_GET['q'] ?? '';
        
        if (empty($query) || strlen($query) < 2) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Search query must be at least 2 characters']);
            break;
        }
        
        try {
            $sql = "
                SELECT 
                    p.id,
                    p.name,
                    p.price,
                    c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' 
                AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
                ORDER BY 
                    CASE 
                        WHEN p.name LIKE ? THEN 1
                        WHEN p.name LIKE ? THEN 2
                        ELSE 3
                    END,
                    p.name
                LIMIT 20
            ";
            
            $searchParam = "%$query%";
            $exactParam = "$query%";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$searchParam, $searchParam, $searchParam, $exactParam, $searchParam]);
            $results = $stmt->fetchAll();
            
            foreach ($results as &$result) {
                $result['price'] = floatval($result['price']);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $results,
                'query' => $query,
                'count' => count($results)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Search failed: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Products endpoint not found']);
}
?>