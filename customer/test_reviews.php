<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "<h3>Not logged in. Please <a href='login.php'>log in first</a></h3>";
    exit;
}

echo "<h2>Reviews API Test</h2>";
echo "<p>Logged in as user ID: " . $_SESSION['customer_id'] . "</p>";

// Check if this user has any delivered orders
$stmt = $pdo->prepare("SELECT id, order_number, status FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['customer_id']]);
$orders = $stmt->fetchAll();

echo "<h3>Your Orders:</h3>";
foreach ($orders as $order) {
    echo "<p>Order #{$order['order_number']} (ID: {$order['id']}) - Status: {$order['status']}</p>";
    if ($order['status'] === 'delivered') {
        echo "<button onclick='testReviews({$order['id']})'>Test Reviews for Order {$order['id']}</button><br>";
    }
}
?>

<script src="assets/js/customer-api.js"></script>
<script>
async function testReviews(orderId) {
    console.log('Testing reviews for order:', orderId);
    
    try {
        const response = await customerAPI.reviews.getReviewableItems(orderId);
        console.log('API Response:', response);
        
        if (response.success) {
            alert('Success! Found ' + response.data.length + ' reviewable items');
            console.log('Reviewable items:', response.data);
        } else {
            alert('Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Network error: ' + error.message);
    }
}
</script>