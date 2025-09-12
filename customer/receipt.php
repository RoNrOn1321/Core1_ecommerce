<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Core1 E-commerce</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-container { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-50">

<?php 
// Require authentication
require_once 'auth/functions.php';
requireLogin();

// Get order ID from URL
$orderId = $_GET['order'] ?? null;

if (!$orderId || !is_numeric($orderId)) {
    header('Location: index.php');
    exit;
}

// Get order details
require_once 'config/database.php';

try {
    // Get order with customer details
    $stmt = $pdo->prepare("
        SELECT 
            o.*,
            u.first_name,
            u.last_name,
            u.email,
            pt.payment_intent_id,
            pt.paymongo_payment_id,
            pt.created_at as payment_created_at
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN payment_transactions pt ON o.id = pt.order_id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $_SESSION['customer_id']]);
    $order = $stmt->fetch();
    
    if (!$order) {
        header('Location: index.php');
        exit;
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
    
} catch (Exception $e) {
    header('Location: index.php');
    exit;
}
?>

<div class="no-print"><?php include 'components/navbar.php'; ?></div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Print Actions (No Print) -->
        <div class="no-print mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Order Receipt</h1>
            <div class="space-x-4">
                <button onclick="window.print()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </button>
                <a href="account/orders.php" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-list mr-2"></i>My Orders
                </a>
            </div>
        </div>

        <!-- Receipt Container -->
        <div class="print-container bg-white rounded-lg shadow-lg p-8 border">
            <!-- Header -->
            <div class="text-center mb-8 border-b pb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">CORE1 E-COMMERCE</h1>
                <p class="text-gray-600">Your trusted online marketplace</p>
                <div class="mt-4">
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                        <?php echo strtoupper($order['payment_status']); ?>
                    </span>
                </div>
            </div>

            <!-- Order & Customer Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Order Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Number:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($order['order_number']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Date:</span>
                            <span class="font-medium"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-medium"><?php echo strtoupper($order['payment_method']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Status:</span>
                            <span class="font-medium"><?php echo ucfirst($order['status']); ?></span>
                        </div>
                        <?php if ($order['payment_intent_id']): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment ID:</span>
                            <span class="font-medium text-xs"><?php echo htmlspecialchars($order['payment_intent_id']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer & Shipping Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium ml-2"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium ml-2"><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                    </div>

                    <h4 class="text-md font-semibold text-gray-900 mt-6 mb-3">Shipping Address</h4>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        <div><?php echo htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']); ?></div>
                        <div><?php echo htmlspecialchars($order['shipping_address_1']); ?></div>
                        <?php if ($order['shipping_address_2']): ?>
                        <div><?php echo htmlspecialchars($order['shipping_address_2']); ?></div>
                        <?php endif; ?>
                        <div><?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' ' . $order['shipping_postal_code']); ?></div>
                        <?php if ($order['shipping_phone']): ?>
                        <div>Phone: <?php echo htmlspecialchars($order['shipping_phone']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold">Product</th>
                                <th class="border border-gray-300 px-4 py-3 text-center text-sm font-semibold">Qty</th>
                                <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold">Unit Price</th>
                                <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <?php if ($item['product_image']): ?>
                                        <?php
                                        // Handle product image URL properly
                                        $imageUrl = $item['product_image'];
                                        if (strpos($imageUrl, 'http') === 0 || strpos($imageUrl, '/') === 0) {
                                            $imageSrc = $imageUrl;
                                        } else {
                                            $imageSrc = '/Core1_ecommerce/uploads/' . $imageUrl;
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imageSrc); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                             class="w-12 h-12 object-cover rounded"
                                             onerror="this.src='/Core1_ecommerce/customer/images/no-image.png'">
                                        <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="font-medium text-sm"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                            <?php if ($item['store_name']): ?>
                                            <div class="text-xs text-gray-500">by <?php echo htmlspecialchars($item['store_name']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-center"><?php echo intval($item['quantity']); ?></td>
                                <td class="border border-gray-300 px-4 py-3 text-right">₱<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td class="border border-gray-300 px-4 py-3 text-right font-medium">₱<?php echo number_format($item['total_price'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t pt-6">
                <div class="flex justify-end">
                    <div class="w-full max-w-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span>₱<?php echo number_format($order['subtotal'], 2); ?></span>
                            </div>
                            <?php if ($order['discount_amount'] > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Discount:</span>
                                <span class="text-green-600">-₱<?php echo number_format($order['discount_amount'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping:</span>
                                <span>₱<?php echo number_format($order['shipping_cost'], 2); ?></span>
                            </div>
                            <?php if ($order['tax_amount'] > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax:</span>
                                <span>₱<?php echo number_format($order['tax_amount'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="border-t border-gray-300 pt-2">
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Total:</span>
                                    <span class="text-blue-600">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t text-center text-sm text-gray-500">
                <p>Thank you for shopping with Core1 E-commerce!</p>
                <p class="mt-2">For questions about this order, please contact our support team.</p>
                <?php if ($order['payment_method'] === 'gcash' && $order['payment_status'] === 'paid'): ?>
                <p class="mt-4 text-green-600 font-medium">
                    <i class="fas fa-check-circle mr-1"></i>
                    Payment confirmed via GCash
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons (No Print) -->
        <div class="no-print mt-6 text-center space-x-4">
            <a href="products.php" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
            </a>
            <a href="account/orders.php" 
               class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-list mr-2"></i>View All Orders
            </a>
        </div>
    </div>
</div>

<script>
// Auto-print on load if print parameter is present
if (new URLSearchParams(window.location.search).has('print')) {
    window.onload = function() {
        setTimeout(() => {
            window.print();
        }, 1000);
    };
}
</script>

</body>
</html>