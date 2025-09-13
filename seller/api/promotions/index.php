<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/promotion.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$promotionManager = new PromotionManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get promotions with filters
    $filters = [
        'status' => $_GET['status'] ?? null,
        'type' => $_GET['type'] ?? null,
        'search' => $_GET['search'] ?? null,
        'limit' => $_GET['limit'] ?? 20,
        'offset' => $_GET['offset'] ?? 0
    ];
    
    $result = $promotionManager->getPromotions($sellerId, $filters);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Promotions retrieved successfully',
            'promotions' => $result['promotions'],
            'total' => $result['total'],
            'limit' => $result['limit'],
            'offset' => $result['offset']
        ]);
    } else {
        APIResponse::error($result['message']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new promotion
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        APIResponse::error('Invalid JSON input');
    }
    
    $result = $promotionManager->createPromotion($sellerId, $input);
    
    if ($result['success']) {
        APIResponse::success(['promotion_id' => $result['promotion_id']], $result['message']);
    } else {
        APIResponse::error($result['message']);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>