<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

require_once '../config/database.php';
require_once '../includes/auth_helper.php';

// Check if user is authenticated
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

$session_id = (int)($_GET['session_id'] ?? 0);

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
    
    // Get messages for the session
    $messages_stmt = $pdo->prepare("
        SELECT cm.*, 
               CASE 
                   WHEN cm.sender_type = 'customer' THEN 'You'
                   WHEN cm.sender_type = 'agent' THEN 'Support Agent'
                   WHEN cm.sender_type = 'bot' THEN 'Assistant'
                   ELSE 'System'
               END as sender_name
        FROM chat_messages cm
        WHERE cm.session_id = ?
        ORDER BY cm.created_at ASC
    ");
    $messages_stmt->execute([$session_id]);
    $messages = $messages_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update customer's last seen timestamp
    $update_stmt = $pdo->prepare("
        UPDATE chat_sessions 
        SET user_last_seen = NOW()
        WHERE id = ?
    ");
    $update_stmt->execute([$session_id]);
    
    echo json_encode([
        'success' => true,
        'session' => $session,
        'messages' => $messages
    ]);
    
} catch (PDOException $e) {
    error_log("Get chat messages error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>