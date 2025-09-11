<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// API Information endpoint
$api_info = [
    'name' => 'Core1 E-commerce Seller API',
    'version' => '1.0.0',
    'description' => 'Backend API for seller portal functionality',
    'endpoints' => [
        'authentication' => [
            'login' => 'POST /auth/login',
            'register' => 'POST /auth/register',
            'logout' => 'POST /auth/logout',
            'me' => 'GET /auth/me'
        ],
        'products' => [
            'list' => 'GET /products/',
            'create' => 'POST /products/',
            'detail' => 'GET /products/detail?id={id}',
            'update' => 'PUT /products/detail?id={id}',
            'delete' => 'DELETE /products/detail?id={id}',
            'categories' => 'GET /products/categories'
        ],
        'orders' => [
            'list' => 'GET /orders/',
            'detail' => 'GET /orders/detail?id={id}',
            'update' => 'PUT /orders/detail?id={id}',
            'stats' => 'GET /orders/stats'
        ],
        'store' => [
            'profile' => 'GET /store/profile',
            'update_profile' => 'PUT /store/profile',
            'dashboard' => 'GET /store/dashboard'
        ],
        'analytics' => [
            'get' => 'GET /analytics/?period={days}'
        ]
    ],
    'base_url' => 'http://localhost/Core1_ecommerce/seller/api',
    'documentation' => 'See API_DOCUMENTATION.md for detailed usage'
];

echo json_encode([
    'success' => true,
    'message' => 'Core1 E-commerce Seller API',
    'data' => $api_info
], JSON_PRETTY_PRINT);
?>