<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/product.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$productManager = new ProductManager($pdo);

$productId = $_GET['id'] ?? null;
if (!$productId) {
    APIResponse::error('Product ID is required');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get single product
    $result = $productManager->getProduct($sellerId, $productId);
    
    if ($result['success']) {
        APIResponse::success($result['product'], 'Product retrieved successfully');
    } else {
        APIResponse::error($result['message'], 404);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update product
    $input = json_decode(file_get_contents('php://input'), true);
    
    $result = $productManager->updateProduct($sellerId, $productId, $input);
    
    if ($result['success']) {
        APIResponse::success([], $result['message']);
    } else {
        APIResponse::error($result['message']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete product
    $result = $productManager->deleteProduct($sellerId, $productId);
    
    if ($result['success']) {
        APIResponse::success([], $result['message']);
    } else {
        APIResponse::error($result['message']);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>