<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
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
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        exit;
    }
    
    $sellerId = $_SESSION['seller_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    $ticketId = $input['ticket_id'] ?? '';
    $message = trim($input['message'] ?? '');
    $status = $input['status'] ?? '';
    $isInternal = $input['is_internal'] ?? false;
    
    if (!$ticketId || !$message) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ticket ID and message are required'
        ]);
        exit;
    }
    
    // Verify seller has access to this ticket
    $stmt = $pdo->prepare("
        SELECT DISTINCT st.id, st.status
        FROM support_tickets st
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
    
    if ($ticket['status'] === 'closed') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Cannot respond to closed ticket'
        ]);
        exit;
    }
    
    $pdo->beginTransaction();
    
    try {
        // Add the message
        $messageStmt = $pdo->prepare("
            INSERT INTO support_ticket_messages (ticket_id, sender_type, sender_id, message, is_internal, created_at)
            VALUES (?, 'agent', ?, ?, ?, NOW())
        ");
        $messageStmt->execute([$ticketId, $sellerId, $message, $isInternal ? 1 : 0]);
        $messageId = $pdo->lastInsertId();
        
        // Update ticket status if provided
        $updateFields = ['updated_at = NOW()'];
        $updateParams = [];
        
        if ($status && in_array($status, ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'])) {
            $updateFields[] = 'status = ?';
            $updateParams[] = $status;
            
            if ($status === 'resolved' || $status === 'closed') {
                $updateFields[] = 'resolved_at = NOW()';
            }
        }
        
        $updateParams[] = $ticketId;
        
        $updateStmt = $pdo->prepare("
            UPDATE support_tickets 
            SET " . implode(', ', $updateFields) . "
            WHERE id = ?
        ");
        $updateStmt->execute($updateParams);
        
        $pdo->commit();
        
        // Get the created message details
        $messageDetailsStmt = $pdo->prepare("
            SELECT 
                id,
                message,
                sender_type,
                sender_id,
                is_internal,
                created_at
            FROM support_ticket_messages
            WHERE id = ?
        ");
        $messageDetailsStmt->execute([$messageId]);
        $messageDetails = $messageDetailsStmt->fetch(PDO::FETCH_ASSOC);
        
        $messageDetails['created_at'] = date('Y-m-d H:i:s', strtotime($messageDetails['created_at']));
        $messageDetails['is_internal'] = (bool)$messageDetails['is_internal'];
        $messageDetails['sender_name'] = 'Support Agent';
        
        echo json_encode([
            'success' => true,
            'message' => 'Response added successfully',
            'data' => [
                'message_id' => $messageId,
                'message_details' => $messageDetails
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add response'
    ]);
}
?>