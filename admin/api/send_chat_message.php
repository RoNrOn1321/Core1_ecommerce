<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

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
    // Verify session exists and is active
    $session_stmt = $pdo->prepare("
        SELECT cs.*, u.id as customer_id 
        FROM chat_sessions cs
        LEFT JOIN users u ON cs.user_id = u.id
        WHERE cs.id = ? AND cs.status IN ('waiting', 'active')
    ");
    $session_stmt->execute([$session_id]);
    $session = $session_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$session) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Session not found or inactive']);
        exit;
    }
    
    $admin_id = getAdminId();
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Insert the message
    $message_stmt = $pdo->prepare("
        INSERT INTO chat_messages (session_id, sender_type, sender_id, message) 
        VALUES (?, 'agent', ?, ?)
    ");
    $message_stmt->execute([$session_id, $admin_id, $message]);
    
    // Update session status to active and assign agent
    $update_stmt = $pdo->prepare("
        UPDATE chat_sessions 
        SET status = 'active', 
            agent_id = ?,
            agent_last_seen = NOW()
        WHERE id = ?
    ");
    $update_stmt->execute([$admin_id, $session_id]);
    
    // Create notification for customer if available
    try {
        if (file_exists('../../customer/includes/NotificationHelper.php')) {
            require_once '../../customer/includes/NotificationHelper.php';
            $notificationHelper = new NotificationHelper($pdo);
            $agentName = getAdminName() ?: 'Support Agent';
            
            // Check if method exists before calling
            if (method_exists($notificationHelper, 'chatMessage')) {
                $notificationHelper->chatMessage(
                    $session['customer_id'],
                    $session_id,
                    $agentName
                );
            }
        }
    } catch (Exception $e) {
        // Log notification error but don't fail the main operation
        error_log("Notification error in send_chat_message: " . $e->getMessage());
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Send chat message error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}
?>