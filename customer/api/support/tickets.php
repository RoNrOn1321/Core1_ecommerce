<?php
// Customer Support Tickets API
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
$resource = $endpointParts[1] ?? ''; // tickets
$id = $endpointParts[2] ?? null; // ticket ID (optional)

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
            if ($id) {
                // Get specific ticket
                getTicketById($pdo, $customerId, $id);
            } else {
                // Get all tickets for customer
                getCustomerTickets($pdo, $customerId);
            }
            break;
            
        case 'POST':
            // Create new ticket
            createTicket($pdo, $customerId, $input);
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

function createTicket($pdo, $customerId, $data) {
    try {
        // Validate required fields
        $requiredFields = ['category', 'priority', 'subject', 'description'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => "Field '$field' is required"
                ]);
                return;
            }
        }
        
        // Validate enum values
        $validCategories = ['order', 'product', 'payment', 'shipping', 'technical', 'other'];
        $validPriorities = ['low', 'medium', 'high', 'urgent'];
        
        if (!in_array($data['category'], $validCategories)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid category']);
            return;
        }
        
        if (!in_array($data['priority'], $validPriorities)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid priority']);
            return;
        }
        
        // Validate text lengths
        if (strlen($data['subject']) > 200) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Subject must be 200 characters or less']);
            return;
        }
        
        $pdo->beginTransaction();
        
        // Generate unique ticket number
        $ticketNumber = generateTicketNumber($pdo);
        
        // Prepare order ID (if provided)
        $orderId = null;
        if (!empty($data['order_id']) && is_numeric($data['order_id'])) {
            // Verify the order belongs to this customer
            $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
            $stmt->execute([$data['order_id'], $customerId]);
            if ($stmt->fetch()) {
                $orderId = (int)$data['order_id'];
            }
        }
        
        // Insert ticket
        $stmt = $pdo->prepare("
            INSERT INTO support_tickets (
                ticket_number, user_id, subject, category, priority, 
                order_id, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $ticketNumber,
            $customerId,
            trim($data['subject']),
            $data['category'],
            $data['priority'],
            $orderId
        ]);
        
        $ticketId = $pdo->lastInsertId();
        
        // Insert initial message
        $stmt = $pdo->prepare("
            INSERT INTO support_ticket_messages (
                ticket_id, sender_type, sender_id, message, created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $ticketId,
            'customer',
            $customerId,
            trim($data['description'])
        ]);
        
        $pdo->commit();
        
        // Get the created ticket details
        $stmt = $pdo->prepare("
            SELECT st.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                   u.email as customer_email
            FROM support_tickets st 
            LEFT JOIN users u ON st.user_id = u.id 
            WHERE st.id = ?
        ");
        $stmt->execute([$ticketId]);
        $ticket = $stmt->fetch();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Support ticket created successfully',
            'data' => [
                'id' => $ticketId,
                'ticket_number' => $ticketNumber,
                'subject' => $ticket['subject'],
                'category' => $ticket['category'],
                'priority' => $ticket['priority'],
                'status' => $ticket['status'],
                'created_at' => $ticket['created_at']
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function getCustomerTickets($pdo, $customerId) {
    try {
        // Get query parameters
        $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 50;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $status = $_GET['status'] ?? '';
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';
        
        // Build WHERE conditions
        $whereConditions = ['st.user_id = ?'];
        $params = [$customerId];
        
        if (!empty($status)) {
            $whereConditions[] = 'st.status = ?';
            $params[] = $status;
        }
        
        if (!empty($category)) {
            $whereConditions[] = 'st.category = ?';
            $params[] = $category;
        }
        
        if (!empty($search)) {
            $whereConditions[] = '(st.subject LIKE ? OR st.ticket_number LIKE ?)';
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM support_tickets st 
            WHERE $whereClause
        ");
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch()['total'];
        
        // Get tickets
        $stmt = $pdo->prepare("
            SELECT st.*, 
                   (SELECT COUNT(*) FROM support_ticket_messages WHERE ticket_id = st.id) as message_count,
                   (SELECT created_at FROM support_ticket_messages WHERE ticket_id = st.id ORDER BY created_at DESC LIMIT 1) as last_message_at,
                   o.order_number
            FROM support_tickets st 
            LEFT JOIN orders o ON st.order_id = o.id
            WHERE $whereClause
            ORDER BY 
                CASE WHEN st.status = 'open' THEN 1 
                     WHEN st.status = 'in_progress' THEN 2 
                     ELSE 3 END,
                st.priority = 'urgent' DESC,
                st.priority = 'high' DESC,
                st.created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        
        $stmt->execute($params);
        $tickets = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $tickets,
            'pagination' => [
                'total' => (int)$totalCount,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $totalCount
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function getTicketById($pdo, $customerId, $ticketId) {
    try {
        // Get ticket details
        $stmt = $pdo->prepare("
            SELECT st.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                   u.email as customer_email,
                   o.order_number,
                   o.total_amount as order_total
            FROM support_tickets st 
            LEFT JOIN users u ON st.user_id = u.id 
            LEFT JOIN orders o ON st.order_id = o.id
            WHERE st.id = ? AND st.user_id = ?
        ");
        
        $stmt->execute([$ticketId, $customerId]);
        $ticket = $stmt->fetch();
        
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            return;
        }
        
        // Get ticket messages
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
        
        // Add messages to ticket data
        $ticket['messages'] = $messages;
        $ticket['message_count'] = count($messages);
        
        echo json_encode([
            'success' => true,
            'data' => $ticket
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function generateTicketNumber($pdo) {
    $prefix = 'TKT';
    $year = date('Y');
    $month = date('m');
    
    // Get the next sequence number for this month
    $stmt = $pdo->prepare("
        SELECT MAX(CAST(SUBSTRING(ticket_number, -6) AS UNSIGNED)) as max_seq 
        FROM support_tickets 
        WHERE ticket_number LIKE ? 
    ");
    $stmt->execute(["$prefix-$year$month-%"]);
    $result = $stmt->fetch();
    
    $nextSeq = ($result['max_seq'] ?? 0) + 1;
    
    return sprintf('%s-%s%s-%06d', $prefix, $year, $month, $nextSeq);
}

?>