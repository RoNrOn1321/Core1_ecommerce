<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
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
require_once __DIR__ . '/../../includes/order.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$orderManager = new OrderManager($pdo);

$filters = [
    'status' => $_GET['status'] ?? null,
    'payment_status' => $_GET['payment_status'] ?? null,
    'search' => $_GET['search'] ?? null,
    'date_from' => $_GET['date_from'] ?? null,
    'date_to' => $_GET['date_to'] ?? null,
    'limit' => $_GET['limit'] ?? 20,
    'offset' => $_GET['offset'] ?? 0
];

$result = $orderManager->getOrders($sellerId, $filters);

if ($result['success']) {
    APIResponse::success($result, 'Orders retrieved successfully');
} else {
    APIResponse::error($result['message']);
}
?>