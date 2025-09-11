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

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';

$input = json_decode(file_get_contents('php://input'), true);

RequestValidator::validateAndRespond($input, [
    'email' => ['required' => true, 'email' => true],
    'password' => ['required' => true, 'min' => 6]
]);

$auth = new SellerAuth($pdo);
$result = $auth->login($input['email'], $input['password']);

if ($result['success']) {
    APIResponse::success($result['seller'], 'Login successful');
} else {
    APIResponse::error($result['message'], 401);
}
?>