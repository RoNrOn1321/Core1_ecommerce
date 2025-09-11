<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
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

$auth = new SellerAuth($pdo);
$auth->requireLogin();

if (!isset($_FILES['image'])) {
    APIResponse::error('No image file provided');
}

$file = $_FILES['image'];
$uploadDir = __DIR__ . '/../../../uploads/products/';

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    APIResponse::error('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
}

// Validate file size (10MB max)
if ($file['size'] > 10 * 1024 * 1024) {
    APIResponse::error('File too large. Maximum size is 10MB.');
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '_' . time() . '.' . $extension;
$filepath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    $imageUrl = '/Core1_ecommerce/uploads/products/' . $filename;
    
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'image_url' => $imageUrl,
        'filename' => $filename
    ]);
} else {
    APIResponse::error('Failed to upload image');
}
?>