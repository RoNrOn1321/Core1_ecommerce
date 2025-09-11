<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

echo "<h2>Direct Authentication Test</h2>";

$auth = new SellerAuth($pdo);

// Test login with hardcoded credentials (you should replace with your actual credentials)
echo "<h3>Testing login...</h3>";

// Replace with your actual seller credentials
$email = "test@example.com"; // Change this to your seller email
$password = "password123"; // Change this to your seller password

echo "<p><strong>Email:</strong> $email</p>";
echo "<p><strong>Trying to login...</strong></p>";

$result = $auth->login($email, $password);

echo "<h4>Login Result:</h4>";
echo "<pre>";
var_dump($result);
echo "</pre>";

echo "<h4>Session after login:</h4>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

echo "<h4>Is Logged In: " . ($auth->isLoggedIn() ? 'Yes' : 'No') . "</h4>";

if ($auth->isLoggedIn()) {
    $seller = $auth->getCurrentSeller();
    echo "<h4>Current Seller Data:</h4>";
    echo "<pre>";
    var_dump($seller);
    echo "</pre>";
}

// Test database connection
echo "<h3>Database Test</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sellers");
    $count = $stmt->fetch();
    echo "<p>Total sellers in database: " . $count['count'] . "</p>";
    
    $stmt = $pdo->query("SELECT s.id, s.store_name, u.email, s.status FROM sellers s JOIN users u ON s.user_id = u.id LIMIT 5");
    $sellers = $stmt->fetchAll();
    echo "<h4>Sample sellers:</h4>";
    echo "<pre>";
    var_dump($sellers);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p>Database error: " . $e->getMessage() . "</p>";
}
?>