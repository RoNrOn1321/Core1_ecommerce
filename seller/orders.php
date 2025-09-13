<?php
$page_title = "Orders";

// Include necessary files
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/order.php';
require_once 'includes/layout.php';

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

// Start layout
startLayout('Orders');
?>
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
                                    <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" class="btn-beige text-sm px-4 py-2">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </button>
                                    
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

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Order Details</h2>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <div id="orderDetailsContent">
                <div class="flex items-center justify-center py-12">
                    <i class="fas fa-spinner fa-spin text-3xl text-beige mr-3"></i>
                    <span class="text-lg text-gray-600">Loading order details...</span>
                </div>
            </div>
        </div>
    </div>
</div>

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

// Order Details Modal Functions
async function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const content = document.getElementById('orderDetailsContent');
    
    // Show modal and reset content
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <i class="fas fa-spinner fa-spin text-3xl text-beige mr-3"></i>
            <span class="text-lg text-gray-600">Loading order details...</span>
        </div>
    `;
    
    try {
        const response = await fetch(`api/orders/detail.php?id=${orderId}`, {
            credentials: 'include'
        });
        const result = await response.json();
        
        if (result.success) {
            displayOrderDetails(result.data);
        } else {
            content.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-red-600 mb-2">Error Loading Order</h3>
                    <p class="text-gray-600">${result.message}</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error fetching order details:', error);
        content.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-red-600 mb-2">Connection Error</h3>
                <p class="text-gray-600">Failed to load order details. Please try again.</p>
            </div>
        `;
    }
}

function displayOrderDetails(order) {
    const content = document.getElementById('orderDetailsContent');
    
    const statusColors = {
        'pending': 'bg-blue-100 text-blue-800',
        'processing': 'bg-yellow-100 text-yellow-800',
        'shipped': 'bg-purple-100 text-purple-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'refunded': 'bg-gray-100 text-gray-800'
    };
    
    content.innerHTML = `
        <!-- Order Header -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">${order.order_number}</h3>
                    <p class="text-gray-600 mt-1">Placed on ${new Date(order.created_at).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                        ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                    <span class="text-2xl font-bold text-beige">₱${parseFloat(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                </div>
            </div>
        </div>

        <!-- Order Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Customer Information -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-beige mr-2"></i>
                    Customer Information
                </h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">${order.first_name} ${order.last_name}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">${order.email}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium">${order.phone || 'Not provided'}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-credit-card text-beige mr-2"></i>
                    Payment Information
                </h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="font-medium">${order.payment_method.toUpperCase()}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payment Status</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${order.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                            ${order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1)}
                        </span>
                    </div>
                    ${order.payment_reference ? `
                        <div>
                            <p class="text-sm text-gray-600">Reference</p>
                            <p class="font-mono text-sm">${order.payment_reference}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-shipping-fast text-beige mr-2"></i>
                Shipping Address
            </h4>
            <div class="text-gray-700">
                <p class="font-medium">${order.shipping_first_name} ${order.shipping_last_name}</p>
                ${order.shipping_company ? `<p>${order.shipping_company}</p>` : ''}
                <p>${order.shipping_address_1}</p>
                ${order.shipping_address_2 ? `<p>${order.shipping_address_2}</p>` : ''}
                <p>${order.shipping_city}, ${order.shipping_state} ${order.shipping_postal_code}</p>
                ${order.shipping_country ? `<p>${order.shipping_country}</p>` : ''}
                ${order.shipping_phone ? `<p class="mt-2"><i class="fas fa-phone text-beige mr-1"></i> ${order.shipping_phone}</p>` : ''}
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-box text-beige mr-2"></i>
                Order Items
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        ${order.items.map(item => `
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        ${item.product_image ? `
                                            <img src="${item.product_image}" alt="${item.product_name}" 
                                                 class="w-16 h-16 object-cover rounded-lg mr-4"
                                                 onerror="this.style.display='none'">
                                        ` : ''}
                                        <div>
                                            <p class="font-medium text-gray-900">${item.product_name}</p>
                                            ${item.product_sku ? `<p class="text-sm text-gray-500">SKU: ${item.product_sku}</p>` : ''}
                                            ${item.variant_details ? `<p class="text-sm text-gray-500">${item.variant_details}</p>` : ''}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-gray-900">${item.quantity}</td>
                                <td class="px-4 py-4 text-gray-900">₱${parseFloat(item.unit_price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="px-4 py-4 font-medium text-gray-900">₱${parseFloat(item.total_price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h4>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span>₱${parseFloat(order.subtotal).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Shipping</span>
                    <span>₱${parseFloat(order.shipping_cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tax</span>
                    <span>₱${parseFloat(order.tax_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                </div>
                ${order.discount_amount > 0 ? `
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span>-₱${parseFloat(order.discount_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                    </div>
                ` : ''}
                <div class="border-t pt-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-beige">₱${parseFloat(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status History -->
        ${order.status_history && order.status_history.length > 0 ? `
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-beige mr-2"></i>
                    Order History
                </h4>
                <div class="space-y-4">
                    ${order.status_history.map((history, index) => `
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 rounded-full ${index === 0 ? 'bg-beige' : 'bg-gray-300'}"></div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 capitalize">${history.status}</p>
                                    <p class="text-sm text-gray-500">${new Date(history.created_at).toLocaleString()}</p>
                                </div>
                                ${history.notes ? `<p class="text-sm text-gray-600 mt-1">${history.notes}</p>` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        ` : ''}

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            ${order.status === 'pending' || order.status === 'processing' ? `
                <button onclick="updateOrderStatusFromModal(${order.id}, 'shipped')" class="btn-beige">
                    <i class="fas fa-truck mr-2"></i>Mark as Shipped
                </button>
            ` : ''}
            ${order.status === 'shipped' ? `
                <button onclick="updateOrderStatusFromModal(${order.id}, 'delivered')" class="btn-beige">
                    <i class="fas fa-check mr-2"></i>Mark as Delivered
                </button>
            ` : ''}
            ${order.status !== 'cancelled' && order.status !== 'delivered' ? `
                <button onclick="updateOrderStatusFromModal(${order.id}, 'cancelled')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-times mr-2"></i>Cancel Order
                </button>
            ` : ''}
        </div>
    `;
}

function closeOrderModal() {
    document.getElementById('orderDetailsModal').classList.add('hidden');
}

async function updateOrderStatusFromModal(orderId, status) {
    if (await updateOrderStatus(orderId, status)) {
        // Refresh the modal content
        setTimeout(() => viewOrderDetails(orderId), 1000);
    }
}

// Close modal when clicking outside
document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOrderModal();
    }
});
</script>

<?php endLayout(); ?>