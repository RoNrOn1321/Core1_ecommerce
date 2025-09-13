<?php
// Customer Reviews API

// Headers are set in index.php
require_once __DIR__ . '/../config/database.php';

// Start session for authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// These variables are passed from index.php routing
// $requestMethod, $action, $id are available from the router

// Get actual logged-in user ID from session
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}
$user_id = $_SESSION['customer_id'];

switch ($action) {
    case '':
    case 'list':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        if (isset($_GET['product_id'])) {
            // Get reviews for a specific product
            getProductReviews($_GET['product_id']);
        } elseif (isset($_GET['order_id'])) {
            // Get reviewable items for an order
            getReviewableItems($_GET['order_id'], $user_id);
        } else {
            // Get user's reviews
            getUserReviews($user_id);
        }
        break;
        
    case 'submit':
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            submitReview($input, $user_id);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'update':
        if ($requestMethod !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            updateReview($input, $user_id);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'delete':
        if ($requestMethod !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            if (isset($_GET['id'])) {
                deleteReview($_GET['id'], $user_id);
            } else {
                throw new Exception('Review ID is required');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Reviews endpoint not found',
            'action' => $action
        ]);
}

function getProductReviews($product_id) {
    global $pdo;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // Get reviews with user info
    $stmt = $pdo->prepare("
        SELECT r.*, u.first_name, u.last_name, u.profile_image,
               CASE WHEN r.is_verified_purchase = 1 THEN 'Verified Purchase' ELSE NULL END as purchase_verified
        FROM product_reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.product_id = ? AND r.is_approved = 1
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$product_id, $limit, $offset]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_reviews WHERE product_id = ? AND is_approved = 1");
    $stmt->execute([$product_id]);
    $total = $stmt->fetchColumn();
    
    // Get rating summary
    $stmt = $pdo->prepare("
        SELECT 
            AVG(rating) as avg_rating,
            COUNT(*) as total_reviews,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
        FROM product_reviews 
        WHERE product_id = ? AND is_approved = 1
    ");
    $stmt->execute([$product_id]);
    $rating_summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'reviews' => $reviews,
            'rating_summary' => $rating_summary,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]
    ]);
}

function getReviewableItems($order_id, $user_id) {
    global $pdo;
    
    // Verify the order belongs to the user
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = 'delivered'");
    $stmt->execute([$order_id, $user_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Order not found or not eligible for reviews');
    }
    
    // Get order items with existing reviews
    $stmt = $pdo->prepare("
        SELECT 
            oi.product_id,
            p.name as product_name,
            p.description,
            pi.image_url as product_image,
            s.store_name,
            r.id as review_id,
            r.rating,
            r.title as review_title,
            r.review_text,
            r.created_at as review_date
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN sellers s ON p.seller_id = s.id
        LEFT JOIN product_reviews r ON (r.product_id = oi.product_id AND r.user_id = ? AND r.order_id = ?)
        WHERE oi.order_id = ?
        ORDER BY p.name
    ");
    $stmt->execute([$user_id, $order_id, $order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $items
    ]);
}

function getUserReviews($user_id) {
    global $pdo;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT r.*, p.name as product_name, p.description as product_description,
               pi.image_url as product_image, s.store_name
        FROM product_reviews r
        JOIN products p ON r.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN sellers s ON p.seller_id = s.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$user_id, $limit, $offset]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_reviews WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'data' => $reviews,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);
}

function submitReview($input, $user_id) {
    global $pdo;
    
    // Validate input
    $required_fields = ['product_id', 'order_id', 'rating', 'title'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("$field is required");
        }
    }
    
    $product_id = $input['product_id'];
    $order_id = $input['order_id'];
    $rating = (int)$input['rating'];
    $title = trim($input['title']);
    $review_text = isset($input['review_text']) ? trim($input['review_text']) : '';
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating must be between 1 and 5');
    }
    
    // Verify the user purchased this product in this order
    $stmt = $pdo->prepare("
        SELECT oi.id FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE oi.order_id = ? AND oi.product_id = ? AND o.user_id = ? AND o.status = 'delivered'
    ");
    $stmt->execute([$order_id, $product_id, $user_id]);
    if (!$stmt->fetch()) {
        throw new Exception('You can only review products from your delivered orders');
    }
    
    // Check if review already exists
    $stmt = $pdo->prepare("
        SELECT id FROM product_reviews 
        WHERE product_id = ? AND user_id = ? AND order_id = ?
    ");
    $stmt->execute([$product_id, $user_id, $order_id]);
    if ($stmt->fetch()) {
        throw new Exception('You have already reviewed this product from this order');
    }
    
    // Insert review
    $stmt = $pdo->prepare("
        INSERT INTO product_reviews (product_id, user_id, order_id, rating, title, review_text, is_verified_purchase, is_approved)
        VALUES (?, ?, ?, ?, ?, ?, 1, 1)
    ");
    $stmt->execute([$product_id, $user_id, $order_id, $rating, $title, $review_text]);
    
    $review_id = $pdo->lastInsertId();
    
    // Get the created review
    $stmt = $pdo->prepare("
        SELECT r.*, p.name as product_name 
        FROM product_reviews r
        JOIN products p ON r.product_id = p.id
        WHERE r.id = ?
    ");
    $stmt->execute([$review_id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Review submitted successfully',
        'data' => $review
    ]);
}

function updateReview($input, $user_id) {
    global $pdo;
    
    if (!isset($input['id'])) {
        throw new Exception('Review ID is required');
    }
    
    $review_id = $input['id'];
    $rating = isset($input['rating']) ? (int)$input['rating'] : null;
    $title = isset($input['title']) ? trim($input['title']) : null;
    $review_text = isset($input['review_text']) ? trim($input['review_text']) : null;
    
    // Verify the review belongs to the user
    $stmt = $pdo->prepare("SELECT id FROM product_reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $user_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Review not found');
    }
    
    // Build update query
    $updates = [];
    $params = [];
    
    if ($rating !== null) {
        if ($rating < 1 || $rating > 5) {
            throw new Exception('Rating must be between 1 and 5');
        }
        $updates[] = "rating = ?";
        $params[] = $rating;
    }
    
    if ($title !== null) {
        $updates[] = "title = ?";
        $params[] = $title;
    }
    
    if ($review_text !== null) {
        $updates[] = "review_text = ?";
        $params[] = $review_text;
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $params[] = $review_id;
    $stmt = $pdo->prepare("UPDATE product_reviews SET " . implode(', ', $updates) . " WHERE id = ?");
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Review updated successfully'
    ]);
}

function deleteReview($review_id, $user_id) {
    global $pdo;
    
    // Verify the review belongs to the user
    $stmt = $pdo->prepare("SELECT id FROM product_reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $user_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Review not found');
    }
    
    $stmt = $pdo->prepare("DELETE FROM product_reviews WHERE id = ?");
    $stmt->execute([$review_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Review deleted successfully'
    ]);
}
?>