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
    
    // Get query parameters
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = max(1, min(50, (int)($_GET['limit'] ?? 20)));
    $status = $_GET['status'] ?? '';
    $category = $_GET['category'] ?? '';
    $priority = $_GET['priority'] ?? '';
    
    $offset = ($page - 1) * $limit;
    
    // Build WHERE conditions
    $whereConditions = ["(oi.seller_id = ? OR st.order_id IS NULL)"];
    $params = [$sellerId];
    
    if ($status) {
        $whereConditions[] = "st.status = ?";
        $params[] = $status;
    }
    
    if ($category) {
        $whereConditions[] = "st.category = ?";
        $params[] = $category;
    }
    
    if ($priority) {
        $whereConditions[] = "st.priority = ?";
        $params[] = $priority;
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Get tickets with user info
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
            u.first_name,
            u.last_name,
            u.email,
            (SELECT COUNT(*) FROM support_ticket_messages stm WHERE stm.ticket_id = st.id) as message_count,
            (SELECT created_at FROM support_ticket_messages stm WHERE stm.ticket_id = st.id ORDER BY created_at DESC LIMIT 1) as last_message_at
        FROM support_tickets st
        JOIN users u ON st.user_id = u.id
        LEFT JOIN orders o ON st.order_id = o.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE $whereClause
        ORDER BY st.updated_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $countStmt = $pdo->prepare("
        SELECT COUNT(DISTINCT st.id) as total
        FROM support_tickets st
        LEFT JOIN orders o ON st.order_id = o.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE $whereClause
    ");
    $countStmt->execute(array_slice($params, 0, -2));
    $total = $countStmt->fetch()['total'];
    
    // Format response
    foreach ($tickets as &$ticket) {
        $ticket['customer_name'] = trim($ticket['first_name'] . ' ' . $ticket['last_name']);
        $ticket['created_at'] = date('Y-m-d H:i:s', strtotime($ticket['created_at']));
        $ticket['updated_at'] = date('Y-m-d H:i:s', strtotime($ticket['updated_at']));
        $ticket['last_message_at'] = $ticket['last_message_at'] ? date('Y-m-d H:i:s', strtotime($ticket['last_message_at'])) : null;
        $ticket['message_count'] = (int)$ticket['message_count'];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'tickets' => $tickets,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'pages' => ceil($total / $limit)
            ]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch support tickets'
    ]);
}
?>