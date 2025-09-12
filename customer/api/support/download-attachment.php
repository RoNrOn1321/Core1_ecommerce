<?php
// Customer Support Attachment Download
require_once __DIR__ . '/../../auth/functions.php';

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$customerId = $_SESSION['customer_id'];

// Get attachment ID from query parameter
$attachmentId = $_GET['id'] ?? null;

if (!$attachmentId || !is_numeric($attachmentId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Valid attachment ID is required']);
    exit;
}

try {
    // Get attachment details and verify customer has access
    $stmt = $pdo->prepare("
        SELECT sta.*, st.user_id, st.ticket_number
        FROM support_ticket_attachments sta
        INNER JOIN support_tickets st ON sta.ticket_id = st.id
        WHERE sta.id = ? AND st.user_id = ?
    ");
    
    $stmt->execute([$attachmentId, $customerId]);
    $attachment = $stmt->fetch();
    
    if (!$attachment) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Attachment not found or access denied']);
        exit;
    }
    
    // Build file path
    $filePath = __DIR__ . '/../../../' . $attachment['file_path'];
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'File not found on server']);
        exit;
    }
    
    // Get file info
    $fileName = $attachment['original_filename'];
    $fileSize = filesize($filePath);
    $mimeType = $attachment['mime_type'];
    
    // Set appropriate headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . $fileSize);
    header('Content-Disposition: inline; filename="' . $fileName . '"');
    header('Cache-Control: private, max-age=3600');
    header('X-Ticket-Number: ' . $attachment['ticket_number']);
    
    // For images, allow inline display
    if (strpos($mimeType, 'image/') === 0) {
        header('Content-Disposition: inline; filename="' . $fileName . '"');
    } else {
        // For other files, suggest download
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }
    
    // Output file
    readfile($filePath);
    
} catch (Exception $e) {
    error_log("Attachment download error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to download attachment']);
}

exit;
?>