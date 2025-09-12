<?php

class NotificationHelper {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Create a new notification
     */
    private function createNotification($userId, $type, $title, $message, $data = null, $relatedId = null, $relatedType = null, $priority = 'medium', $actionUrl = null, $expiresAt = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO user_notifications (
                    user_id, type, title, message, data, related_id, related_type, 
                    priority, action_url, expires_at, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $jsonData = $data ? json_encode($data) : null;
            
            return $stmt->execute([
                $userId, $type, $title, $message, $jsonData, 
                $relatedId, $relatedType, $priority, $actionUrl, $expiresAt
            ]);
        } catch (Exception $e) {
            error_log("Failed to create notification: " . $e->getMessage());
            return false;
        }
    }
    
    // ORDER NOTIFICATIONS
    public function orderPlaced($userId, $orderId, $orderNumber, $total) {
        return $this->createNotification(
            $userId,
            'order',
            'Order Placed Successfully',
            "Your order #$orderNumber for ₱" . number_format($total, 2) . " has been placed successfully. We'll send you updates as your order is processed.",
            ['order_number' => $orderNumber, 'total' => $total],
            $orderId,
            'order',
            'medium',
            "/Core1_ecommerce/customer/account/orders.php?id=$orderId"
        );
    }
    
    public function orderConfirmed($userId, $orderId, $orderNumber) {
        return $this->createNotification(
            $userId,
            'order',
            'Order Confirmed',
            "Great news! Your order #$orderNumber has been confirmed and is being prepared for shipment.",
            ['order_number' => $orderNumber, 'status' => 'confirmed'],
            $orderId,
            'order',
            'medium',
            "/Core1_ecommerce/customer/account/orders.php?id=$orderId"
        );
    }
    
    public function orderShipped($userId, $orderId, $orderNumber, $trackingNumber = null) {
        $message = "Your order #$orderNumber has been shipped!";
        if ($trackingNumber) {
            $message .= " Tracking number: $trackingNumber";
        }
        
        return $this->createNotification(
            $userId,
            'shipping',
            'Order Shipped',
            $message,
            ['order_number' => $orderNumber, 'tracking_number' => $trackingNumber, 'status' => 'shipped'],
            $orderId,
            'order',
            'high',
            "/Core1_ecommerce/customer/account/orders.php?id=$orderId"
        );
    }
    
    public function orderDelivered($userId, $orderId, $orderNumber) {
        return $this->createNotification(
            $userId,
            'order',
            'Order Delivered',
            "Your order #$orderNumber has been delivered! We hope you love your purchase. Don't forget to leave a review!",
            ['order_number' => $orderNumber, 'status' => 'delivered'],
            $orderId,
            'order',
            'high',
            "/Core1_ecommerce/customer/account/orders.php?id=$orderId"
        );
    }
    
    public function orderCancelled($userId, $orderId, $orderNumber, $reason = null) {
        $message = "Your order #$orderNumber has been cancelled.";
        if ($reason) {
            $message .= " Reason: $reason";
        }
        $message .= " Any payment will be refunded within 3-5 business days.";
        
        return $this->createNotification(
            $userId,
            'order',
            'Order Cancelled',
            $message,
            ['order_number' => $orderNumber, 'status' => 'cancelled', 'reason' => $reason],
            $orderId,
            'order',
            'medium',
            "/Core1_ecommerce/customer/account/orders.php?id=$orderId"
        );
    }
    
    // PAYMENT NOTIFICATIONS
    public function paymentReceived($userId, $orderId, $orderNumber, $amount) {
        return $this->createNotification(
            $userId,
            'payment',
            'Payment Received',
            "We've successfully received your payment of ₱" . number_format($amount, 2) . " for order #$orderNumber.",
            ['order_number' => $orderNumber, 'amount' => $amount, 'status' => 'paid'],
            $orderId,
            'order',
            'medium',
            "/Core1_ecommerce/customer/account/orders.php?id=$orderId"
        );
    }
    
    public function paymentFailed($userId, $orderId, $orderNumber, $reason = null) {
        $message = "Payment for order #$orderNumber failed.";
        if ($reason) {
            $message .= " Reason: $reason";
        }
        $message .= " Please try again or use a different payment method.";
        
        return $this->createNotification(
            $userId,
            'payment',
            'Payment Failed',
            $message,
            ['order_number' => $orderNumber, 'status' => 'failed', 'reason' => $reason],
            $orderId,
            'order',
            'high',
            "/Core1_ecommerce/customer/account/orders.php?id=$orderId"
        );
    }
    
    public function refundProcessed($userId, $orderId, $orderNumber, $amount) {
        return $this->createNotification(
            $userId,
            'payment',
            'Refund Processed',
            "Your refund of ₱" . number_format($amount, 2) . " for order #$orderNumber has been processed and will appear in your account within 3-5 business days.",
            ['order_number' => $orderNumber, 'amount' => $amount, 'status' => 'refunded'],
            $orderId,
            'order',
            'medium'
        );
    }
    
    // PRODUCT NOTIFICATIONS
    public function productBackInStock($userId, $productId, $productName) {
        return $this->createNotification(
            $userId,
            'product',
            'Product Back in Stock',
            "Good news! \"$productName\" is now back in stock. Order now before it sells out again!",
            ['product_name' => $productName, 'status' => 'in_stock'],
            $productId,
            'product',
            'medium',
            "/Core1_ecommerce/customer/products/detail.php?id=$productId"
        );
    }
    
    public function productPriceDropped($userId, $productId, $productName, $oldPrice, $newPrice) {
        $savings = $oldPrice - $newPrice;
        return $this->createNotification(
            $userId,
            'product',
            'Price Drop Alert!',
            "Great news! \"$productName\" price dropped from ₱" . number_format($oldPrice, 2) . " to ₱" . number_format($newPrice, 2) . ". You save ₱" . number_format($savings, 2) . "!",
            ['product_name' => $productName, 'old_price' => $oldPrice, 'new_price' => $newPrice, 'savings' => $savings],
            $productId,
            'product',
            'high',
            "/Core1_ecommerce/customer/products/detail.php?id=$productId"
        );
    }
    
    public function wishlistItemOnSale($userId, $productId, $productName, $discount) {
        return $this->createNotification(
            $userId,
            'product',
            'Wishlist Item on Sale!',
            "\"$productName\" from your wishlist is now on sale with $discount% off! Don't miss this opportunity.",
            ['product_name' => $productName, 'discount' => $discount],
            $productId,
            'product',
            'high',
            "/Core1_ecommerce/customer/products/detail.php?id=$productId"
        );
    }
    
    // SUPPORT NOTIFICATIONS
    public function supportTicketReply($userId, $ticketId, $ticketNumber, $agentName = 'Support Agent') {
        return $this->createNotification(
            $userId,
            'support',
            'New Support Reply',
            "$agentName replied to your support ticket #$ticketNumber. Click to view the response.",
            ['ticket_number' => $ticketNumber, 'agent_name' => $agentName],
            $ticketId,
            'ticket',
            'medium',
            "/Core1_ecommerce/customer/support/ticket-detail.php?id=$ticketId"
        );
    }
    
    public function supportTicketResolved($userId, $ticketId, $ticketNumber) {
        return $this->createNotification(
            $userId,
            'support',
            'Support Ticket Resolved',
            "Your support ticket #$ticketNumber has been marked as resolved. If you need further assistance, please reply to reopen the ticket.",
            ['ticket_number' => $ticketNumber, 'status' => 'resolved'],
            $ticketId,
            'ticket',
            'medium',
            "/Core1_ecommerce/customer/support/ticket-detail.php?id=$ticketId"
        );
    }
    
    // PROMOTIONAL NOTIFICATIONS
    public function newPromotion($userId, $title, $description, $discountCode = null, $expiresAt = null) {
        $message = $description;
        if ($discountCode) {
            $message .= " Use code: $discountCode";
        }
        
        return $this->createNotification(
            $userId,
            'promotion',
            $title,
            $message,
            ['discount_code' => $discountCode, 'expires_at' => $expiresAt],
            null,
            null,
            'medium',
            "/Core1_ecommerce/customer/products.php",
            $expiresAt
        );
    }
    
    public function flashSale($userId, $title, $endTime, $products = []) {
        return $this->createNotification(
            $userId,
            'promotion',
            "⚡ Flash Sale: $title",
            "Limited time flash sale is now live! Don't miss out on amazing deals. Sale ends at " . date('h:i A', strtotime($endTime)),
            ['products' => $products, 'end_time' => $endTime],
            null,
            null,
            'urgent',
            "/Core1_ecommerce/customer/products.php",
            $endTime
        );
    }
    
    public function birthdayDiscount($userId, $discountCode, $discount) {
        return $this->createNotification(
            $userId,
            'promotion',
            'Happy Birthday! 🎉',
            "Happy birthday! Enjoy a special $discount% discount on your next purchase. Use code: $discountCode. Valid for 7 days!",
            ['discount_code' => $discountCode, 'discount' => $discount],
            null,
            null,
            'high',
            "/Core1_ecommerce/customer/products.php",
            date('Y-m-d H:i:s', strtotime('+7 days'))
        );
    }
    
    // ACCOUNT NOTIFICATIONS
    public function accountVerified($userId) {
        return $this->createNotification(
            $userId,
            'account',
            'Account Verified',
            "Congratulations! Your account has been successfully verified. You now have access to all features of our platform.",
            ['status' => 'verified'],
            null,
            null,
            'medium',
            "/Core1_ecommerce/customer/account/profile.php"
        );
    }
    
    public function passwordChanged($userId) {
        return $this->createNotification(
            $userId,
            'account',
            'Password Changed',
            "Your account password has been changed successfully. If you didn't make this change, please contact our support team immediately.",
            ['action' => 'password_changed'],
            null,
            null,
            'high',
            "/Core1_ecommerce/customer/support/index.php"
        );
    }
    
    public function profileUpdated($userId) {
        return $this->createNotification(
            $userId,
            'account',
            'Profile Updated',
            "Your profile information has been updated successfully.",
            ['action' => 'profile_updated'],
            null,
            null,
            'low'
        );
    }
    
    // SYSTEM NOTIFICATIONS
    public function systemMaintenance($userId, $startTime, $endTime) {
        return $this->createNotification(
            $userId,
            'system',
            'Scheduled Maintenance',
            "Our platform will undergo scheduled maintenance from " . date('M d, Y h:i A', strtotime($startTime)) . " to " . date('M d, Y h:i A', strtotime($endTime)) . ". Some features may be temporarily unavailable.",
            ['start_time' => $startTime, 'end_time' => $endTime],
            null,
            null,
            'medium',
            null,
            $endTime
        );
    }
    
    public function newFeatures($userId, $features) {
        return $this->createNotification(
            $userId,
            'system',
            'New Features Available!',
            "We've added exciting new features to enhance your shopping experience. Check them out!",
            ['features' => $features],
            null,
            null,
            'low'
        );
    }
    
    /**
     * Clean up expired notifications
     */
    public function cleanupExpiredNotifications() {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM user_notifications WHERE expires_at < NOW()");
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Failed to cleanup expired notifications: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get notification statistics for a user
     */
    public function getUserNotificationStats($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN is_read = FALSE THEN 1 ELSE 0 END) as unread,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as this_week,
                    type,
                    COUNT(*) as type_count
                FROM user_notifications 
                WHERE user_id = ? AND is_archived = FALSE
                GROUP BY type
            ");
            
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Failed to get notification stats: " . $e->getMessage());
            return [];
        }
    }
}

?>