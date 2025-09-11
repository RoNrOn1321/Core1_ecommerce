<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/product.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$productManager = new ProductManager($pdo);
$result = $productManager->getCategories();

if ($result['success']) {
    APIResponse::success($result['categories'], 'Categories retrieved successfully');
} else {
    APIResponse::error($result['message']);
}
?>