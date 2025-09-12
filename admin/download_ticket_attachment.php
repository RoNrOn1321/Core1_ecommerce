<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_support')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit();
}

// Get attachment ID from query parameter
$attachmentId = $_GET['id'] ?? null;

if (!$attachmentId || !is_numeric($attachmentId)) {
    http_response_code(400);
    echo 'Invalid attachment ID';
    exit();
}

try {
    // Get attachment details (admin can access all attachments)
    $stmt = $pdo->prepare("
        SELECT sta.*, st.ticket_number
        FROM support_ticket_attachments sta
        INNER JOIN support_tickets st ON sta.ticket_id = st.id
        WHERE sta.id = ?
    ");
    
    $stmt->execute([$attachmentId]);
    $attachment = $stmt->fetch();
    
    if (!$attachment) {
        http_response_code(404);
        echo 'Attachment not found';
        exit();
    }
    
    // Build file path
    $filePath = __DIR__ . '/../' . $attachment['file_path'];
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo 'File not found on server';
        exit();
    }
    
    // Get file info
    $fileName = $attachment['original_filename'];
    $fileSize = filesize($filePath);
    $mimeType = $attachment['mime_type'];
    
    // Set appropriate headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . $fileSize);
    header('Cache-Control: private, max-age=3600');
    header('X-Ticket-Number: ' . $attachment['ticket_number']);
    
    // For images, allow inline display
    if (strpos($mimeType, 'image/') === 0) {
        header('Content-Disposition: inline; filename="' . $fileName . '"');
    } else {
        // For other files, suggest download
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }
    
    // Log admin access (optional)
    error_log("Admin " . getAdminId() . " accessed attachment " . $attachmentId . " from ticket " . $attachment['ticket_number']);
    
    // Output file
    readfile($filePath);
    
} catch (Exception $e) {
    error_log("Admin attachment download error: " . $e->getMessage());
    http_response_code(500);
    echo 'Failed to download attachment';
}

exit;
?>