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

    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    $validation = RequestValidator::validate($input, [
        'current_password' => ['required' => true, 'min' => 1],
        'new_password' => ['required' => true, 'min' => 6],
        'confirm_password' => ['required' => true]
    ]);

    if (!empty($validation)) {
        APIResponse::validation($validation);
    }

    // Check if passwords match
    if ($input['new_password'] !== $input['confirm_password']) {
        APIResponse::error('New passwords do not match', 400);
    }

    $auth = new SellerAuth($pdo);
    $result = $auth->changePassword($input['current_password'], $input['new_password']);

    if ($result['success']) {
        APIResponse::success([], $result['message']);
    } else {
        APIResponse::error($result['message'], 400);
    }
} catch (Exception $e) {
    error_log('Change password error: ' . $e->getMessage());
    APIResponse::error('Password change failed', 500);
}
?>