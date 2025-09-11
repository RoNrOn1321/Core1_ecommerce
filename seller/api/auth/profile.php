<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get profile information
    $seller = $auth->getCurrentSeller();
    
    if ($seller) {
        // Remove sensitive information
        unset($seller['password_hash']);
        APIResponse::success($seller, 'Profile retrieved successfully');
    } else {
        APIResponse::error('Profile not found', 404);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update profile information
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        $validation = RequestValidator::validate($input, [
            'first_name' => ['required' => true, 'min' => 2, 'max' => 50],
            'last_name' => ['required' => true, 'min' => 2, 'max' => 50],
            'phone' => ['min' => 10, 'max' => 20]
        ]);

        if (!empty($validation)) {
            APIResponse::validation($validation);
        }

        $result = $auth->updateProfile($input);

        if ($result['success']) {
            APIResponse::success([], $result['message']);
        } else {
            APIResponse::error($result['message'], 400);
        }
    } catch (Exception $e) {
        error_log('Profile update error: ' . $e->getMessage());
        APIResponse::error('Profile update failed', 500);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>