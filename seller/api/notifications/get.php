<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $auth = new SellerAuth($pdo);
    
    // Check if user is logged in, if not return empty data
    if (!$auth->isLoggedIn()) {
        echo json_encode([
            'success' => true,
            'data' => [
                'total_unread' => 0,
                'pending_orders' => 0,
                'low_stock' => 0,
                'unread_tickets' => 0,
                'by_type' => [],
                'sidebar_counts' => [
                    'orders' => 0,
                    'products' => 0,
                    'support' => 0,
                    'notifications' => 0
                ]
            ]
        ]);
        exit;
    }
    
    $sellerId = $_SESSION['seller_id'];
    
    // Get notification counts by type
    $stmt = $pdo->prepare("
        SELECT 
            type,
            COUNT(*) as count,
            SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count,
            SUM(CASE WHEN priority = 'urgent' AND is_read = 0 THEN 1 ELSE 0 END) as urgent_count
        FROM user_notifications 
        WHERE user_id = ? AND is_archived = 0
        GROUP BY type
    ");
    $stmt->execute([$sellerId]);
    $notificationStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total unread count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_unread
        FROM user_notifications 
        WHERE user_id = ? AND is_read = 0 AND is_archived = 0
    ");
    $stmt->execute([$sellerId]);
    $totalUnread = $stmt->fetch()['total_unread'];
    
    // Get pending orders count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as pending_orders
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE oi.seller_id = ? AND o.status = 'pending'
    ");
    $stmt->execute([$sellerId]);
    $pendingOrders = $stmt->fetch()['pending_orders'];
    
    // Get low stock products count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as low_stock
        FROM products 
        WHERE seller_id = ? 
        AND (stock_quantity <= low_stock_threshold OR stock_status = 'out_of_stock')
        AND status = 'published'
    ");
    $stmt->execute([$sellerId]);
    $lowStock = $stmt->fetch()['low_stock'];
    
    // Get unread support tickets count (tickets related to seller's orders/products)
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT st.id) as unread_tickets
        FROM support_tickets st
        LEFT JOIN orders o ON st.order_id = o.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE (oi.seller_id = ? OR st.order_id IS NULL) 
        AND st.status IN ('open', 'in_progress', 'waiting_customer')
    ");
    $stmt->execute([$sellerId]);
    $unreadTickets = $stmt->fetch()['unread_tickets'];
    
    // Organize stats by type
    $statsByType = [];
    foreach ($notificationStats as $stat) {
        $statsByType[$stat['type']] = $stat;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_unread' => (int)$totalUnread,
            'pending_orders' => (int)$pendingOrders,
            'low_stock' => (int)$lowStock,
            'unread_tickets' => (int)$unreadTickets,
            'by_type' => $statsByType,
            'sidebar_counts' => [
                'orders' => (int)$pendingOrders,
                'products' => (int)$lowStock,
                'support' => (int)$unreadTickets,
                'notifications' => (int)$totalUnread
            ]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch notifications'
    ]);
}
?>