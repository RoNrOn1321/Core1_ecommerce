<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/promotion.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$promotionManager = new PromotionManager($pdo);

// Get promotion ID from URL path
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$pathParts = explode('/', trim($pathInfo, '/'));
$promotionId = $pathParts[0] ?? null;

if (!$promotionId) {
    APIResponse::error('Promotion ID is required');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get specific promotion
    $filters = ['limit' => 1, 'offset' => 0];
    $result = $promotionManager->getPromotions($sellerId, $filters);
    
    if ($result['success']) {
        $promotion = null;
        foreach ($result['promotions'] as $p) {
            if ($p['id'] == $promotionId) {
                $promotion = $p;
                break;
            }
        }
        
        if ($promotion) {
            APIResponse::success($promotion, 'Promotion retrieved successfully');
        } else {
            APIResponse::error('Promotion not found', 404);
        }
    } else {
        APIResponse::error($result['message']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update promotion
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        APIResponse::error('Invalid JSON input');
    }
    
    $result = $promotionManager->updatePromotion($sellerId, $promotionId, $input);
    
    if ($result['success']) {
        APIResponse::success([], $result['message']);
    } else {
        APIResponse::error($result['message']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete promotion
    $result = $promotionManager->deletePromotion($sellerId, $promotionId);
    
    if ($result['success']) {
        APIResponse::success([], $result['message']);
    } else {
        APIResponse::error($result['message']);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>