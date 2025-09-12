<?php
// Customer Orders API

// Headers are set in index.php
require_once __DIR__ . '/../config/database.php';

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


switch ($action) {
        
        
    case 'show':
    case 'details':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $orderId = $id;
        
        if (!$orderId || !is_numeric($orderId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid order ID required']);
            break;
        }
        
        try {
            // Get order details
            $stmt = $pdo->prepare("
                SELECT 
                    o.*,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?
            ");
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch();
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Order not found']);
                break;
            }
            
            // Get order items
            $stmt = $pdo->prepare("
                SELECT 
                    oi.*,
                    p.name as product_name,
                    p.description as product_description,
                    (SELECT image_url FROM product_images WHERE product_id = p.id LIMIT 1) as product_image,
                    s.store_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                LEFT JOIN sellers s ON oi.seller_id = s.id
                WHERE oi.order_id = ?
                ORDER BY oi.id
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll();
            
            // Format order data
            $order['id'] = intval($order['id']);
            $order['user_id'] = intval($order['user_id']);
            $order['subtotal'] = floatval($order['subtotal']);
            $order['tax_amount'] = floatval($order['tax_amount']);
            $order['shipping_cost'] = floatval($order['shipping_cost']);
            $order['discount_amount'] = floatval($order['discount_amount']);
            $order['total_amount'] = floatval($order['total_amount']);
            
            // Format items
            foreach ($items as &$item) {
                $item['id'] = intval($item['id']);
                $item['product_id'] = intval($item['product_id']);
                $item['seller_id'] = intval($item['seller_id']);
                $item['quantity'] = intval($item['quantity']);
                $item['unit_price'] = floatval($item['unit_price']);
                $item['total_price'] = floatval($item['total_price']);
                
                if ($item['variant_details']) {
                    $item['variant_details'] = json_decode($item['variant_details'], true);
                }
            }
            
            $order['items'] = $items;
            
            echo json_encode([
                'success' => true,
                'data' => $order
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch order details: ' . $e->getMessage()]);
        }
        break;
        
    case '':
    case 'create':
        if ($requestMethod === 'GET') {
            // Handle GET /orders (list orders)
            try {
                // Get query parameters
                $page = max(1, intval($_GET['page'] ?? 1));
                $limit = min(50, max(1, intval($_GET['limit'] ?? 10)));
                $offset = ($page - 1) * $limit;
                $status = $_GET['status'] ?? null;
                
                $whereClause = "WHERE o.user_id = ?";
                $params = [$userId];
                
                if ($status && in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])) {
                    $whereClause .= " AND o.status = ?";
                    $params[] = $status;
                }
                
                $sql = "
                    SELECT 
                        o.id,
                        o.order_number,
                        o.status,
                        o.subtotal,
                        o.tax_amount,
                        o.shipping_cost,
                        o.discount_amount,
                        o.total_amount,
                        o.payment_method,
                        o.payment_status,
                        o.created_at,
                        COUNT(oi.id) as item_count
                    FROM orders o
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    $whereClause
                    GROUP BY o.id
                    ORDER BY o.created_at DESC
                    LIMIT ? OFFSET ?
                ";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([...$params, $limit, $offset]);
                $orders = $stmt->fetchAll();
                
                // Get total count for pagination
                $countSql = "SELECT COUNT(*) as total FROM orders o $whereClause";
                $countStmt = $pdo->prepare($countSql);
                $countStmt->execute($params);
                $totalCount = $countStmt->fetch()['total'];
                
                // Format orders
                foreach ($orders as &$order) {
                    $order['id'] = intval($order['id']);
                    $order['subtotal'] = floatval($order['subtotal']);
                    $order['tax_amount'] = floatval($order['tax_amount']);
                    $order['shipping_cost'] = floatval($order['shipping_cost']);
                    $order['discount_amount'] = floatval($order['discount_amount']);
                    $order['total_amount'] = floatval($order['total_amount']);
                    $order['item_count'] = intval($order['item_count']);
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $orders,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $limit,
                        'total_items' => intval($totalCount),
                        'total_pages' => ceil($totalCount / $limit)
                    ]
                ]);
                
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to fetch orders: ' . $e->getMessage()]);
            }
            break;
        }
        
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Validate input
        if (!isset($input['shipping_address']) || !isset($input['payment_method'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Shipping address and payment method are required']);
            break;
        }
        
        $shippingAddress = $input['shipping_address'];
        $paymentMethod = $input['payment_method'];
        $orderNotes = $input['order_notes'] ?? null;
        
        // Validate required shipping address fields
        $requiredFields = ['full_name', 'address', 'city', 'postal_code', 'province', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($shippingAddress[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                break 2;
            }
        }
        
        try {
            $pdo->beginTransaction();
            
            // Get cart items
            $stmt = $pdo->prepare("
                SELECT 
                    sc.id as cart_id,
                    sc.quantity,
                    p.id as product_id,
                    p.name,
                    p.price,
                    p.stock_quantity,
                    p.seller_id
                FROM cart_items sc
                JOIN products p ON sc.product_id = p.id
                WHERE sc.user_id = ? AND p.status = 'published'
                ORDER BY sc.created_at
            ");
            $stmt->execute([$userId]);
            $cartItems = $stmt->fetchAll();
            
            if (empty($cartItems)) {
                $pdo->rollBack();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cart is empty']);
                break;
            }
            
            // Validate stock and calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if ($item['stock_quantity'] < $item['quantity']) {
                    $pdo->rollBack();
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => "Insufficient stock for {$item['name']}",
                        'product_name' => $item['name'],
                        'requested' => intval($item['quantity']),
                        'available' => intval($item['stock_quantity'])
                    ]);
                    break 2;
                }
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            // Calculate costs
            $shippingCost = 50.00; // Fixed shipping cost for now
            $taxAmount = 0.00; // No tax for now
            $discountAmount = 0.00; // No discounts for now
            $totalAmount = $subtotal + $shippingCost + $taxAmount - $discountAmount;
            
            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Split full name
            $nameParts = explode(' ', $shippingAddress['full_name'], 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
            
            // Create order
            $stmt = $pdo->prepare("
                INSERT INTO orders (
                    order_number, user_id, status, subtotal, tax_amount, 
                    shipping_cost, discount_amount, total_amount, payment_method, 
                    payment_status, shipping_first_name, shipping_last_name, 
                    shipping_address_1, shipping_city, shipping_state, 
                    shipping_postal_code, shipping_phone, created_at
                ) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $orderNumber, $userId, $subtotal, $taxAmount, $shippingCost, 
                $discountAmount, $totalAmount, $paymentMethod, $firstName, $lastName,
                $shippingAddress['address'], $shippingAddress['city'], 
                $shippingAddress['province'], $shippingAddress['postal_code'], 
                $shippingAddress['phone']
            ]);
            
            $orderId = $pdo->lastInsertId();
            
            // Create order items and update stock
            foreach ($cartItems as $item) {
                // Add order item
                $stmt = $pdo->prepare("
                    INSERT INTO order_items (
                        order_id, product_id, seller_id, product_name, 
                        quantity, unit_price, total_price
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $itemTotal = $item['price'] * $item['quantity'];
                $stmt->execute([
                    $orderId, $item['product_id'], $item['seller_id'], 
                    $item['name'], $item['quantity'], $item['price'], $itemTotal
                ]);
                
                // Update product stock
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity - ? 
                    WHERE id = ?
                ");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Clear cart
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Create order status history
            $stmt = $pdo->prepare("
                INSERT INTO order_status_history (order_id, status, notes, created_at)
                VALUES (?, 'pending', 'Order placed', NOW())
            ");
            $stmt->execute([$orderId]);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => intval($orderId),
                    'order_number' => $orderNumber,
                    'total_amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending'
                ]
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()]);
        }
        break;
        
    case 'cancel':
        if ($requestMethod !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $orderId = $id;
        
        if (!$orderId || !is_numeric($orderId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid order ID required']);
            break;
        }
        
        try {
            // Check if order exists and belongs to user
            $stmt = $pdo->prepare("
                SELECT id, status, order_number 
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
            
            // Check if order can be cancelled
            if (!in_array($order['status'], ['pending', 'processing'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Order cannot be cancelled at this stage',
                    'current_status' => $order['status']
                ]);
                break;
            }
            
            $pdo->beginTransaction();
            
            // Update order status
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET status = 'cancelled', updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);
            
            // Restore stock quantities
            $stmt = $pdo->prepare("
                SELECT product_id, quantity 
                FROM order_items 
                WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll();
            
            foreach ($items as $item) {
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity + ? 
                    WHERE id = ?
                ");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Add status history
            $stmt = $pdo->prepare("
                INSERT INTO order_status_history (order_id, status, notes, created_at)
                VALUES (?, 'cancelled', 'Order cancelled by customer', NOW())
            ");
            $stmt->execute([$orderId]);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'order_number' => $order['order_number']
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false, 
            'message' => 'Orders endpoint not found',
            'debug' => [
                'action' => $action,
                'method' => $requestMethod,
                'user_id' => $userId
            ]
        ]);
}
?>