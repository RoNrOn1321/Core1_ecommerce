<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Clear Seller Session</h2>";

echo "<h3>Before:</h3>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Clear only seller-related session data
unset($_SESSION['seller_id']);
unset($_SESSION['seller_user_id']);
unset($_SESSION['seller_email']);
unset($_SESSION['seller_name']);
unset($_SESSION['store_name']);
unset($_SESSION['store_slug']);

echo "<h3>After clearing seller session:</h3>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

echo "<p><a href='login.php'>Go to Seller Login</a></p>";
echo "<p><a href='../admin/'>Go to Admin Panel</a></p>";
?>