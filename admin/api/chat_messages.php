<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

require_once '../config/auth.php';
require_once '../config/database.php';

// Require authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

// Check permissions
if (!hasPermission('manage_support')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Insufficient permissions']);
    exit;
}

$session_id = (int)($_GET['session_id'] ?? 0);

if ($session_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Session ID is required']);
    exit;
}

try {
    // Get session details
    $session_stmt = $pdo->prepare("
        SELECT cs.*, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email
        FROM chat_sessions cs
        LEFT JOIN users u ON cs.user_id = u.id
        WHERE cs.id = ?
    ");
    $session_stmt->execute([$session_id]);
    $session = $session_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Session not found']);
        exit;
    }
    
    // Get messages for the session
    $messages_stmt = $pdo->prepare("
        SELECT cm.*, 
               CASE 
                   WHEN cm.sender_type = 'customer' THEN CONCAT(u.first_name, ' ', u.last_name)
                   WHEN cm.sender_type = 'agent' THEN 'Support Agent'
                   ELSE 'System'
               END as sender_name
        FROM chat_messages cm
        LEFT JOIN users u ON cm.sender_id = u.id AND cm.sender_type = 'customer'
        WHERE cm.session_id = ?
        ORDER BY cm.created_at ASC
    ");
    $messages_stmt->execute([$session_id]);
    $messages = $messages_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update agent's last seen timestamp for this session
    $admin_id = getAdminId();
    if ($admin_id) {
        $update_stmt = $pdo->prepare("
            UPDATE chat_sessions 
            SET agent_last_seen = NOW(),
                agent_id = ? 
            WHERE id = ?
        ");
        $update_stmt->execute([$admin_id, $session_id]);
    }
    
    echo json_encode([
        'success' => true,
        'session' => $session,
        'messages' => $messages
    ]);
    
} catch (PDOException $e) {
    error_log("Chat messages error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>