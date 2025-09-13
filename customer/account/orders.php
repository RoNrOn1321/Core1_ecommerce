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

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Write Product Reviews</h2>
                    <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div id="reviewModalContent" class="p-6">
                <!-- Review items will be loaded here -->
            </div>
        </div>
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
                ${order.status === 'delivered' ? `
                    <button onclick="showReviewModal(${order.id})" 
                            class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-star mr-2"></i>
                        Write Review
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
                
                ${order.status === 'delivered' ? `
                    <div class="mt-4">
                        <button onclick="showReviewModal(${order.id})" 
                                class="w-full px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                            <i class="fas fa-star mr-2"></i>
                            Write Reviews
                        </button>
                    </div>
                ` : ''}
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

// Review Modal Functions
async function showReviewModal(orderId) {
    try {
        const response = await customerAPI.reviews.getReviewableItems(orderId);
        
        if (response.success) {
            renderReviewModal(response.data, orderId);
            document.getElementById('reviewModal').classList.remove('hidden');
        } else {
            if (response.message === 'Authentication required') {
                showError('Please log in to write reviews');
                setTimeout(() => {
                    window.location.href = '../login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
                }, 2000);
            } else {
                showError('Failed to load reviewable items: ' + response.message);
            }
        }
        
    } catch (error) {
        console.error('Failed to load reviewable items:', error);
        showError('Network error. Please check your connection and try again.');
    }
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

function renderReviewModal(items, orderId) {
    const container = document.getElementById('reviewModalContent');
    
    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-star text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No items to review</h3>
                <p class="text-gray-500">All items from this order have been reviewed.</p>
            </div>
        `;
        return;
    }
    
    const itemsHTML = items.map(item => `
        <div class="border border-gray-200 rounded-lg p-6 mb-6">
            <div class="flex items-start space-x-4 mb-4">
                <img src="${getProductImage(item.product_image)}" 
                     alt="${item.product_name}" class="w-16 h-16 object-cover rounded-lg">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${item.product_name}</h4>
                    <p class="text-sm text-gray-600">Store: ${item.store_name || 'N/A'}</p>
                    ${item.review_id ? `
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Reviewed on ${new Date(item.review_date).toLocaleDateString()}
                            </span>
                        </div>
                    ` : ''}
                </div>
            </div>
            
            ${item.review_id ? `
                <!-- Existing Review -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="flex items-center">
                            ${generateStarRating(item.rating, false)}
                        </div>
                        <span class="ml-2 text-sm text-gray-600">${item.rating}/5</span>
                    </div>
                    <h5 class="font-medium text-gray-900 mb-1">${item.review_title}</h5>
                    <p class="text-gray-700 text-sm">${item.review_text || 'No additional comments'}</p>
                    <div class="mt-3 flex gap-2">
                        <button onclick="editReview(${item.review_id}, ${item.product_id}, '${item.product_name}')" 
                                class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Edit Review
                        </button>
                        <button onclick="deleteReview(${item.review_id}, ${item.product_id})" 
                                class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-1"></i>
                            Delete Review
                        </button>
                    </div>
                </div>
            ` : `
                <!-- Review Form -->
                <form onsubmit="submitReview(event, ${item.product_id}, ${orderId})" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex items-center space-x-1" data-rating="0">
                            ${generateStarRating(0, true)}
                        </div>
                        <input type="hidden" name="rating" value="0" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review Title</label>
                        <input type="text" name="title" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                               placeholder="Summarize your experience">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review Details (Optional)</label>
                        <textarea name="review_text" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                  placeholder="Share more about your experience with this product"></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                            <i class="fas fa-star mr-2"></i>
                            Submit Review
                        </button>
                    </div>
                </form>
            `}
        </div>
    `).join('');
    
    container.innerHTML = itemsHTML;
    
    // Initialize star ratings
    initializeStarRatings();
}

function generateStarRating(rating, interactive = false) {
    let starsHTML = '';
    for (let i = 1; i <= 5; i++) {
        const filled = i <= rating;
        const classes = interactive 
            ? `cursor-pointer text-2xl hover:text-yellow-400 transition-colors ${filled ? 'text-yellow-400' : 'text-gray-300'}`
            : `text-lg ${filled ? 'text-yellow-400' : 'text-gray-300'}`;
        
        if (interactive) {
            starsHTML += `<i class="fas fa-star ${classes}" data-rating="${i}" onclick="setRating(this, ${i})"></i>`;
        } else {
            starsHTML += `<i class="fas fa-star ${classes}"></i>`;
        }
    }
    return starsHTML;
}

function initializeStarRatings() {
    // Add hover effects to star ratings
    document.querySelectorAll('[data-rating]').forEach(container => {
        const stars = container.querySelectorAll('.fas.fa-star');
        
        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                highlightStars(stars, index + 1);
            });
        });
        
        container.addEventListener('mouseleave', () => {
            const currentRating = parseInt(container.dataset.rating);
            highlightStars(stars, currentRating);
        });
    });
}

function highlightStars(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('text-yellow-400');
            star.classList.remove('text-gray-300');
        } else {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        }
    });
}

function setRating(starElement, rating) {
    const container = starElement.closest('[data-rating]');
    const form = starElement.closest('form');
    
    container.dataset.rating = rating;
    form.querySelector('input[name="rating"]').value = rating;
    
    const stars = container.querySelectorAll('.fas.fa-star');
    highlightStars(stars, rating);
}

async function submitReview(event, productId, orderId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    const reviewData = {
        product_id: productId,
        order_id: orderId,
        rating: parseInt(formData.get('rating')),
        title: formData.get('title'),
        review_text: formData.get('review_text')
    };
    
    if (reviewData.rating === 0) {
        showError('Please select a rating');
        return;
    }
    
    try {
        const response = await customerAPI.reviews.submitReview(reviewData);
        
        if (response.success) {
            showSuccess('Review submitted successfully');
            // Refresh the review modal
            showReviewModal(orderId);
        } else {
            showError('Failed to submit review: ' + response.message);
        }
        
    } catch (error) {
        console.error('Failed to submit review:', error);
        showError('Failed to submit review');
    }
}

async function editReview(reviewId, productId, productName) {
    // Implementation for editing reviews - could open a separate modal or inline edit
    showError('Edit functionality coming soon');
}

async function deleteReview(reviewId, productId) {
    if (!confirm('Are you sure you want to delete this review?')) {
        return;
    }
    
    try {
        const response = await customerAPI.reviews.deleteReview(reviewId);
        
        if (response.success) {
            showSuccess('Review deleted successfully');
            // Refresh the current view
            const urlOrderId = new URLSearchParams(window.location.search).get('order');
            if (urlOrderId) {
                showReviewModal(urlOrderId);
            }
        } else {
            showError('Failed to delete review: ' + response.message);
        }
        
    } catch (error) {
        console.error('Failed to delete review:', error);
        showError('Failed to delete review');
    }
}
</script>

</body>
</html>