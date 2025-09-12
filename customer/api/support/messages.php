<?php
// Customer Support Messages API
require_once __DIR__ . '/../../auth/functions.php';

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request information
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove base path to get API endpoint
$basePath = '/Core1_ecommerce/customer/api';
$endpoint = str_replace($basePath, '', $requestPath);
$endpoint = trim($endpoint, '/');

// Split endpoint into parts
$endpointParts = explode('/', $endpoint);
$module = $endpointParts[0] ?? ''; // support
$resource = $endpointParts[1] ?? ''; // messages
$ticketId = $endpointParts[2] ?? null; // ticket ID

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Initialize auth system
$auth = new CustomerAuth($pdo);

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$customerId = $_SESSION['customer_id'];

try {
    switch ($requestMethod) {
        case 'GET':
            // Get messages for a specific ticket
            if ($ticketId) {
                getTicketMessages($pdo, $customerId, $ticketId);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ticket ID required']);
            }
            break;
            
        case 'POST':
            // Send reply to a specific ticket
            if ($ticketId) {
                sendTicketReply($pdo, $customerId, $ticketId, $input);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ticket ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}

function getTicketMessages($pdo, $customerId, $ticketId) {
    try {
        // First verify the ticket belongs to the customer
        $stmt = $pdo->prepare("SELECT id, status FROM support_tickets WHERE id = ? AND user_id = ?");
        $stmt->execute([$ticketId, $customerId]);
        $ticket = $stmt->fetch();
        
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            return;
        }
        
        // Get messages for the ticket
        $stmt = $pdo->prepare("
            SELECT stm.*, 
                   CASE 
                       WHEN stm.sender_type = 'customer' THEN CONCAT(u.first_name, ' ', u.last_name)
                       WHEN stm.sender_type = 'agent' THEN 'Support Agent'
                       ELSE 'System'
                   END as sender_name,
                   CASE 
                       WHEN stm.sender_type = 'customer' THEN u.email
                       ELSE NULL
                   END as sender_email
            FROM support_ticket_messages stm
            LEFT JOIN users u ON stm.sender_type = 'customer' AND stm.sender_id = u.id
            WHERE stm.ticket_id = ?
            ORDER BY stm.created_at ASC
        ");
        
        $stmt->execute([$ticketId]);
        $messages = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'ticket_id' => $ticketId,
                'ticket_status' => $ticket['status'],
                'messages' => $messages,
                'message_count' => count($messages)
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function sendTicketReply($pdo, $customerId, $ticketId, $data) {
    try {
        // Validate input
        if (empty($data['message'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message content is required']);
            return;
        }
        
        $message = trim($data['message']);
        if (strlen($message) > 5000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message is too long (max 5000 characters)']);
            return;
        }
        
        // Verify the ticket belongs to the customer and check status
        $stmt = $pdo->prepare("SELECT id, status FROM support_tickets WHERE id = ? AND user_id = ?");
        $stmt->execute([$ticketId, $customerId]);
        $ticket = $stmt->fetch();
        
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            return;
        }
        
        if ($ticket['status'] === 'closed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot reply to a closed ticket']);
            return;
        }
        
        $pdo->beginTransaction();
        
        // Insert the message
        $stmt = $pdo->prepare("
            INSERT INTO support_ticket_messages (
                ticket_id, sender_type, sender_id, message, created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $ticketId,
            'customer',
            $customerId,
            $message
        ]);
        
        $messageId = $pdo->lastInsertId();
        
        // Update ticket status if it was resolved (customer replied, so set back to waiting_customer)
        $newStatus = 'waiting_customer';
        if ($ticket['status'] === 'open') {
            $newStatus = 'in_progress';
        } elseif ($ticket['status'] === 'resolved') {
            $newStatus = 'waiting_customer';
        } elseif ($ticket['status'] === 'in_progress' || $ticket['status'] === 'waiting_customer') {
            $newStatus = $ticket['status']; // Keep current status
        }
        
        // Update ticket timestamp and status if needed
        $stmt = $pdo->prepare("UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newStatus, $ticketId]);
        
        $pdo->commit();
        
        // Get the created message details
        $stmt = $pdo->prepare("
            SELECT stm.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as sender_name,
                   u.email as sender_email
            FROM support_ticket_messages stm
            LEFT JOIN users u ON stm.sender_id = u.id
            WHERE stm.id = ?
        ");
        $stmt->execute([$messageId]);
        $messageData = $stmt->fetch();
        
        // Create notification for customer reply (update existing notification system)
        try {
            require_once __DIR__ . '/../../includes/NotificationHelper.php';
            $notificationHelper = new NotificationHelper($pdo);
            
            // Get ticket details for notification
            $ticketStmt = $pdo->prepare("SELECT ticket_number FROM support_tickets WHERE id = ?");
            $ticketStmt->execute([$ticketId]);
            $ticketData = $ticketStmt->fetch();
            
            if ($ticketData) {
                // This is a customer reply, so we don't send notification to the customer
                // Notifications for customer replies would be sent to agents (not implemented in this scope)
            }
        } catch (Exception $e) {
            // Log error but don't fail the reply
            error_log("Failed to create notification for support reply: " . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Reply sent successfully',
            'data' => [
                'message_id' => $messageId,
                'ticket_id' => $ticketId,
                'message' => $messageData['message'],
                'sender_name' => $messageData['sender_name'],
                'created_at' => $messageData['created_at'],
                'ticket_status' => $newStatus
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

?>