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

if ($session_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Session ID is required']);
    exit;
}

try {
    // Verify session exists
    $session_stmt = $pdo->prepare("
        SELECT cs.*, u.id as customer_id 
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
    
    $admin_id = getAdminId();
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Add system message indicating chat ended
    $system_message = "Chat session ended by support agent.";
    $message_stmt = $pdo->prepare("
        INSERT INTO chat_messages (session_id, sender_type, sender_id, message, message_type) 
        VALUES (?, 'agent', ?, ?, 'system')
    ");
    $message_stmt->execute([$session_id, $admin_id, $system_message]);
    
    // Update session status to ended
    $update_stmt = $pdo->prepare("
        UPDATE chat_sessions 
        SET status = 'ended', 
            ended_at = NOW(),
            agent_id = ?
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
            if (method_exists($notificationHelper, 'chatSessionEnded')) {
                $notificationHelper->chatSessionEnded(
                    $session['customer_id'],
                    $session_id,
                    $agentName
                );
            }
        }
    } catch (Exception $e) {
        // Log notification error but don't fail the main operation
        error_log("Notification error in end_chat_session: " . $e->getMessage());
    }
    
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