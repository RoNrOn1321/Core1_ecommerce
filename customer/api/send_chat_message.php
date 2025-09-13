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
$message = trim($input['message'] ?? '');

if ($session_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Session ID is required']);
    exit;
}

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Message cannot be empty']);
    exit;
}

if (strlen($message) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Message too long']);
    exit;
}

try {
    $user_id = getUserId();
    
    // Verify session belongs to the current user and is active
    $session_stmt = $pdo->prepare("
        SELECT * FROM chat_sessions 
        WHERE id = ? AND user_id = ? AND status IN ('waiting', 'active')
    ");
    $session_stmt->execute([$session_id, $user_id]);
    $session = $session_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Session not found, access denied, or session ended']);
        exit;
    }
    
    // Insert the message
    $message_stmt = $pdo->prepare("
        INSERT INTO chat_messages (session_id, sender_type, sender_id, message) 
        VALUES (?, 'customer', ?, ?)
    ");
    $message_stmt->execute([$session_id, $user_id, $message]);
    
    // Update session's last activity
    $update_stmt = $pdo->prepare("
        UPDATE chat_sessions 
        SET user_last_seen = NOW()
        WHERE id = ?
    ");
    $update_stmt->execute([$session_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Send chat message error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>