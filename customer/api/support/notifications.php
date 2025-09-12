<?php
// Customer Support Notifications API
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
$resource = $endpointParts[1] ?? ''; // notifications
$action = $endpointParts[2] ?? ''; // unread

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
            if ($action === 'unread') {
                getUnreadNotifications($pdo, $customerId);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
            break;
            
        case 'POST':
            if ($action === 'mark-read') {
                markNotificationsAsRead($pdo, $customerId);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
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

function getUnreadNotifications($pdo, $customerId) {
    try {
        // Get tickets with unread messages from support agents
        $stmt = $pdo->prepare("
            SELECT st.id as ticket_id,
                   st.ticket_number,
                   st.subject,
                   st.status,
                   st.priority,
                   st.updated_at,
                   COUNT(stm.id) as unread_messages,
                   MAX(stm.created_at) as last_reply_at,
                   GROUP_CONCAT(
                       CASE WHEN stm.sender_type = 'agent' 
                       THEN SUBSTRING(stm.message, 1, 100)
                       END SEPARATOR '|'
                   ) as recent_agent_messages
            FROM support_tickets st
            INNER JOIN support_ticket_messages stm ON st.id = stm.ticket_id
            WHERE st.user_id = ?
              AND stm.sender_type = 'agent'
              AND stm.created_at > COALESCE(st.customer_last_seen, st.created_at)
              AND st.status IN ('open', 'in_progress', 'waiting_customer', 'resolved')
            GROUP BY st.id, st.ticket_number, st.subject, st.status, st.priority, st.updated_at
            ORDER BY MAX(stm.created_at) DESC
        ");
        
        $stmt->execute([$customerId]);
        $tickets = $stmt->fetchAll();
        
        // Get total count of unread messages
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) as total_unread
            FROM support_tickets st
            INNER JOIN support_ticket_messages stm ON st.id = stm.ticket_id
            WHERE st.user_id = ?
              AND stm.sender_type = 'agent'
              AND stm.created_at > COALESCE(st.customer_last_seen, st.created_at)
              AND st.status IN ('open', 'in_progress', 'waiting_customer', 'resolved')
        ");
        
        $countStmt->execute([$customerId]);
        $totalUnread = $countStmt->fetch()['total_unread'];
        
        // Process the data for better frontend consumption
        $notifications = [];
        foreach ($tickets as $ticket) {
            $recentMessages = array_filter(explode('|', $ticket['recent_agent_messages'] ?? ''));
            $lastMessage = !empty($recentMessages) ? trim($recentMessages[0]) : '';
            
            $notifications[] = [
                'ticket_id' => $ticket['ticket_id'],
                'ticket_number' => $ticket['ticket_number'],
                'subject' => $ticket['subject'],
                'status' => $ticket['status'],
                'priority' => $ticket['priority'],
                'unread_count' => (int)$ticket['unread_messages'],
                'last_reply_at' => $ticket['last_reply_at'],
                'preview' => $lastMessage ? substr($lastMessage, 0, 80) . (strlen($lastMessage) > 80 ? '...' : '') : null
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'tickets' => $notifications,
                'total_unread_messages' => (int)$totalUnread,
                'total_tickets_with_replies' => count($notifications)
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function markNotificationsAsRead($pdo, $customerId) {
    try {
        // Update customer_last_seen timestamp for all tickets
        $stmt = $pdo->prepare("
            UPDATE support_tickets 
            SET customer_last_seen = NOW() 
            WHERE user_id = ? 
              AND status IN ('open', 'in_progress', 'waiting_customer', 'resolved')
        ");
        
        $stmt->execute([$customerId]);
        $affectedRows = $stmt->rowCount();
        
        echo json_encode([
            'success' => true,
            'message' => 'Notifications marked as read',
            'data' => [
                'tickets_updated' => $affectedRows
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

?>