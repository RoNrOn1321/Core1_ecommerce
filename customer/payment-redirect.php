<?php
// PayMongo Payment Redirect Handler
// This handles the return from PayMongo and cleans up the URL parameters

session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

// Get parameters from URL
$orderId = $_GET['order_id'] ?? null;
$paymentIntentId = $_GET['payment_intent_id'] ?? null;

// Clean up parameters (PayMongo sometimes adds duplicate parameters)
if (!$paymentIntentId) {
    // Check for other possible parameter names
    $paymentIntentId = $_GET['payment_intent'] ?? null;
}

// Validate required parameters
if (!$orderId || !$paymentIntentId) {
    // If parameters are missing, redirect to orders page
    header('Location: account/orders.php');
    exit;
}

// Clean URL and redirect to proper payment success page
$cleanUrl = "payment-success.php?order_id=" . urlencode($orderId) . "&payment_intent_id=" . urlencode($paymentIntentId);
header('Location: ' . $cleanUrl);
exit;
?>