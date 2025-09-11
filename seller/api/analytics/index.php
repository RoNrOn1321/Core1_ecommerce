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
require_once __DIR__ . '/../../includes/store.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$storeManager = new StoreManager($pdo);

$period = $_GET['period'] ?? '30';

// Validate period
$validPeriods = ['7', '30', '90', '365'];
if (!in_array($period, $validPeriods)) {
    $period = '30';
}

$result = $storeManager->getStoreAnalytics($sellerId, $period);

if ($result['success']) {
    APIResponse::success($result['analytics'], 'Analytics data retrieved successfully');
} else {
    APIResponse::error($result['message']);
}
?>