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

try {
    // Get active chat sessions
    $stmt = $pdo->query("
        SELECT cs.*, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email,
               u.id as customer_id,
               (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.id AND cm.sender_type = 'customer' AND cm.created_at > COALESCE(cs.agent_last_seen, '1970-01-01')) as unread_count,
               (SELECT message FROM chat_messages WHERE session_id = cs.id ORDER BY created_at DESC LIMIT 1) as last_message,
               (SELECT created_at FROM chat_messages WHERE session_id = cs.id ORDER BY created_at DESC LIMIT 1) as last_message_time
        FROM chat_sessions cs
        LEFT JOIN users u ON cs.user_id = u.id
        WHERE cs.status IN ('waiting', 'active')
        ORDER BY 
            CASE WHEN cs.status = 'waiting' THEN 1 ELSE 2 END,
            cs.started_at ASC
    ");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'sessions' => $sessions
    ]);
    
} catch (PDOException $e) {
    error_log("Chat sessions error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>