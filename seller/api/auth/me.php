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

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$seller = $auth->getCurrentSeller();

if ($seller) {
    unset($seller['password_hash']); // Remove sensitive data
    APIResponse::success($seller, 'Seller information retrieved');
} else {
    APIResponse::error('Seller not found', 404);
}
?>