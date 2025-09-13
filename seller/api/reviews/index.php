<?php
require_once '../../../config/database.php';
require_once '../../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check authentication
if (!isSellerLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

$sellerId = $_SESSION['seller_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetReviews($pdo, $sellerId);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            handleUpdateReview($pdo, $sellerId, $input);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGetReviews($pdo, $sellerId) {
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = max(1, min(50, intval($_GET['limit'] ?? 10)));
    $status = $_GET['status'] ?? '';
    $rating = $_GET['rating'] ?? '';
    $productId = $_GET['product_id'] ?? '';
    
    $offset = ($page - 1) * $limit;
    
    // Build WHERE conditions
    $where = ["p.seller_id = ?"];
    $params = [$sellerId];
    
    if ($status === 'pending') {
        $where[] = "pr.is_approved = 0";
    } elseif ($status === 'approved') {
        $where[] = "pr.is_approved = 1";
    }
    
    if ($rating && in_array($rating, ['1', '2', '3', '4', '5'])) {
        $where[] = "pr.rating = ?";
        $params[] = $rating;
    }
    
    if ($productId && is_numeric($productId)) {
        $where[] = "pr.product_id = ?";
        $params[] = $productId;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Get total count
    $countSql = "
        SELECT COUNT(*) as total
        FROM product_reviews pr
        JOIN products p ON pr.product_id = p.id
        WHERE $whereClause
    ";
    
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Get reviews
    $sql = "
        SELECT 
            pr.*,
            p.name as product_name,
            p.id as product_id,
            u.first_name,
            u.last_name,
            u.email,
            o.order_number
        FROM product_reviews pr
        JOIN products p ON pr.product_id = p.id
        JOIN users u ON pr.user_id = u.id
        LEFT JOIN orders o ON pr.order_id = o.id
        WHERE $whereClause
        ORDER BY pr.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format reviews
    foreach ($reviews as &$review) {
        $review['rating'] = intval($review['rating']);
        $review['is_approved'] = intval($review['is_approved']);
        $review['is_verified_purchase'] = intval($review['is_verified_purchase']);
        $review['helpful_count'] = intval($review['helpful_count']);
        $review['customer_name'] = $review['first_name'] . ' ' . $review['last_name'];
        
        // Remove sensitive data
        unset($review['first_name'], $review['last_name'], $review['email']);
    }
    
    $totalPages = ceil($total / $limit);
    
    echo json_encode([
        'success' => true,
        'data' => $reviews,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => intval($total),
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ]
    ]);
}

function handleUpdateReview($pdo, $sellerId, $input) {
    if (!isset($input['review_id']) || !isset($input['action'])) {
        throw new Exception('Review ID and action are required');
    }
    
    $reviewId = $input['review_id'];
    $action = $input['action'];
    
    // Verify the review belongs to this seller's product
    $stmt = $pdo->prepare("
        SELECT pr.id
        FROM product_reviews pr
        JOIN products p ON pr.product_id = p.id
        WHERE pr.id = ? AND p.seller_id = ?
    ");
    $stmt->execute([$reviewId, $sellerId]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Review not found or access denied');
    }
    
    switch ($action) {
        case 'approve':
            $stmt = $pdo->prepare("UPDATE product_reviews SET is_approved = 1 WHERE id = ?");
            $stmt->execute([$reviewId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Review approved successfully'
            ]);
            break;
            
        case 'reject':
            $stmt = $pdo->prepare("UPDATE product_reviews SET is_approved = 0 WHERE id = ?");
            $stmt->execute([$reviewId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Review rejected successfully'
            ]);
            break;
            
        case 'respond':
            if (!isset($input['response'])) {
                throw new Exception('Response text is required');
            }
            
            $response = trim($input['response']);
            if (empty($response)) {
                throw new Exception('Response cannot be empty');
            }
            
            // For now, just store as a simple response field
            // In future, you could create a separate seller_responses table
            $stmt = $pdo->prepare("UPDATE product_reviews SET seller_response = ? WHERE id = ?");
            $stmt->execute([$response, $reviewId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Response added successfully'
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
}
?>