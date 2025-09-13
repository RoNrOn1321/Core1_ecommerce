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

try {
    $user_id = getUserId();
    
    // Check if user already has an active chat session
    $existing_stmt = $pdo->prepare("
        SELECT id FROM chat_sessions 
        WHERE user_id = ? AND status IN ('waiting', 'active')
        LIMIT 1
    ");
    $existing_stmt->execute([$user_id]);
    $existing_session = $existing_stmt->fetch();
    
    if ($existing_session) {
        echo json_encode([
            'success' => true,
            'session_id' => $existing_session['id'],
            'message' => 'Existing chat session found'
        ]);
        exit;
    }
    
    // Create new chat session
    $create_stmt = $pdo->prepare("
        INSERT INTO chat_sessions (user_id, status, started_at) 
        VALUES (?, 'waiting', NOW())
    ");
    $create_stmt->execute([$user_id]);
    $session_id = $pdo->lastInsertId();
    
    // Add welcome system message
    $welcome_message = "Welcome to Lumino Support! You are now in the queue. An agent will be with you shortly.";
    $message_stmt = $pdo->prepare("
        INSERT INTO chat_messages (session_id, sender_type, message, message_type) 
        VALUES (?, 'bot', ?, 'system')
    ");
    $message_stmt->execute([$session_id, $welcome_message]);
    
    echo json_encode([
        'success' => true,
        'session_id' => $session_id,
        'message' => 'Chat session created successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Create chat session error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>