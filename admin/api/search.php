<?php
require_once '../config/auth.php';
require_once '../config/database.php';

// Require authentication
requireAuth();

header('Content-Type: application/json');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode(['status' => 'error', 'message' => 'Search query is required']);
    exit;
}

$query = trim($_GET['q']);
$searchResults = [];

try {
    // Search Users
    $stmt = $pdo->prepare("
        SELECT 'user' as type, id, CONCAT(first_name, ' ', last_name) as title, email as subtitle, status
        FROM users 
        WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?
        LIMIT 5
    ");
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        $searchResults[] = [
            'type' => 'user',
            'id' => $user['id'],
            'title' => $user['title'],
            'subtitle' => $user['subtitle'],
            'status' => $user['status'],
            'url' => 'user_detail.php?id=' . $user['id'],
            'icon' => 'fe-user'
        ];
    }

    // Search Sellers
    $stmt = $pdo->prepare("
        SELECT 'seller' as type, s.id, s.store_name as title, s.store_description as subtitle, s.status
        FROM sellers s
        WHERE s.store_name LIKE ? OR s.store_description LIKE ?
        LIMIT 5
    ");
    $stmt->execute([$searchTerm, $searchTerm]);
    $sellers = $stmt->fetchAll();
    
    foreach ($sellers as $seller) {
        $searchResults[] = [
            'type' => 'seller',
            'id' => $seller['id'],
            'title' => $seller['title'],
            'subtitle' => $seller['subtitle'],
            'status' => $seller['status'],
            'url' => 'seller_detail.php?id=' . $seller['id'],
            'icon' => 'fe-user-check'
        ];
    }

    // Search Products
    $stmt = $pdo->prepare("
        SELECT 'product' as type, p.id, p.name as title, s.store_name as subtitle, p.status, p.price
        FROM products p
        LEFT JOIN sellers s ON p.seller_id = s.id
        WHERE p.name LIKE ? OR p.description LIKE ?
        LIMIT 5
    ");
    $stmt->execute([$searchTerm, $searchTerm]);
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $searchResults[] = [
            'type' => 'product',
            'id' => $product['id'],
            'title' => $product['title'],
            'subtitle' => 'by ' . ($product['subtitle'] ?: 'Unknown Store') . ' - ₱' . number_format($product['price'], 2),
            'status' => $product['status'],
            'url' => 'products.php?search=' . urlencode($product['title']),
            'icon' => 'fe-package'
        ];
    }

    // Search Orders
    $stmt = $pdo->prepare("
        SELECT 'order' as type, o.id, o.order_number as title, 
               CONCAT(u.first_name, ' ', u.last_name) as subtitle, o.status, o.total_amount
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.order_number LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?
        LIMIT 5
    ");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $orders = $stmt->fetchAll();
    
    foreach ($orders as $order) {
        $searchResults[] = [
            'type' => 'order',
            'id' => $order['id'],
            'title' => 'Order #' . $order['title'],
            'subtitle' => 'by ' . ($order['subtitle'] ?: 'Unknown Customer') . ' - ₱' . number_format($order['total_amount'], 2),
            'status' => $order['status'],
            'url' => 'order_detail.php?id=' . $order['id'],
            'icon' => 'fe-shopping-cart'
        ];
    }

    echo json_encode([
        'status' => 'success',
        'results' => $searchResults,
        'total' => count($searchResults)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Search failed'
    ]);
}