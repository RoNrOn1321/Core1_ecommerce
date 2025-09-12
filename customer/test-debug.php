<?php
// Debug test for API routing
session_start();

// Simulate a logged-in user for testing
$_SESSION['customer_id'] = 1; // Assuming user ID 1 exists

echo "<h1>API Debug Test</h1>\n";

// Test the payments endpoint
echo "<h2>Testing Payments API</h2>\n";

// Test with cURL
$url = 'http://localhost/Core1_ecommerce/customer/api/payments/create-gcash-payment';
$postData = json_encode([
    'order_id' => 1,
    'amount' => 100.00
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, ''); // Enable cookie handling
curl_setopt($ch, CURLOPT_COOKIEJAR, ''); // Enable cookie handling

// Send session cookie
$sessionName = session_name();
$sessionId = session_id();
curl_setopt($ch, CURLOPT_COOKIE, "$sessionName=$sessionId");

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>URL:</strong> $url</p>\n";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>\n";
echo "<p><strong>Response:</strong></p>\n";
echo "<pre>" . htmlspecialchars($response) . "</pre>\n";

// Parse the request path to see what's happening
$requestPath = '/Core1_ecommerce/customer/api/payments/create-gcash-payment';
$basePath = '/Core1_ecommerce/customer/api';
$endpoint = str_replace($basePath, '', $requestPath);
$endpoint = trim($endpoint, '/');

$endpointParts = explode('/', $endpoint);
$module = $endpointParts[0] ?? '';
$action = $endpointParts[1] ?? '';
$id = $endpointParts[2] ?? null;

echo "<h2>URL Parsing Debug</h2>\n";
echo "<p><strong>Request Path:</strong> $requestPath</p>\n";
echo "<p><strong>Base Path:</strong> $basePath</p>\n";
echo "<p><strong>Endpoint:</strong> $endpoint</p>\n";
echo "<p><strong>Module:</strong> $module</p>\n";
echo "<p><strong>Action:</strong> $action</p>\n";
echo "<p><strong>ID:</strong> $id</p>\n";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
h1, h2 { color: #333; }
p { margin: 10px 0; }
</style>