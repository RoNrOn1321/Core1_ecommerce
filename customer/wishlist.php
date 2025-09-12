<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php?redirect=wishlist');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Lumino</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Include Navbar -->
    <?php include 'components/navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-heart text-red-500 mr-3"></i>
                        My Wishlist
                    </h1>
                    <p class="text-gray-600 mt-2">Save your favorite products for later</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900" id="wishlistItemCount">0</div>
                        <div class="text-sm text-gray-500">Items</div>
                    </div>
                    <button onclick="clearWishlist()" id="clearWishlistBtn" class="hidden bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Clear All
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="wishlistLoading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
            <p class="text-gray-600 mt-4">Loading your wishlist...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyWishlist" class="hidden text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="text-6xl text-gray-300 mb-6">
                    <i class="fas fa-heart-broken"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Your wishlist is empty</h3>
                <p class="text-gray-600 mb-8">Start adding products you love to your wishlist!</p>
                <a href="products.php" class="inline-flex items-center px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Browse Products
                </a>
            </div>
        </div>

        <!-- Wishlist Items Grid -->
        <div id="wishlistItems" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Items will be loaded here -->
        </div>

        <!-- Error State -->
        <div id="wishlistError" class="hidden text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="text-6xl text-red-300 mb-6">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Error Loading Wishlist</h3>
                <p class="text-gray-600 mb-8" id="errorMessage">Something went wrong while loading your wishlist.</p>
                <button onclick="loadWishlist()" class="inline-flex items-center px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>
                    Try Again
                </button>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Action</h3>
            <p id="confirmMessage" class="text-gray-600 mb-6"></p>
            <div class="flex justify-end space-x-3">
                <button onclick="hideConfirmModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button id="confirmButton" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Load API script before our script -->
    <script src="assets/js/customer-api.js"></script>
    <script>
        let wishlistData = [];

        // Fallback wishlist API if main API fails
        const wishlistAPI = {
            baseURL: '/Core1_ecommerce/customer/api/wishlist.php',
            
            async request(method, endpoint, data = null) {
                const url = `${this.baseURL}${endpoint}`;
                
                const config = {
                    method: method.toUpperCase(),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                };

                if (data && ['POST', 'PUT', 'PATCH'].includes(config.method)) {
                    config.body = JSON.stringify(data);
                }

                try {
                    const response = await fetch(url, config);
                    const contentType = response.headers.get('content-type');
                    let responseData;
                    
                    if (contentType && contentType.includes('application/json')) {
                        responseData = await response.json();
                    } else {
                        const textResponse = await response.text();
                        responseData = { success: false, message: textResponse };
                    }

                    responseData._status = response.status;
                    responseData._ok = response.ok;
                    return responseData;
                    
                } catch (error) {
                    return {
                        success: false,
                        message: 'Network error occurred',
                        error: error.message
                    };
                }
            },

            async getItems() {
                return this.request('GET', ''); // Empty path for main endpoint
            },

            async getCount() {
                return this.request('GET', '/count');
            },

            async addItem(productId) {
                return this.request('POST', '/add', { product_id: productId });
            },

            async removeItem(wishlistId) {
                return this.request('DELETE', `/remove/${wishlistId}`);
            },

            async clear() {
                return this.request('DELETE', '/clear');
            }
        };

        // Load wishlist on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit for the API to fully initialize
            setTimeout(async function() {
                // Check login status first
                const isLoggedIn = await checkLoginStatus();
                
                if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                    loadWishlist();
                } else {
                    loadWishlist();
                }
                
                // Force update navbar counts regardless of which API we use
                setTimeout(() => {
                    updateNavbarWishlistCount();
                }, 2000);
                
                // Manual test of API to update navbar counts
                setTimeout(async () => {
                    try {
                        const response = await fetch('/Core1_ecommerce/customer/api/wishlist.php/count', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        const data = await response.json();
                        
                        if (data.success && data.data && data.data.count) {
                            const wishlistCountElement = document.getElementById('wishlistCount');
                            if (wishlistCountElement) {
                                wishlistCountElement.textContent = data.data.count;
                                // Force styling to make sure it's visible
                                wishlistCountElement.style.display = 'flex';
                                wishlistCountElement.style.visibility = 'visible';
                                wishlistCountElement.style.opacity = '1';
                                wishlistCountElement.style.backgroundColor = '#dc2626';
                                wishlistCountElement.style.color = '#ffffff';
                                wishlistCountElement.style.fontSize = '12px';
                                wishlistCountElement.style.fontWeight = 'bold';
                            }
                        }
                    } catch (error) {
                        // Silent fail for wishlist count update
                    }
                    
                    // Also test cart count
                    try {
                        const cartResponse = await fetch('/Core1_ecommerce/customer/api/cart/count', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        const cartData = await cartResponse.json();
                        
                        if (cartData.success && cartData.data) {
                            const cartCountElement = document.getElementById('cartCount');
                            if (cartCountElement) {
                                const count = cartData.data.total_quantity || cartData.data.count || 0;
                                cartCountElement.textContent = count;
                                // Force styling to make sure it's visible
                                cartCountElement.style.display = 'flex';
                                cartCountElement.style.visibility = 'visible';
                                cartCountElement.style.opacity = '1';
                                cartCountElement.style.backgroundColor = '#d97706';
                                cartCountElement.style.color = '#ffffff';
                                cartCountElement.style.fontSize = '12px';
                                cartCountElement.style.fontWeight = 'bold';
                            }
                        }
                    } catch (error) {
                        // Silent fail for cart count update
                    }
                }, 3000);
            }, 1000);
        });

        async function loadWishlist() {
            showLoading();
            
            try {
                let response;
                
                // Try main API first
                if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                    response = await customerAPI.wishlist.getItems();
                } else {
                    // Use fallback API
                    response = await wishlistAPI.getItems();
                }
                
                if (response.success) {
                    wishlistData = response.data;
                    renderWishlist(wishlistData);
                    updateWishlistCount(response.count || (response.data ? response.data.length : 0));
                } else {
                    if (response.message && response.message.includes('log in')) {
                        showError('Please refresh the page and try again.');
                        // Force page reload after a moment to re-authenticate
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        showError(response.message || 'Failed to load wishlist');
                    }
                }
            } catch (error) {
                showError('Failed to load wishlist. Please try again.');
            }
        }

        function renderWishlist(items) {
            hideAllStates();
            
            if (items.length === 0) {
                document.getElementById('emptyWishlist').classList.remove('hidden');
                document.getElementById('clearWishlistBtn').classList.add('hidden');
                return;
            }
            
            document.getElementById('wishlistItems').classList.remove('hidden');
            document.getElementById('clearWishlistBtn').classList.remove('hidden');
            
            const container = document.getElementById('wishlistItems');
            container.innerHTML = items.map(item => createWishlistItemCard(item)).join('');
        }

        function createWishlistItemCard(item) {
            const discountPercent = item.discount_price ? 
                Math.round(((item.price - item.discount_price) / item.price) * 100) : 0;
            
            return `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow group">
                    <!-- Product Image -->
                    <div class="relative h-48 bg-gray-200 overflow-hidden">
                        <img src="${item.image}" alt="${item.name}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             onerror="this.src='/Core1_ecommerce/customer/images/no-image.png'">
                        ${discountPercent > 0 ? `
                            <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                -${discountPercent}%
                            </div>
                        ` : ''}
                        
                        <!-- Remove from wishlist button -->
                        <button onclick="removeFromWishlist(${item.wishlist_id}, '${item.name.replace(/'/g, "\\'")}', event)"
                                class="absolute top-2 right-2 bg-white p-2 rounded-full shadow-md hover:bg-red-50 hover:text-red-500 transition-colors group-hover:opacity-100 opacity-80">
                            <i class="fas fa-heart text-red-500"></i>
                        </button>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <!-- Store Name -->
                        <div class="text-xs text-gray-500 mb-2">
                            <i class="fas fa-store mr-1"></i>
                            <a href="sellers/store.php?id=${item.seller_id}" 
                               class="hover:text-blue-600 hover:underline transition-colors cursor-pointer"
                               onclick="event.stopPropagation()">
                                ${item.store_name || 'Store'}
                            </a>
                        </div>

                        <!-- Product Name -->
                        <h3 class="font-semibold text-gray-900 text-sm mb-2 line-clamp-2 h-10">
                            ${item.name}
                        </h3>

                        <!-- Price -->
                        <div class="mb-4">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold text-amber-600">₱${parseFloat(item.actual_price || item.price || 0).toFixed(2)}</span>
                                ${item.discount_price && parseFloat(item.discount_price) > 0 ? `
                                    <span class="text-sm text-gray-500 line-through">₱${parseFloat(item.price || 0).toFixed(2)}</span>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button onclick="addToCart(${item.id})" 
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium"
                                    style="background: #d97706 !important; color: white !important; border: none !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important; min-height: 40px !important;">
                                🛒 Add to Cart
                            </button>
                            <button onclick="viewProduct(${item.id})" 
                                    class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <!-- Added Date -->
                        <div class="text-xs text-gray-400 mt-3">
                            Added ${formatDateAgo(item.created_at)}
                        </div>
                    </div>
                </div>
            `;
        }

        async function removeFromWishlist(wishlistId, productName, event) {
            if (event) {
                event.stopPropagation();
            }
            
            showConfirmModal(
                `Remove "${productName}" from your wishlist?`,
                async () => {
                    try {
                        let response;
                        
                        // Try main API first
                        if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                            response = await customerAPI.wishlist.removeItem(wishlistId);
                        } else {
                            // Use fallback API
                            response = await wishlistAPI.removeItem(wishlistId);
                        }
                        
                        if (response.success) {
                            showToast(response.message, 'success');
                            loadWishlist(); // Reload the wishlist
                            updateNavbarWishlistCount();
                            // Also update global wishlist count if function exists
                            if (typeof updateWishlistCount === 'function') {
                                updateWishlistCount();
                            }
                        } else {
                            showToast(response.message, 'error');
                        }
                    } catch (error) {
                        showToast('Failed to remove item from wishlist', 'error');
                    }
                    hideConfirmModal();
                }
            );
        }

        async function clearWishlist() {
            showConfirmModal(
                'Are you sure you want to clear your entire wishlist?',
                async () => {
                    try {
                        let response;
                        
                        // Try main API first
                        if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                            response = await customerAPI.wishlist.clear();
                        } else {
                            // Use fallback API
                            response = await wishlistAPI.clear();
                        }
                        
                        if (response.success) {
                            showToast(response.message, 'success');
                            loadWishlist(); // Reload the wishlist
                            updateNavbarWishlistCount();
                            // Also update global wishlist count if function exists
                            if (typeof updateWishlistCount === 'function') {
                                updateWishlistCount();
                            }
                        } else {
                            showToast(response.message, 'error');
                        }
                    } catch (error) {
                        showToast('Failed to clear wishlist', 'error');
                    }
                    hideConfirmModal();
                }
            );
        }

        async function addToCart(productId) {
            try {
                const response = await customerAPI.cart.addItem(productId, 1);
                
                if (response.success) {
                    showToast(response.message, 'success');
                    updateCartCount(); // Update cart count in navbar
                    // Also update global cart count if function exists (from navbar)
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                } else {
                    showToast(response.message, 'error');
                }
            } catch (error) {
                showToast('Failed to add item to cart', 'error');
            }
        }

        function viewProduct(productId) {
            window.location.href = `products/product.php?id=${productId}`;
        }

        async function checkoutSingleItem(productId) {
            try {
                // First add item to cart
                const addToCartResponse = await customerAPI.cart.addItem(productId, 1);
                
                if (addToCartResponse.success) {
                    // Redirect to checkout with this specific product
                    window.location.href = `checkout.php?product_id=${productId}`;
                } else {
                    showToast(addToCartResponse.message, 'error');
                }
            } catch (error) {
                showToast('Failed to proceed to checkout', 'error');
            }
        }

        async function updateNavbarWishlistCount() {
            try {
                let response;
                
                // Try main API first
                if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                    response = await customerAPI.wishlist.getCount();
                } else {
                    // Use fallback API
                    response = await wishlistAPI.getCount();
                }
                
                if (response.success) {
                    const count = response.data ? response.data.count : response.count;
                    
                    const wishlistCountElement = document.getElementById('wishlistCount');
                    if (wishlistCountElement) {
                        wishlistCountElement.textContent = count;
                    }
                }
            } catch (error) {
                // Silent fail for navbar update
            }
        }

        function updateWishlistCount(count) {
            document.getElementById('wishlistItemCount').textContent = count;
        }

        // Check login status
        async function checkLoginStatus() {
            try {
                if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.auth) {
                    const response = await customerAPI.auth.getProfile();
                    return response.success;
                } else {
                    return false;
                }
            } catch (error) {
                return false;
            }
        }

        function showLoading() {
            hideAllStates();
            document.getElementById('wishlistLoading').classList.remove('hidden');
        }

        function showError(message) {
            hideAllStates();
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('wishlistError').classList.remove('hidden');
        }

        function hideAllStates() {
            document.getElementById('wishlistLoading').classList.add('hidden');
            document.getElementById('emptyWishlist').classList.add('hidden');
            document.getElementById('wishlistItems').classList.add('hidden');
            document.getElementById('wishlistError').classList.add('hidden');
        }

        function showConfirmModal(message, onConfirm) {
            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmButton').onclick = onConfirm;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function hideConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        function formatDateAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            
            if (days === 0) return 'today';
            if (days === 1) return 'yesterday';
            if (days < 7) return `${days} days ago`;
            if (days < 30) return `${Math.floor(days / 7)} weeks ago`;
            if (days < 365) return `${Math.floor(days / 30)} months ago`;
            return `${Math.floor(days / 365)} years ago`;
        }

        // Add CSS for line-clamp utility
        const style = document.createElement('style');
        style.textContent = `
            .line-clamp-1 {
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
            }
            .line-clamp-2 {
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>