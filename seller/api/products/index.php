<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/product.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$productManager = new ProductManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get products with filters
    $filters = [
        'category_id' => $_GET['category_id'] ?? null,
        'status' => $_GET['status'] ?? null,
        'search' => $_GET['search'] ?? null,
        'limit' => $_GET['limit'] ?? 20,
        'offset' => $_GET['offset'] ?? 0
    ];
    
    $result = $productManager->getProducts($sellerId, $filters);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'products' => $result['products'],
            'total' => $result['total'],
            'limit' => $result['limit'],
            'offset' => $result['offset']
        ]);
    } else {
        APIResponse::error($result['message']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new product
    $input = json_decode(file_get_contents('php://input'), true);
    
    RequestValidator::validateAndRespond($input, [
        'name' => ['required' => true, 'min' => 3, 'max' => 255],
        'price' => ['required' => true, 'numeric' => true]
    ]);
    
    $result = $productManager->createProduct($sellerId, $input);
    
    if ($result['success']) {
        APIResponse::success(['product_id' => $result['product_id']], $result['message']);
    } else {
        APIResponse::error($result['message']);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>