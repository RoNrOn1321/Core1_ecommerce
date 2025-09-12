<?php
// Customer Authentication API
require_once __DIR__ . '/../auth/functions.php';

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request information
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove base path to get API endpoint
$basePath = '/Core1_ecommerce/customer/api';
$endpoint = str_replace($basePath, '', $requestPath);
$endpoint = trim($endpoint, '/');

// Split endpoint into parts
$endpointParts = explode('/', $endpoint);
$module = $endpointParts[0] ?? '';
$action = $endpointParts[1] ?? '';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $firstName = $input['first_name'] ?? '';
        $lastName = $input['last_name'] ?? '';
        $phone = $input['phone'] ?? null;
        
        // Validation
        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Required fields missing']);
            break;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            break;
        }
        
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            break;
        }
        
        $result = $customerAuth->register($email, $password, $firstName, $lastName, $phone);
        
        if ($result['success']) {
            http_response_code(201);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
        break;
        
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email and password required']);
            break;
        }
        
        $result = $customerAuth->login($email, $password);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(401);
        }
        
        echo json_encode($result);
        break;
        
    case 'logout':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $result = $customerAuth->logout();
        echo json_encode($result);
        break;
        
    case 'me':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        if (!$customerAuth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            break;
        }
        
        $customer = $customerAuth->getCurrentCustomer();
        
        if ($customer) {
            echo json_encode([
                'success' => true,
                'customer' => $customer
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to get customer data']);
        }
        break;
        
    case 'verify':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $token = $input['token'] ?? '';
        
        if (empty($token)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verification token required']);
            break;
        }
        
        $result = $customerAuth->verifyEmail($token);
        echo json_encode($result);
        break;
        
    case 'forgot-password':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $email = $input['email'] ?? '';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid email required']);
            break;
        }
        
        $result = $customerAuth->requestPasswordReset($email);
        echo json_encode($result);
        break;
        
    case 'reset-password':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $token = $input['token'] ?? '';
        $newPassword = $input['new_password'] ?? '';
        
        if (empty($token) || empty($newPassword)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Token and new password required']);
            break;
        }
        
        if (strlen($newPassword) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            break;
        }
        
        $result = $customerAuth->resetPassword($token, $newPassword);
        echo json_encode($result);
        break;
        
    case 'update-profile':
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['customer_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            break;
        }
        
        $userId = $_SESSION['customer_id'];
        $firstName = $input['first_name'] ?? '';
        $lastName = $input['last_name'] ?? '';
        $email = $input['email'] ?? '';
        $phone = $input['phone'] ?? null;
        
        // Basic validation
        if (empty($firstName) || empty($lastName) || empty($email)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required']);
            break;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            break;
        }
        
        try {
            require_once __DIR__ . '/../config/database.php';
            
            // Check if email is already taken by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email already taken']);
                break;
            }
            
            // Update user profile
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$firstName, $lastName, $email, $phone, $userId]);
            
            // Update session data
            $_SESSION['customer_email'] = $email;
            $_SESSION['customer_name'] = $firstName . ' ' . $lastName;
            
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
        break;
        
    case 'change-password':
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['customer_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            break;
        }
        
        $userId = $_SESSION['customer_id'];
        $currentPassword = $input['current_password'] ?? '';
        $newPassword = $input['new_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Current password and new password are required']);
            break;
        }
        
        if (strlen($newPassword) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
            break;
        }
        
        try {
            require_once __DIR__ . '/../config/database.php';
            
            // Get current password hash
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                break;
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to change password']);
        }
        break;
        
    case 'delete-account':
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['customer_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            break;
        }
        
        $userId = $_SESSION['customer_id'];
        
        try {
            require_once __DIR__ . '/../config/database.php';
            
            $pdo->beginTransaction();
            
            // Delete user addresses
            $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Delete cart items
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Delete customer sessions
            $stmt = $pdo->prepare("DELETE FROM customer_sessions WHERE customer_id = ?");
            $stmt->execute([$userId]);
            
            // Note: We don't delete orders for business records, but mark user as deleted
            $stmt = $pdo->prepare("UPDATE users SET status = 'deleted', email = CONCAT(email, '_deleted_', ?), deleted_at = NOW() WHERE id = ?");
            $stmt->execute([time(), $userId]);
            
            $pdo->commit();
            
            // Clear session
            session_unset();
            session_destroy();
            
            echo json_encode([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete account']);
        }
        break;
        
    case 'upload-profile-image':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['customer_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            break;
        }
        
        $userId = $_SESSION['customer_id'];
        
        // Check if file was uploaded
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
            break;
        }
        
        $file = $_FILES['profile_image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed']);
            break;
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
            break;
        }
        
        // Validate image dimensions (optional)
        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid image file']);
            break;
        }
        
        try {
            require_once __DIR__ . '/../config/database.php';
            
            // Get current profile image to delete old one
            $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $currentUser = $stmt->fetch();
            $oldImage = $currentUser['profile_image'];
            
            // Generate unique filename
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFilename = 'profile_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
            $uploadPath = __DIR__ . '/../../uploads/profiles/' . $newFilename;
            $webPath = '/Core1_ecommerce/uploads/profiles/' . $newFilename;
            
            // Create directory if it doesn't exist
            $uploadDir = dirname($uploadPath);
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Update database
                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$webPath, $userId]);
                
                // Delete old image if exists
                if ($oldImage && file_exists(__DIR__ . '/../../' . ltrim($oldImage, '/'))) {
                    unlink(__DIR__ . '/../../' . ltrim($oldImage, '/'));
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile image uploaded successfully',
                    'data' => [
                        'image_url' => $webPath
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile image']);
        }
        break;
        
    case 'delete-profile-image':
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['customer_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            break;
        }
        
        $userId = $_SESSION['customer_id'];
        
        try {
            require_once __DIR__ . '/../config/database.php';
            
            // Get current profile image
            $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if ($user && $user['profile_image']) {
                // Delete file
                $imagePath = __DIR__ . '/../../' . ltrim($user['profile_image'], '/');
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                // Update database
                $stmt = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
                $stmt->execute([$userId]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Profile image deleted successfully'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete profile image']);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Auth endpoint not found']);
}
?>