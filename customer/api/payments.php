<?php
// Customer Payments API - PayMongo Integration
// Headers are set in index.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/paymongo.php';

// Start session for authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Get actual logged-in user ID from session
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}
$userId = $_SESSION['customer_id'];

$payMongo = new PayMongo();

switch ($action) {
    case 'create-gcash-payment':
    case 'create_gcash_payment':
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Validate input
        if (!isset($input['order_id']) || !isset($input['amount'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Order ID and amount are required']);
            break;
        }
        
        $orderId = intval($input['order_id']);
        $amount = floatval($input['amount']);
        
        try {
            // Verify order belongs to user and get order details
            $stmt = $pdo->prepare("
                SELECT id, order_number, total_amount, payment_status, status
                FROM orders 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch();
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Order not found']);
                break;
            }
            
            // Check if payment is still pending
            if ($order['payment_status'] !== 'pending') {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Order payment is already ' . $order['payment_status']
                ]);
                break;
            }
            
            // Verify amount matches order total
            if (abs($amount - floatval($order['total_amount'])) > 0.01) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Amount mismatch',
                    'order_total' => floatval($order['total_amount']),
                    'provided_amount' => $amount
                ]);
                break;
            }
            
            // Create PayMongo payment intent
            $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $baseUrl .= '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            
            $paymentIntentResponse = $payMongo->createPaymentIntent($amount, 'PHP', ['gcash']);
            
            if (!$paymentIntentResponse['success']) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create payment intent',
                    'error' => $paymentIntentResponse['data']['errors'][0]['detail'] ?? 'Unknown PayMongo error'
                ]);
                break;
            }
            
            $paymentIntent = $paymentIntentResponse['data']['data'];
            $paymentIntentId = $paymentIntent['id'];
            
            // Create GCash payment method
            $returnUrl = $baseUrl . '/payment-success.php?order_id=' . $orderId . '&payment_intent_id=' . $paymentIntentId;
            
            $paymentMethodResponse = $payMongo->createGCashPaymentMethod($returnUrl);
            
            if (!$paymentMethodResponse['success']) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create GCash payment method',
                    'error' => $paymentMethodResponse['data']['errors'][0]['detail'] ?? 'Unknown PayMongo error'
                ]);
                break;
            }
            
            $paymentMethod = $paymentMethodResponse['data']['data'];
            $paymentMethodId = $paymentMethod['id'];
            
            // Attach payment method to payment intent
            $attachResponse = $payMongo->attachPaymentMethod($paymentIntentId, $paymentMethodId, $returnUrl);
            
            if (!$attachResponse['success']) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to attach payment method',
                    'error' => $attachResponse['data']['errors'][0]['detail'] ?? 'Unknown PayMongo error'
                ]);
                break;
            }
            
            $attachedPaymentIntent = $attachResponse['data']['data'];
            
            // Store payment intent details in database
            $stmt = $pdo->prepare("
                INSERT INTO payment_transactions (
                    order_id, payment_method, payment_intent_id, payment_method_id,
                    amount, currency, status, created_at
                ) VALUES (?, 'gcash', ?, ?, ?, 'PHP', 'pending', NOW())
                ON DUPLICATE KEY UPDATE
                payment_intent_id = VALUES(payment_intent_id),
                payment_method_id = VALUES(payment_method_id),
                status = 'pending',
                updated_at = NOW()
            ");
            
            $stmt->execute([$orderId, $paymentIntentId, $paymentMethodId, $amount]);
            
            // Get the checkout URL from the response
            $checkoutUrl = null;
            if (isset($attachedPaymentIntent['attributes']['next_action']['redirect']['url'])) {
                $checkoutUrl = $attachedPaymentIntent['attributes']['next_action']['redirect']['url'];
            } else {
                // Fallback: construct checkout URL
                $checkoutUrl = "https://checkout.paymongo.com/payment_intents/{$paymentIntentId}";
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'GCash payment initialized',
                'data' => [
                    'payment_intent_id' => $paymentIntentId,
                    'payment_method_id' => $paymentMethodId,
                    'checkout_url' => $checkoutUrl,
                    'order_number' => $order['order_number'],
                    'amount' => $amount
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Payment creation failed: ' . $e->getMessage()]);
        }
        break;
        
    case 'verify-payment':
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        if (!isset($input['payment_intent_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Payment intent ID is required']);
            break;
        }
        
        $paymentIntentId = $input['payment_intent_id'];
        
        try {
            // Get payment intent from PayMongo
            $url = "https://api.paymongo.com/v1/payment_intents/{$paymentIntentId}";
            
            $headers = [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode('sk_test_pjwsYuhwzBmMt8Mg1coog2CN' . ':')
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30
            ]);
            
            $curlResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $response = [
                'success' => $httpCode >= 200 && $httpCode < 300,
                'http_code' => $httpCode,
                'data' => json_decode($curlResponse, true),
                'raw_response' => $curlResponse
            ];
            
            if (!$response['success']) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to verify payment',
                    'error' => 'PayMongo API error'
                ]);
                break;
            }
            
            $paymentIntent = $response['data']['data'];
            $status = $paymentIntent['attributes']['status'];
            $amount = $paymentIntent['attributes']['amount'] / 100; // Convert from centavos
            
            // Get order from database
            $stmt = $pdo->prepare("
                SELECT o.*, pt.id as transaction_id
                FROM orders o
                LEFT JOIN payment_transactions pt ON o.id = pt.order_id
                WHERE pt.payment_intent_id = ? AND o.user_id = ?
            ");
            $stmt->execute([$paymentIntentId, $userId]);
            $order = $stmt->fetch();
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Order not found']);
                break;
            }
            
            $pdo->beginTransaction();
            
            // Update payment transaction status
            $stmt = $pdo->prepare("
                UPDATE payment_transactions 
                SET status = ?, paymongo_payment_id = ?, updated_at = NOW()
                WHERE payment_intent_id = ?
            ");
            
            $paymentId = isset($paymentIntent['attributes']['payments'][0]['id']) 
                ? $paymentIntent['attributes']['payments'][0]['id'] 
                : null;
            
            $stmt->execute([$status, $paymentId, $paymentIntentId]);
            
            // Update order based on payment status
            if ($status === 'succeeded') {
                // Payment successful - update order
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET payment_status = 'paid', payment_reference = ?, status = 'processing', updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$paymentIntentId, $order['id']]);
                
                // Add order status history
                $stmt = $pdo->prepare("
                    INSERT INTO order_status_history (order_id, status, notes, created_at)
                    VALUES (?, 'processing', 'Payment confirmed via GCash', NOW())
                ");
                $stmt->execute([$order['id']]);
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Payment verified successfully',
                    'data' => [
                        'order_id' => intval($order['id']),
                        'order_number' => $order['order_number'],
                        'payment_status' => 'paid',
                        'order_status' => 'processing',
                        'amount' => $amount
                    ]
                ]);
                
            } elseif ($status === 'failed') {
                // Payment failed
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET payment_status = 'failed', updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$order['id']]);
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Payment failed',
                    'data' => [
                        'order_id' => intval($order['id']),
                        'order_number' => $order['order_number'],
                        'payment_status' => 'failed',
                        'amount' => $amount
                    ]
                ]);
                
            } else {
                // Payment still processing
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Payment is being processed',
                    'data' => [
                        'order_id' => intval($order['id']),
                        'order_number' => $order['order_number'],
                        'payment_status' => $status,
                        'amount' => $amount
                    ]
                ]);
            }
            
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Payment verification failed: ' . $e->getMessage()]);
        }
        break;
        
    case 'webhook':
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // PayMongo webhook handler
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';
        
        // Note: In production, you should verify the webhook signature
        // For now, we'll process without verification for testing
        
        $webhookData = json_decode($payload, true);
        
        if (!$webhookData) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid webhook payload']);
            break;
        }
        
        try {
            $eventType = $webhookData['data']['attributes']['type'];
            $eventData = $webhookData['data']['attributes']['data'];
            
            if ($eventType === 'payment.paid') {
                // Payment completed successfully
                $paymentIntentId = $eventData['attributes']['payment_intent_id'] ?? null;
                
                if ($paymentIntentId) {
                    // Find and update the order
                    $stmt = $pdo->prepare("
                        SELECT o.id, o.order_number
                        FROM orders o
                        JOIN payment_transactions pt ON o.id = pt.order_id
                        WHERE pt.payment_intent_id = ?
                    ");
                    $stmt->execute([$paymentIntentId]);
                    $order = $stmt->fetch();
                    
                    if ($order) {
                        $pdo->beginTransaction();
                        
                        // Update order status
                        $stmt = $pdo->prepare("
                            UPDATE orders 
                            SET payment_status = 'paid', status = 'processing', updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$order['id']]);
                        
                        // Update transaction status
                        $stmt = $pdo->prepare("
                            UPDATE payment_transactions 
                            SET status = 'succeeded', updated_at = NOW()
                            WHERE payment_intent_id = ?
                        ");
                        $stmt->execute([$paymentIntentId]);
                        
                        // Add status history
                        $stmt = $pdo->prepare("
                            INSERT INTO order_status_history (order_id, status, notes, created_at)
                            VALUES (?, 'processing', 'Payment confirmed via webhook', NOW())
                        ");
                        $stmt->execute([$order['id']]);
                        
                        $pdo->commit();
                    }
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Webhook processed']);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Webhook processing failed: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Payment endpoint not found']);
}
?>