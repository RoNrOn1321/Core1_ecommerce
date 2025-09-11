<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/store.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$storeManager = new StoreManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get store profile
    $result = $storeManager->getStoreProfile($sellerId);
    
    if ($result['success']) {
        APIResponse::success($result['store'], 'Store profile retrieved successfully');
    } else {
        APIResponse::error($result['message'], 404);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update store profile
    $input = json_decode(file_get_contents('php://input'), true);
    
    $result = $storeManager->updateStoreProfile($sellerId, $input);
    
    if ($result['success']) {
        APIResponse::success([], $result['message']);
    } else {
        APIResponse::error($result['message']);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>