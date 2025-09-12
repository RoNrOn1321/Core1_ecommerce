<?php
// Admin Support Reply API - Creates notifications for customers
require_once '../config/auth.php';
require_once '../config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_support')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit();
}

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Validate input
    if (empty($input['ticket_id']) || empty($input['message'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ticket ID and message are required']);
        exit;
    }
    
    $ticketId = (int)$input['ticket_id'];
    $message = trim($input['message']);
    $agentId = getAdminId();
    
    if (strlen($message) > 5000) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Message is too long (max 5000 characters)']);
        exit;
    }
    
    // Get ticket details and verify it exists
    $stmt = $pdo->prepare("
        SELECT st.id, st.user_id, st.ticket_number, st.subject, st.status,
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email
        FROM support_tickets st 
        LEFT JOIN users u ON st.user_id = u.id 
        WHERE st.id = ?
    ");
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Ticket not found']);
        exit;
    }
    
    if ($ticket['status'] === 'closed') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cannot reply to a closed ticket']);
        exit;
    }
    
    $pdo->beginTransaction();
    
    // Insert the agent reply message
    $stmt = $pdo->prepare("
        INSERT INTO support_ticket_messages (
            ticket_id, sender_type, sender_id, message, created_at
        ) VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $ticketId,
        'agent',
        $agentId,
        $message
    ]);
    
    $messageId = $pdo->lastInsertId();
    
    // Update ticket status to waiting_customer and timestamp
    $stmt = $pdo->prepare("UPDATE support_tickets SET status = 'waiting_customer', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$ticketId]);
    
    // Create notification for the customer using the new notification system
    require_once '../../customer/includes/NotificationHelper.php';
    $notificationHelper = new NotificationHelper($pdo);
    
    $agentName = getAdminName() ?: 'Support Agent';
    $success = $notificationHelper->supportTicketReply(
        $ticket['user_id'], 
        $ticketId, 
        $ticket['ticket_number'], 
        $agentName
    );
    
    // Log activity
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (
            user_type, user_id, action, resource_type, resource_id, description, ip_address
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        'admin', 
        $agentId, 
        'support_reply_sent', 
        'support_ticket', 
        $ticketId, 
        "Agent reply sent to ticket #{$ticket['ticket_number']}", 
        $_SERVER['REMOTE_ADDR']
    ]);
    
    $pdo->commit();
    
    // Get the created message details for response
    $stmt = $pdo->prepare("
        SELECT stm.*, 
               'Support Agent' as sender_name
        FROM support_ticket_messages stm
        WHERE stm.id = ?
    ");
    $stmt->execute([$messageId]);
    $messageData = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'Reply sent successfully and notification created',
        'data' => [
            'message_id' => $messageId,
            'ticket_id' => $ticketId,
            'message' => $messageData['message'],
            'sender_name' => $messageData['sender_name'],
            'created_at' => $messageData['created_at'],
            'ticket_status' => 'waiting_customer',
            'notification_created' => $success
        ]
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Support reply error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>