<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/response.php';

    $auth = new SellerAuth($pdo);
    $result = $auth->logout();

    APIResponse::success([], $result['message']);
} catch (Exception $e) {
    error_log('Logout error: ' . $e->getMessage());
    APIResponse::error('Logout failed', 500);
}
?>