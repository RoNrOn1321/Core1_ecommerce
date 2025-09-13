<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
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
    $result = $promotionManager->getPromotionStats($sellerId);
    
    if ($result['success']) {
        APIResponse::success($result['stats'], 'Promotion stats retrieved successfully');
    } else {
        APIResponse::error($result['message']);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>