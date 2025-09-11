<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/response.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get notification preferences
    try {
        $stmt = $pdo->prepare("
            SELECT 
                email_new_orders, 
                email_order_updates, 
                email_customer_messages, 
                email_marketing_updates,
                sms_urgent_orders,
                sms_payment_issues,
                timezone,
                currency
            FROM seller_preferences 
            WHERE seller_id = ?
        ");
        $stmt->execute([$sellerId]);
        $preferences = $stmt->fetch();
        
        // If no preferences exist, create default ones
        if (!$preferences) {
            $stmt = $pdo->prepare("
                INSERT INTO seller_preferences (
                    seller_id, 
                    email_new_orders, 
                    email_order_updates, 
                    email_customer_messages, 
                    email_marketing_updates,
                    sms_urgent_orders,
                    sms_payment_issues,
                    timezone,
                    currency
                ) VALUES (?, 1, 1, 0, 1, 1, 0, 'UTC-5', 'USD')
            ");
            
            if ($stmt->execute([$sellerId])) {
                // Get the newly created preferences
                $stmt = $pdo->prepare("
                    SELECT 
                        email_new_orders, 
                        email_order_updates, 
                        email_customer_messages, 
                        email_marketing_updates,
                        sms_urgent_orders,
                        sms_payment_issues,
                        timezone,
                        currency
                    FROM seller_preferences 
                    WHERE seller_id = ?
                ");
                $stmt->execute([$sellerId]);
                $preferences = $stmt->fetch();
            }
        }
        
        APIResponse::success($preferences, 'Preferences retrieved successfully');
    } catch (PDOException $e) {
        APIResponse::error('Database error: ' . $e->getMessage(), 500);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update preferences
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'email_new_orders',
            'email_order_updates', 
            'email_customer_messages', 
            'email_marketing_updates',
            'sms_urgent_orders',
            'sms_payment_issues',
            'timezone',
            'currency'
        ];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $input)) {
                $fields[] = "$field = ?";
                // Convert boolean values for database storage
                if (is_bool($input[$field])) {
                    $params[] = $input[$field] ? 1 : 0;
                } else {
                    $params[] = $input[$field];
                }
            }
        }
        
        if (!empty($fields)) {
            $params[] = $sellerId;
            $sql = "UPDATE seller_preferences SET " . implode(', ', $fields) . " WHERE seller_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            APIResponse::success([], 'Preferences updated successfully');
        } else {
            APIResponse::error('No valid fields to update', 400);
        }
    } catch (PDOException $e) {
        APIResponse::error('Database error: ' . $e->getMessage(), 500);
    } catch (Exception $e) {
        APIResponse::error('Update failed: ' . $e->getMessage(), 500);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>