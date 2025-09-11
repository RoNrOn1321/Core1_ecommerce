<?php
session_start();

echo "<h3>Session Debug Information</h3>";
echo "<h4>Session ID: " . session_id() . "</h4>";
echo "<h4>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</h4>";
echo "<h4>Session Variables:</h4>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Test auth class
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$auth = new SellerAuth($pdo);
echo "<h4>Is Logged In: " . ($auth->isLoggedIn() ? 'Yes' : 'No') . "</h4>";

if ($auth->isLoggedIn()) {
    $seller = $auth->getCurrentSeller();
    echo "<h4>Current Seller:</h4>";
    echo "<pre>";
    var_dump($seller);
    echo "</pre>";
}

echo "<h4>Cookies:</h4>";
echo "<pre>";
var_dump($_COOKIE);
echo "</pre>";
?>