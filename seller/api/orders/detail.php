<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/order.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$orderManager = new OrderManager($pdo);

$orderId = $_GET['id'] ?? null;
if (!$orderId) {
    APIResponse::error('Order ID is required');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get single order
    $result = $orderManager->getOrder($sellerId, $orderId);
    
    if ($result['success']) {
        APIResponse::success($result['order'], 'Order retrieved successfully');
    } else {
        APIResponse::error($result['message'], 404);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update order (status or tracking)
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['status'])) {
        // Update order status
        $result = $orderManager->updateOrderStatus(
            $sellerId, 
            $orderId, 
            $input['status'], 
            $input['notes'] ?? ''
        );
        
        if ($result['success']) {
            APIResponse::success([], $result['message']);
        } else {
            APIResponse::error($result['message']);
        }
        
    } elseif (isset($input['tracking_number']) || isset($input['courier_company']) || isset($input['estimated_delivery_date'])) {
        // Update tracking information
        $result = $orderManager->updateTrackingInfo($sellerId, $orderId, $input);
        
        if ($result['success']) {
            APIResponse::success([], $result['message']);
        } else {
            APIResponse::error($result['message']);
        }
        
    } else {
        APIResponse::error('No valid update data provided');
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>