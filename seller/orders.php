<?php
$page_title = "Orders";

// Include necessary files
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/order.php';

// Initialize authentication
$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];
$orderManager = new OrderManager($pdo);

// Get orders with filters
$filters = [
    'status' => $_GET['status'] ?? null,
    'payment_status' => $_GET['payment_status'] ?? null,
    'search' => $_GET['search'] ?? null,
    'date_from' => $_GET['date_from'] ?? null,
    'date_to' => $_GET['date_to'] ?? null,
    'limit' => $_GET['limit'] ?? 20,
    'offset' => $_GET['offset'] ?? 0
];

// Get orders and stats
$ordersResult = $orderManager->getOrders($sellerId, $filters);
$statsResult = $orderManager->getOrderStats($sellerId);

$orders = $ordersResult['success'] ? $ordersResult['orders'] : [];
$stats = $statsResult['success'] ? $statsResult['stats'] : ['by_status' => []];
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Orders</h1>
                <p class="text-gray-600">Manage and track all your customer orders</p>
            </div>

            <!-- Order Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['by_status']['pending'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-truck text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Processing</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['by_status']['processing'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Delivered</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['by_status']['delivered'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Cancelled</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['by_status']['cancelled'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search orders..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo ($_GET['status'] ?? '') === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo ($_GET['status'] ?? '') === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo ($_GET['status'] ?? '') === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <select name="payment_status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" onchange="this.form.submit()">
                            <option value="">All Payments</option>
                            <option value="pending" <?php echo ($_GET['payment_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Payment Pending</option>
                            <option value="paid" <?php echo ($_GET['payment_status'] ?? '') === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="failed" <?php echo ($_GET['payment_status'] ?? '') === 'failed' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-beige text-white rounded-lg hover:bg-beige-dark transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <a href="orders.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-refresh mr-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                <?php if (empty($orders)): ?>
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No orders found</h3>
                        <p class="text-gray-600">You haven't received any orders yet. Keep promoting your products!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): 
                        // Status styling
                        $statusClasses = [
                            'pending' => 'bg-blue-100 text-blue-800',
                            'processing' => 'bg-yellow-100 text-yellow-800',
                            'shipped' => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-gray-100 text-gray-800'
                        ];
                        $statusClass = $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-800';
                        
                        // Payment status styling
                        $paymentStatusClasses = [
                            'pending' => 'text-yellow-600',
                            'paid' => 'text-green-600',
                            'failed' => 'text-red-600',
                            'refunded' => 'text-gray-600'
                        ];
                        $paymentStatusClass = $paymentStatusClasses[$order['payment_status']] ?? 'text-gray-600';
                    ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($order['order_number']); ?></h3>
                                        <p class="text-sm text-gray-600">
                                            Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                                        </p>
                                        <p class="text-sm <?php echo $paymentStatusClass; ?>">
                                            Payment: <?php echo ucfirst($order['payment_status']); ?>
                                        </p>
                                    </div>
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Customer</p>
                                        <p class="text-gray-900"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['email']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Your Total</p>
                                        <p class="text-xl font-bold text-beige">₱<?php echo number_format($order['seller_total'], 2); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo $order['item_count']; ?> item<?php echo $order['item_count'] > 1 ? 's' : ''; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Shipping Address</p>
                                        <p class="text-gray-900"><?php echo htmlspecialchars($order['shipping_address_1']); ?></p>
                                        <p class="text-sm text-gray-600">
                                            <?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' ' . $order['shipping_postal_code']); ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if ($order['tracking_number']): ?>
                                <div class="border-t pt-4">
                                    <h4 class="font-medium text-gray-900 mb-2">Tracking</h4>
                                    <p class="text-sm text-gray-600">
                                        <?php if ($order['courier_company']): ?>
                                            Courier: <?php echo htmlspecialchars($order['courier_company']); ?> |
                                        <?php endif; ?>
                                        Tracking #: <span class="font-mono text-beige"><?php echo htmlspecialchars($order['tracking_number']); ?></span>
                                    </p>
                                    <?php if ($order['estimated_delivery_date']): ?>
                                        <p class="text-sm text-gray-600">
                                            Expected delivery: <?php echo date('F j, Y', strtotime($order['estimated_delivery_date'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-4 lg:mt-0 lg:ml-6">
                                <div class="flex flex-col space-y-2">
                                    <a href="api/orders/detail.php?id=<?php echo $order['id']; ?>" class="btn-beige text-sm px-4 py-2 text-center">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                    
                                    <?php if ($order['status'] == 'pending' || $order['status'] == 'processing'): ?>
                                        <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'shipped')" class="btn-beige-outline text-sm px-4 py-2">
                                            <i class="fas fa-truck mr-2"></i>Mark as Shipped
                                        </button>
                                    <?php elseif ($order['status'] == 'shipped'): ?>
                                        <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'delivered')" class="btn-beige-outline text-sm px-4 py-2">
                                            <i class="fas fa-check mr-2"></i>Mark as Delivered
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($order['status'] != 'cancelled' && $order['status'] != 'delivered'): ?>
                                        <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')" class="px-4 py-2 text-sm border border-red-300 text-red-700 rounded-full hover:bg-red-50">
                                            <i class="fas fa-times mr-2"></i>Cancel Order
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (!empty($orders) && $ordersResult['total'] > $ordersResult['limit']): 
                $currentPage = floor($ordersResult['offset'] / $ordersResult['limit']) + 1;
                $totalPages = ceil($ordersResult['total'] / $ordersResult['limit']);
                $queryParams = $_GET;
            ?>
            <div class="mt-8 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Showing <?php echo $ordersResult['offset'] + 1; ?> to <?php echo min($ordersResult['offset'] + $ordersResult['limit'], $ordersResult['total']); ?>
                    of <?php echo $ordersResult['total']; ?> orders
                </p>
                <div class="flex items-center space-x-2">
                    <?php if ($currentPage > 1): 
                        $queryParams['offset'] = ($currentPage - 2) * $ordersResult['limit'];
                    ?>
                        <a href="?<?php echo http_build_query($queryParams); ?>" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): 
                        $queryParams['offset'] = ($i - 1) * $ordersResult['limit'];
                        if ($i == $currentPage): ?>
                            <span class="px-3 py-2 text-sm bg-beige text-white rounded-lg"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): 
                        $queryParams['offset'] = $currentPage * $ordersResult['limit'];
                    ?>
                        <a href="?<?php echo http_build_query($queryParams); ?>" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
    async function updateOrderStatus(orderId, status) {
        if (!confirm(`Are you sure you want to mark this order as ${status}?`)) {
            return;
        }
        
        try {
            const response = await fetch('api/orders/status.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    order_id: orderId,
                    status: status
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Order status updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error updating order status:', error);
            alert('An error occurred while updating the order status.');
        }
    }
    </script>

<?php include 'includes/footer.php'; ?>