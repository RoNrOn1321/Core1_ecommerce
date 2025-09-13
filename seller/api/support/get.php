<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $auth = new SellerAuth($pdo);
    
    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Authentication required'
        ]);
        exit;
    }
    
    $sellerId = $_SESSION['seller_id'];
    $ticketId = $_GET['id'] ?? '';
    
    if (!$ticketId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ticket ID is required'
        ]);
        exit;
    }
    
    // Get ticket details with user info
    $stmt = $pdo->prepare("
        SELECT DISTINCT
            st.id,
            st.ticket_number,
            st.subject,
            st.category,
            st.priority,
            st.status,
            st.order_id,
            st.created_at,
            st.updated_at,
            st.resolved_at,
            u.id as user_id,
            u.first_name,
            u.last_name,
            u.email,
            o.order_number,
            o.total_amount
        FROM support_tickets st
        JOIN users u ON st.user_id = u.id
        LEFT JOIN orders o ON st.order_id = o.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE st.id = ? AND (oi.seller_id = ? OR st.order_id IS NULL)
        LIMIT 1
    ");
    $stmt->execute([$ticketId, $sellerId]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Ticket not found or access denied'
        ]);
        exit;
    }
    
    // Get ticket messages
    $messagesStmt = $pdo->prepare("
        SELECT 
            stm.id,
            stm.message,
            stm.sender_type,
            stm.sender_id,
            stm.attachments,
            stm.is_internal,
            stm.created_at,
            CASE 
                WHEN stm.sender_type = 'customer' THEN u.first_name
                WHEN stm.sender_type = 'agent' THEN 'Support Agent'
                ELSE 'System'
            END as sender_name,
            CASE 
                WHEN stm.sender_type = 'customer' THEN u.email
                ELSE NULL
            END as sender_email
        FROM support_ticket_messages stm
        LEFT JOIN users u ON stm.sender_id = u.id AND stm.sender_type = 'customer'
        WHERE stm.ticket_id = ?
        ORDER BY stm.created_at ASC
    ");
    $messagesStmt->execute([$ticketId]);
    $messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    $ticket['customer_name'] = trim($ticket['first_name'] . ' ' . $ticket['last_name']);
    $ticket['created_at'] = date('Y-m-d H:i:s', strtotime($ticket['created_at']));
    $ticket['updated_at'] = date('Y-m-d H:i:s', strtotime($ticket['updated_at']));
    $ticket['resolved_at'] = $ticket['resolved_at'] ? date('Y-m-d H:i:s', strtotime($ticket['resolved_at'])) : null;
    
    foreach ($messages as &$message) {
        $message['created_at'] = date('Y-m-d H:i:s', strtotime($message['created_at']));
        $message['attachments'] = $message['attachments'] ? json_decode($message['attachments'], true) : [];
        $message['is_internal'] = (bool)$message['is_internal'];
    }
    
    $ticket['messages'] = $messages;
    $ticket['message_count'] = count($messages);
    
    echo json_encode([
        'success' => true,
        'data' => $ticket
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch ticket details'
    ]);
}
?>