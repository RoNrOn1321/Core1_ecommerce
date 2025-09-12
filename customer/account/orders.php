<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Core1 E-commerce</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gray-50">

<?php 
// Require authentication
require_once '../auth/functions.php';
requireLogin();

// Get order ID if provided
$orderId = $_GET['order'] ?? null;
?>

<?php include '../components/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <?php if ($orderId): ?>
                    Order Confirmation
                <?php else: ?>
                    My Orders
                <?php endif; ?>
            </h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <a href="../index.php" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-home"></i>
                            <span class="sr-only">Home</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-4"></i>
                            <a href="../account/orders.php" class="text-gray-500 hover:text-gray-700">Account</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-4"></i>
                            <?php if ($orderId): ?>
                                <a href="orders.php" class="text-gray-500 hover:text-gray-700">Orders</a>
                            <?php else: ?>
                                <span class="text-gray-700 font-medium">Orders</span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php if ($orderId): ?>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-4"></i>
                            <span class="text-gray-700 font-medium">Order Details</span>
                        </div>
                    </li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>

        <!-- Account Navigation -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-wrap gap-4">
                <a href="orders.php" class="flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg">
                    <i class="fas fa-box mr-2"></i>
                    My Orders
                </a>
                <a href="addresses.php" class="flex items-center px-4 py-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Addresses
                </a>
                <a href="profile.php" class="flex items-center px-4 py-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                    <i class="fas fa-user-edit mr-2"></i>
                    Profile
                </a>
            </div>
        </div>

        <?php if ($orderId): ?>
        <!-- Order Confirmation/Details -->
        <div id="orderDetails" class="space-y-6">
            <!-- Order confirmation will be loaded here -->
        </div>
        <?php else: ?>
        <!-- Orders List -->
        <div class="space-y-6">
            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <label for="statusFilter" class="text-sm font-medium text-gray-700">Status:</label>
                        <select id="statusFilter" onchange="loadOrders()" 
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <option value="">All Orders</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <button onclick="loadOrders()" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                </div>
            </div>

            <!-- Orders List -->
            <div id="ordersList" class="space-y-4">
                <!-- Orders will be loaded here -->
            </div>

            <!-- Pagination -->
            <div id="pagination" class="flex justify-center mt-8">
                <!-- Pagination will be loaded here -->
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="../assets/js/customer-api.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = <?php echo $orderId ? json_encode($orderId) : 'null'; ?>;
    
    if (orderId) {
        loadOrderDetails(orderId);
    } else {
        loadOrders();
    }
});

let currentPage = 1;
let currentStatus = '';

async function loadOrders(page = 1) {
    try {
        currentPage = page;
        currentStatus = document.getElementById('statusFilter')?.value || '';
        
        const params = {
            page: currentPage,
            limit: 10
        };
        
        if (currentStatus) {
            params.status = currentStatus;
        }
        
        const response = await customerAPI.orders.getAll(params);
        
        if (response.success) {
            renderOrdersList(response.data);
            renderPagination(response.pagination);
        } else {
            showError('Failed to load orders: ' + response.message);
        }
        
    } catch (error) {
        console.error('Failed to load orders:', error);
        showError('Failed to load orders');
    }
}

async function loadOrderDetails(orderId) {
    try {
        const response = await customerAPI.orders.getById(orderId);
        
        if (response.success) {
            renderOrderDetails(response.data);
        } else {
            showError('Failed to load order details: ' + response.message);
        }
        
    } catch (error) {
        console.error('Failed to load order details:', error);
        showError('Failed to load order details');
    }
}

function renderOrdersList(orders) {
    const container = document.getElementById('ordersList');
    
    if (!orders || orders.length === 0) {
        container.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
                <p class="text-gray-500 mb-4">You haven't placed any orders yet.</p>
                <a href="../products.php" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Start Shopping
                </a>
            </div>
        `;
        return;
    }
    
    const ordersHTML = orders.map(order => `
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Order #${order.order_number}</h3>
                    <p class="text-sm text-gray-500">Placed on ${new Date(order.created_at).toLocaleDateString()}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusClass(order.status)}">
                        ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Items</p>
                    <p class="font-medium text-gray-900">${order.item_count} item(s)</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="font-medium text-gray-900">₱${order.total_amount.toFixed(2)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Payment</p>
                    <p class="font-medium text-gray-900">${order.payment_method.toUpperCase()}</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <a href="orders.php?order=${order.id}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    View Details
                </a>
                ${order.status === 'pending' || order.status === 'processing' ? `
                    <button onclick="cancelOrder(${order.id}, '${order.order_number}')" 
                            class="inline-flex items-center px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Cancel Order
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    container.innerHTML = ordersHTML;
}

function renderOrderDetails(order) {
    const container = document.getElementById('orderDetails');
    
    const itemsHTML = order.items.map(item => `
        <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0">
            <div class="flex items-center space-x-4">
                <img src="${getProductImage(item.product_image)}" 
                     alt="${item.product_name}" class="w-16 h-16 object-cover rounded-lg">
                <div>
                    <h4 class="font-medium text-gray-900">${item.product_name}</h4>
                    <p class="text-sm text-gray-600">Store: ${item.store_name || 'N/A'}</p>
                    <p class="text-sm text-gray-600">₱${item.unit_price.toFixed(2)} × ${item.quantity}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="font-semibold text-gray-900">₱${item.total_price.toFixed(2)}</span>
            </div>
        </div>
    `).join('');
    
    const detailsHTML = `
        <!-- Order Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-2xl mr-4"></i>
                <div>
                    <h2 class="text-lg font-semibold text-green-900">Order Placed Successfully!</h2>
                    <p class="text-green-700">Your order #${order.order_number} has been received and is being processed.</p>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        ${itemsHTML}
                    </div>
                </div>
                
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                    <div class="text-gray-700">
                        <p class="font-medium">${order.shipping_first_name} ${order.shipping_last_name}</p>
                        <p>${order.shipping_address_1}</p>
                        <p>${order.shipping_city}, ${order.shipping_state} ${order.shipping_postal_code}</p>
                        <p>Phone: ${order.shipping_phone}</p>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6 h-fit">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium">#${order.order_number}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Date:</span>
                        <span class="font-medium">${new Date(order.created_at).toLocaleDateString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium ${getStatusClass(order.status)}">
                            ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium">${order.payment_method.toUpperCase()}</span>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span>₱${order.subtotal.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping:</span>
                        <span>₱${order.shipping_cost.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax:</span>
                        <span>₱${order.tax_amount.toFixed(2)}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total:</span>
                            <span class="text-amber-600">₱${order.total_amount.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3">
                    ${order.status === 'pending' || order.status === 'processing' ? `
                        <button onclick="cancelOrder(${order.id}, '${order.order_number}')" 
                                class="w-full px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Cancel Order
                        </button>
                    ` : ''}
                    <a href="orders.php" 
                       class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-list mr-2"></i>
                        View All Orders
                    </a>
                    <a href="../products.php" 
                       class="block w-full px-4 py-2 bg-amber-600 text-white text-center rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = detailsHTML;
}

function renderPagination(pagination) {
    const container = document.getElementById('pagination');
    
    if (!pagination || pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    const { current_page, total_pages } = pagination;
    let paginationHTML = '<div class="flex items-center space-x-2">';
    
    // Previous button
    if (current_page > 1) {
        paginationHTML += `
            <button onclick="loadOrders(${current_page - 1})" 
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
    }
    
    // Page numbers
    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(total_pages, current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const isActive = i === current_page;
        paginationHTML += `
            <button onclick="loadOrders(${i})" 
                    class="px-3 py-2 rounded-lg transition-colors ${isActive ? 'bg-amber-600 text-white' : 'border border-gray-300 hover:bg-gray-50'}">
                ${i}
            </button>
        `;
    }
    
    // Next button
    if (current_page < total_pages) {
        paginationHTML += `
            <button onclick="loadOrders(${current_page + 1})" 
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
    }
    
    paginationHTML += '</div>';
    container.innerHTML = paginationHTML;
}

async function cancelOrder(orderId, orderNumber) {
    if (!confirm(`Are you sure you want to cancel order #${orderNumber}?`)) {
        return;
    }
    
    try {
        const response = await customerAPI.orders.updateStatus(orderId, 'cancelled');
        
        if (response.success) {
            showSuccess('Order cancelled successfully');
            
            // Reload the current view
            const urlOrderId = new URLSearchParams(window.location.search).get('order');
            if (urlOrderId) {
                loadOrderDetails(urlOrderId);
            } else {
                loadOrders(currentPage);
            }
        } else {
            showError('Failed to cancel order: ' + response.message);
        }
        
    } catch (error) {
        console.error('Failed to cancel order:', error);
        showError('Failed to cancel order');
    }
}

function getStatusClass(status) {
    const statusClasses = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'shipped': 'bg-purple-100 text-purple-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'refunded': 'bg-gray-100 text-gray-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
}

function getProductImage(imageUrl) {
    if (!imageUrl) return '../images/no-image.png';
    
    if (imageUrl.startsWith('http') || imageUrl.startsWith('/')) {
        return imageUrl;
    }
    
    return `/Core1_ecommerce/uploads/${imageUrl}`;
}

function showError(message) {
    customerAPI.utils.showNotification(message, 'error');
}

function showSuccess(message) {
    customerAPI.utils.showNotification(message, 'success');
}
</script>

</body>
</html>