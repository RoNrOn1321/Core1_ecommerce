<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order_id']) || !isset($input['status'])) {
    APIResponse::error('Order ID and status are required');
}

$orderId = (int)$input['order_id'];
$status = $input['status'];
$notes = $input['notes'] ?? '';

$result = $orderManager->updateOrderStatus($sellerId, $orderId, $status, $notes);

if ($result['success']) {
    APIResponse::success($result, 'Order status updated successfully');
} else {
    APIResponse::error($result['message']);
}
?>