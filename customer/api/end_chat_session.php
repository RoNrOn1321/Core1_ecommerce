<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';
require_once '../includes/auth_helper.php';

// Check if user is authenticated
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$session_id = (int)($input['session_id'] ?? 0);

if ($session_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Session ID is required']);
    exit;
}

try {
    $user_id = getUserId();
    
    // Verify session belongs to the current user
    $session_stmt = $pdo->prepare("
        SELECT * FROM chat_sessions 
        WHERE id = ? AND user_id = ?
    ");
    $session_stmt->execute([$session_id, $user_id]);
    $session = $session_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Session not found or access denied']);
        exit;
    }
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Add system message indicating chat ended
    $system_message = "Chat session ended by customer.";
    $message_stmt = $pdo->prepare("
        INSERT INTO chat_messages (session_id, sender_type, sender_id, message, message_type) 
        VALUES (?, 'customer', ?, ?, 'system')
    ");
    $message_stmt->execute([$session_id, $user_id, $system_message]);
    
    // Update session status to ended
    $update_stmt = $pdo->prepare("
        UPDATE chat_sessions 
        SET status = 'ended', 
            ended_at = NOW(),
            user_last_seen = NOW()
        WHERE id = ?
    ");
    $update_stmt->execute([$session_id]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Chat session ended successfully'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("End chat session error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>