<?php
session_start();

// Add CORS headers
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Set content type
header('Content-Type: application/json');

try {
    // Include database config
    require_once '../config/database.php';

    // Use PDO connection from config
    $conn = $pdo;
} catch (Exception $e) {
    error_log("Wishlist API Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Service temporarily unavailable'
    ]);
    exit();
}

// Check authentication
function checkAuth() {
    if (!isset($_SESSION['customer_id']) || empty($_SESSION['customer_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Please log in to access your wishlist'
        ]);
        exit();
    }
    return $_SESSION['customer_id'];
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if (empty($path) || $path === '/') {
                getWishlistItems();
            } elseif ($path === '/count') {
                getWishlistCount();
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
            }
            break;
            
        case 'POST':
            if ($path === '/add') {
                addToWishlist();
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
            }
            break;
            
        case 'DELETE':
            if (preg_match('/^\/remove\/(\d+)$/', $path, $matches)) {
                removeFromWishlist($matches[1]);
            } elseif ($path === '/clear') {
                clearWishlist();
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("Wishlist API Error: " . $e->getMessage());
    error_log("Wishlist API Stack Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request'
    ]);
}

function getWishlistItems() {
    global $conn;
    $userId = checkAuth();
    
    $sql = "SELECT w.id as wishlist_id, w.created_at, 
                   p.id, p.name, p.description, p.price, p.compare_price,
                   s.store_name, s.id as seller_id,
                   pi.image_url as image
            FROM wishlist_items w
            JOIN products p ON w.product_id = p.id
            LEFT JOIN sellers s ON p.seller_id = s.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE w.user_id = ? AND p.status = 'published'
            ORDER BY w.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    
    $items = [];
    while ($row = $stmt->fetch()) {
        // Format image URL
        if ($row['image']) {
            // Handle different image URL formats
            if (strpos($row['image'], '/Core1_ecommerce/uploads/') !== 0) {
                $row['image'] = '/Core1_ecommerce/uploads/' . $row['image'];
            }
        } else {
            $row['image'] = '/Core1_ecommerce/customer/images/no-image.png';
        }
        
        // Calculate actual price (use compare_price as discount if available)
        $row['discount_price'] = $row['compare_price']; // For compatibility
        $row['price'] = floatval($row['price']);
        $row['compare_price'] = floatval($row['compare_price']);
        $row['actual_price'] = $row['compare_price'] && $row['compare_price'] < $row['price'] ? $row['compare_price'] : $row['price'];
        
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $items,
        'count' => count($items)
    ]);
}

function getWishlistCount() {
    global $conn;
    $userId = checkAuth();
    
    $sql = "SELECT COUNT(*) as count FROM wishlist_items w
            JOIN products p ON w.product_id = p.id
            WHERE w.user_id = ? AND p.status = 'published'";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'count' => (int)$row['count']
        ]
    ]);
}

function addToWishlist() {
    global $conn;
    $userId = checkAuth();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['product_id']) || empty($input['product_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Product ID is required'
        ]);
        return;
    }
    
    $productId = (int)$input['product_id'];
    
    // Check if product exists and is published
    $checkSql = "SELECT id, name FROM products WHERE id = ? AND status = 'published'";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([$productId]);
    $product = $checkStmt->fetch();
    
    if (!$product) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        return;
    }
    
    // Check if already in wishlist
    $existsSql = "SELECT id FROM wishlist_items WHERE user_id = ? AND product_id = ?";
    $existsStmt = $conn->prepare($existsSql);
    $existsStmt->execute([$userId, $productId]);
    $existingItem = $existsStmt->fetch();
    
    if ($existingItem) {
        echo json_encode([
            'success' => false,
            'message' => 'Product is already in your wishlist'
        ]);
        return;
    }
    
    // Add to wishlist
    $insertSql = "INSERT INTO wishlist_items (user_id, product_id, created_at) VALUES (?, ?, NOW())";
    $insertStmt = $conn->prepare($insertSql);
    
    if ($insertStmt->execute([$userId, $productId])) {
        echo json_encode([
            'success' => true,
            'message' => $product['name'] . ' added to your wishlist',
            'data' => [
                'wishlist_id' => $conn->lastInsertId(),
                'product_id' => $productId
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add product to wishlist'
        ]);
    }
}

function removeFromWishlist($wishlistId) {
    global $conn;
    $userId = checkAuth();
    
    // Get product info before deleting
    $infoSql = "SELECT p.name FROM wishlist_items w 
                JOIN products p ON w.product_id = p.id 
                WHERE w.id = ? AND w.user_id = ?";
    $infoStmt = $conn->prepare($infoSql);
    $infoStmt->execute([$wishlistId, $userId]);
    $product = $infoStmt->fetch();
    
    if (!$product) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Wishlist item not found'
        ]);
        return;
    }
    
    // Delete from wishlist
    $deleteSql = "DELETE FROM wishlist_items WHERE id = ? AND user_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    
    if ($deleteStmt->execute([$wishlistId, $userId]) && $deleteStmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => $product['name'] . ' removed from your wishlist'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to remove product from wishlist'
        ]);
    }
}

function clearWishlist() {
    global $conn;
    $userId = checkAuth();
    
    $sql = "DELETE FROM wishlist_items WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$userId])) {
        echo json_encode([
            'success' => true,
            'message' => 'Wishlist cleared successfully',
            'data' => [
                'removed_count' => $stmt->rowCount()
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to clear wishlist'
        ]);
    }
}
?>