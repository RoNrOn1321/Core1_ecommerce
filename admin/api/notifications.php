<?php
require_once '../config/auth.php';
require_once '../config/database.php';

// Require authentication
requireAuth();

// Set JSON response header
header('Content-Type: application/json');

try {
    // Get notification counts
    $notifications = [];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE status = 'active'");
    $notifications['users'] = $stmt->fetch()['total_users'];
    
    // Pending sellers
    $stmt = $pdo->query("SELECT COUNT(*) as pending_sellers FROM sellers WHERE status = 'pending'");
    $notifications['sellers'] = $stmt->fetch()['pending_sellers'];
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products WHERE status = 'published'");
    $notifications['products'] = $stmt->fetch()['total_products'];
    
    // Pending orders (for notification badge)
    $stmt = $pdo->query("SELECT COUNT(*) as pending_orders FROM orders WHERE status = 'pending'");
    $notifications['orders'] = $stmt->fetch()['pending_orders'];
    
    // Open support tickets (for notification badge)
    $stmt = $pdo->query("SELECT COUNT(*) as open_tickets FROM support_tickets WHERE status IN ('open', 'in_progress')");
    $notifications['support'] = $stmt->fetch()['open_tickets'];
    
    // Return successful response
    echo json_encode([
        'success' => true,
        'data' => $notifications,
        'timestamp' => time()
    ]);
    
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred',
        'timestamp' => time()
    ]);
}
?>