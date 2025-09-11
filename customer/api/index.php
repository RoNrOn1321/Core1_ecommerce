<?php
// Customer API Entry Point
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

// Get request information
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove base path to get API endpoint
$basePath = '/Core1_ecommerce/customer/api';
$endpoint = str_replace($basePath, '', $requestPath);
$endpoint = trim($endpoint, '/');

// Split endpoint into parts
$endpointParts = explode('/', $endpoint);
$module = $endpointParts[0] ?? '';
$action = $endpointParts[1] ?? '';
$id = $endpointParts[2] ?? null;

// Route requests
try {
    switch ($module) {
        case '':
        case 'info':
            echo json_encode([
                'success' => true,
                'message' => 'Customer API v1.0',
                'timestamp' => date('Y-m-d H:i:s'),
                'endpoints' => [
                    'POST /auth/register' => 'Register customer account',
                    'POST /auth/login' => 'Customer login',
                    'POST /auth/logout' => 'Customer logout',
                    'GET /auth/me' => 'Get current customer info',
                    'GET /products' => 'Get products list',
                    'GET /products/{id}' => 'Get single product',
                    'POST /cart/add' => 'Add item to cart',
                    'GET /cart' => 'Get cart contents',
                    'PUT /cart/{id}' => 'Update cart item',
                    'DELETE /cart/{id}' => 'Remove cart item',
                    'GET /orders' => 'Get customer orders',
                    'POST /orders' => 'Create new order',
                    'GET /addresses' => 'Get customer addresses',
                    'POST /addresses' => 'Add new address',
                    'PUT /addresses/{id}' => 'Update address',
                    'DELETE /addresses/{id}' => 'Delete address'
                ]
            ]);
            break;
            
        case 'auth':
            require_once 'auth.php';
            break;
            
        case 'products':
            require_once 'products.php';
            break;
            
        case 'cart':
            require_once 'cart.php';
            break;
            
        case 'orders':
            require_once 'orders.php';
            break;
            
        case 'addresses':
            require_once 'addresses.php';
            break;
            
        case 'payment':
            require_once 'payment.php';
            break;
            
        case 'support':
            require_once 'support.php';
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Endpoint not found',
                'endpoint' => $endpoint
            ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>