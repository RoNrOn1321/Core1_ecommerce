<?php
// Customer Cart API
require_once '../auth/functions.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check authentication for most cart operations
$requiresAuth = !in_array($action, ['session']);
if ($requiresAuth && !$customerAuth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$customerId = $_SESSION['customer_id'] ?? null;

switch ($action) {
    case 'add':
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $productId = $input['product_id'] ?? null;
        $quantity = max(1, intval($input['quantity'] ?? 1));
        
        if (!$productId || !is_numeric($productId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid product ID required']);
            break;
        }
        
        try {
            // Check if product exists and is available
            $stmt = $pdo->prepare("
                SELECT id, name, price, stock_quantity 
                FROM products 
                WHERE id = ? AND is_active = 1
            ");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            
            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                break;
            }
            
            if ($product['stock_quantity'] < $quantity) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Insufficient stock',
                    'available_stock' => intval($product['stock_quantity'])
                ]);
                break;
            }
            
            // Check if item already in cart
            $stmt = $pdo->prepare("
                SELECT id, quantity 
                FROM shopping_cart 
                WHERE customer_id = ? AND product_id = ?
            ");
            $stmt->execute([$customerId, $productId]);
            $existingItem = $stmt->fetch();
            
            if ($existingItem) {
                // Update existing item
                $newQuantity = $existingItem['quantity'] + $quantity;
                
                if ($newQuantity > $product['stock_quantity']) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Total quantity would exceed stock',
                        'current_in_cart' => intval($existingItem['quantity']),
                        'available_stock' => intval($product['stock_quantity'])
                    ]);
                    break;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE shopping_cart 
                    SET quantity = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([$newQuantity, $existingItem['id']]);
                
            } else {
                // Add new item
                $stmt = $pdo->prepare("
                    INSERT INTO shopping_cart (customer_id, product_id, quantity)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$customerId, $productId, $quantity]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Item added to cart',
                'product' => [
                    'id' => intval($product['id']),
                    'name' => $product['name'],
                    'price' => floatval($product['price'])
                ],
                'quantity_added' => $quantity
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to add item to cart: ' . $e->getMessage()]);
        }
        break;
        
    case '':
    case 'list':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            $sql = "
                SELECT 
                    sc.id as cart_id,
                    sc.quantity,
                    sc.created_at as added_at,
                    p.id as product_id,
                    p.name,
                    p.price,
                    p.stock_quantity,
                    (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) as image
                FROM shopping_cart sc
                JOIN products p ON sc.product_id = p.id
                WHERE sc.customer_id = ? AND p.is_active = 1
                ORDER BY sc.created_at DESC
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$customerId]);
            $cartItems = $stmt->fetchAll();
            
            $totalAmount = 0;
            $totalItems = 0;
            
            foreach ($cartItems as &$item) {
                $item['price'] = floatval($item['price']);
                $item['quantity'] = intval($item['quantity']);
                $item['stock_quantity'] = intval($item['stock_quantity']);
                $item['subtotal'] = $item['price'] * $item['quantity'];
                $item['in_stock'] = $item['stock_quantity'] >= $item['quantity'];
                
                $totalAmount += $item['subtotal'];
                $totalItems += $item['quantity'];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $cartItems,
                'summary' => [
                    'total_items' => $totalItems,
                    'total_amount' => round($totalAmount, 2),
                    'currency' => 'PHP'
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch cart: ' . $e->getMessage()]);
        }
        break;
        
    case 'update':
        if ($requestMethod !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $cartId = $input['cart_id'] ?? $id;
        $quantity = intval($input['quantity'] ?? 0);
        
        if (!$cartId || !is_numeric($cartId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid cart ID required']);
            break;
        }
        
        if ($quantity < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Quantity cannot be negative']);
            break;
        }
        
        try {
            // Get cart item and check ownership
            $stmt = $pdo->prepare("
                SELECT sc.id, sc.product_id, p.stock_quantity, p.name
                FROM shopping_cart sc
                JOIN products p ON sc.product_id = p.id
                WHERE sc.id = ? AND sc.customer_id = ?
            ");
            $stmt->execute([$cartId, $customerId]);
            $cartItem = $stmt->fetch();
            
            if (!$cartItem) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cart item not found']);
                break;
            }
            
            if ($quantity === 0) {
                // Remove item
                $stmt = $pdo->prepare("DELETE FROM shopping_cart WHERE id = ?");
                $stmt->execute([$cartId]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Item removed from cart'
                ]);
            } else {
                // Update quantity
                if ($quantity > $cartItem['stock_quantity']) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Quantity exceeds available stock',
                        'available_stock' => intval($cartItem['stock_quantity'])
                    ]);
                    break;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE shopping_cart 
                    SET quantity = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([$quantity, $cartId]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Cart item updated',
                    'product_name' => $cartItem['name'],
                    'new_quantity' => $quantity
                ]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update cart item: ' . $e->getMessage()]);
        }
        break;
        
    case 'remove':
        if ($requestMethod !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $cartId = $id;
        
        if (!$cartId || !is_numeric($cartId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid cart ID required']);
            break;
        }
        
        try {
            $stmt = $pdo->prepare("
                DELETE FROM shopping_cart 
                WHERE id = ? AND customer_id = ?
            ");
            $stmt->execute([$cartId, $customerId]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Item removed from cart'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Cart item not found']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to remove cart item: ' . $e->getMessage()]);
        }
        break;
        
    case 'clear':
        if ($requestMethod !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            $stmt = $pdo->prepare("DELETE FROM shopping_cart WHERE customer_id = ?");
            $stmt->execute([$customerId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart cleared',
                'items_removed' => $stmt->rowCount()
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to clear cart: ' . $e->getMessage()]);
        }
        break;
        
    case 'count':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as item_count, SUM(quantity) as total_quantity
                FROM shopping_cart 
                WHERE customer_id = ?
            ");
            $stmt->execute([$customerId]);
            $result = $stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'item_count' => intval($result['item_count']),
                    'total_quantity' => intval($result['total_quantity'] ?? 0)
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to get cart count: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Cart endpoint not found']);
}
?>