<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if customer is logged in
$isLoggedIn = isset($_SESSION['customer_id']);
$customerName = $_SESSION['customer_name'] ?? '';

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

// Helper function to determine if nav item should be active
function isActive($page, $currentPage, $currentDir = '') {
    if ($page === $currentPage) return true;
    if ($page === 'index' && $currentPage === 'index' && empty($currentDir)) return true;
    if ($page === 'products' && ($currentPage === 'products' || $currentDir === 'products')) return true;
    if ($page === 'account' && ($currentDir === 'account' || $currentDir === 'cart')) return true;
    return false;
}

// Base path for links (adjust based on current directory)
$basePath = '';
if ($currentDir === 'products' || $currentDir === 'account' || $currentDir === 'cart' || $currentDir === 'support' || $currentDir === 'sellers') {
    $basePath = '../';
}
?>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0" style="display: none;"></div>

<!-- header section -->
<header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Mobile menu toggle -->
            <div class="lg:hidden">
                <button id="mobileMenuToggle" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <img class="h-10 w-10 object-contain" src="<?php echo $basePath; ?>images/logo1.png" alt="Logo">
                <a href="<?php echo $basePath; ?>index.php" class="text-2xl font-bold text-gray-900">
                    Lumino<span class="text-amber-600">.</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="<?php echo $basePath; ?>index.php" 
                   class="font-medium transition-colors <?php echo isActive('index', $currentPage, $currentDir) ? 'text-amber-600' : 'text-gray-700 hover:text-amber-600'; ?>">
                    <i class="fas fa-home mr-1"></i> Home
                </a>
                <a href="<?php echo $basePath; ?>index.php#about" 
                   class="font-medium text-gray-700 hover:text-amber-600 transition-colors">
                    <i class="fas fa-info-circle mr-1"></i> About
                </a>
                <a href="<?php echo $basePath; ?>products.php" 
                   class="font-medium transition-colors <?php echo isActive('products', $currentPage, $currentDir) ? 'text-amber-600' : 'text-gray-700 hover:text-amber-600'; ?>">
                    <i class="fas fa-shopping-bag mr-1"></i> Products
                </a>
                <a href="<?php echo $basePath; ?>index.php#contact" 
                   class="font-medium text-gray-700 hover:text-amber-600 transition-colors">
                    <i class="fas fa-envelope mr-1"></i> Contact
                </a>
                <a href="<?php echo $basePath; ?>support/index.php" 
                   class="font-medium text-gray-700 hover:text-amber-600 transition-colors">
                    <i class="fas fa-headset mr-1"></i> Support
                </a>
            </nav>

            <!-- Right side icons -->
            <div class="flex items-center space-x-4">
                <!-- Support Notifications (only show when logged in) -->
                <?php if ($isLoggedIn): ?>
                <div class="relative">
                    <button id="notificationIcon" class="p-2 rounded-full text-gray-600 hover:text-blue-500 hover:bg-blue-50 transition-all relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notificationCount" class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" style="display: none;">0</span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div id="notificationDropdown" class="absolute top-full right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible transition-all transform translate-y-2 z-50">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-lg text-gray-900">Support Updates</h3>
                                <button id="markAllReadBtn" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Mark All Read
                                </button>
                            </div>
                            <div id="notificationsList" class="max-h-64 overflow-y-auto mb-4">
                                <!-- Notifications will be loaded here -->
                                <div id="notificationsLoading" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500">Loading notifications...</p>
                                </div>
                            </div>
                            <div class="border-t pt-4">
                                <a href="<?php echo $basePath; ?>support/my-tickets.php" class="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    <i class="fas fa-list mr-2"></i> View All Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Wishlist -->
                <a href="<?php echo $basePath; ?>wishlist.php" class="p-2 rounded-full text-gray-600 hover:text-red-500 hover:bg-red-50 transition-all relative">
                    <i class="fas fa-heart text-xl"></i>
                    <span id="wishlistCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>

                <!-- Shopping Cart -->
                <div class="relative">
                    <button id="cartIcon" class="p-2 rounded-full text-gray-600 hover:text-amber-600 hover:bg-amber-50 transition-all relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cartCount" class="absolute -top-1 -right-1 bg-amber-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </button>

                    <!-- Mini Cart Dropdown -->
                    <div id="miniCart" class="absolute top-full right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible transition-all transform translate-y-2 z-50">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-lg text-gray-900">Shopping Cart</h3>
                                <button id="closeMiniCart" class="p-1 rounded-full text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="miniCartItems" class="max-h-64 overflow-y-auto mb-4">
                                <!-- Cart items will be loaded here -->
                            </div>
                            <div class="border-t pt-4">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-semibold text-gray-900">Total:</span>
                                    <span class="font-bold text-xl text-amber-600">₱<span id="miniCartTotal">0.00</span></span>
                                </div>
                                <button id="miniCheckoutBtn" onclick="proceedToCheckout()" 
                                        class="w-full bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors"
                                        style="background-color: #d97706 !important; color: #ffffff !important; border: none !important; font-weight: 500 !important;">
                                    <i class="fas fa-shopping-cart mr-2" style="color: #ffffff !important;"></i> Checkout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Account -->
                <div class="relative">
                    <?php if ($isLoggedIn): ?>
                        <button id="userMenuToggle" class="flex items-center space-x-2 p-2 rounded-full text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all">
                            <div id="navProfileImageContainer" class="w-8 h-8 rounded-full overflow-hidden">
                                <img id="navProfileImage" src="" alt="Profile" class="w-full h-full object-cover" style="display: none;">
                                <div id="navProfilePlaceholder" class="w-full h-full bg-amber-600 text-white flex items-center justify-center">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                            </div>
                            <span class="hidden md:block font-medium"><?php echo htmlspecialchars(explode(' ', $customerName)[0]); ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div id="userDropdown" class="absolute top-full right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible transition-all transform translate-y-2 z-50">
                            <div class="p-2">
                                <div class="px-3 py-2 border-b border-gray-100 mb-2">
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($customerName); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($_SESSION['customer_email'] ?? ''); ?></p>
                                </div>
                                <a href="<?php echo $basePath; ?>index.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                                </a>
                                <a href="<?php echo $basePath; ?>account/orders.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                    <i class="fas fa-box mr-3"></i> My Orders
                                </a>
                                <a href="<?php echo $basePath; ?>account/addresses.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                    <i class="fas fa-map-marker-alt mr-3"></i> Addresses
                                </a>
                                <a href="<?php echo $basePath; ?>account/profile.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                    <i class="fas fa-user-edit mr-3"></i> Profile
                                </a>
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <button onclick="logout()" class="flex items-center w-full px-3 py-2 text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-2">
                            <a href="<?php echo $basePath; ?>login.php" class="px-4 py-2 text-gray-700 hover:text-amber-600 font-medium transition-colors">
                                Login
                            </a>
                            <a href="<?php echo $basePath; ?>register.php" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                                Sign Up
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="lg:hidden mt-4 border-t border-gray-200 pt-4 opacity-0 invisible transition-all transform -translate-y-4">
            <nav class="flex flex-col space-y-2">
                <a href="<?php echo $basePath; ?>index.php" 
                   class="flex items-center px-3 py-2 rounded-md font-medium transition-colors <?php echo isActive('index', $currentPage, $currentDir) ? 'bg-amber-50 text-amber-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-home mr-3"></i> Home
                </a>
                <a href="<?php echo $basePath; ?>index.php#about" 
                   class="flex items-center px-3 py-2 rounded-md font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-info-circle mr-3"></i> About
                </a>
                <a href="<?php echo $basePath; ?>products.php" 
                   class="flex items-center px-3 py-2 rounded-md font-medium transition-colors <?php echo isActive('products', $currentPage, $currentDir) ? 'bg-amber-50 text-amber-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                    <i class="fas fa-shopping-bag mr-3"></i> Products
                </a>
                <a href="<?php echo $basePath; ?>index.php#contact" 
                   class="flex items-center px-3 py-2 rounded-md font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-envelope mr-3"></i> Contact
                </a>
                <a href="<?php echo $basePath; ?>support/index.php" 
                   class="flex items-center px-3 py-2 rounded-md font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-headset mr-3"></i> Support
                </a>
            </nav>

            <?php if (!$isLoggedIn): ?>
                <div class="flex flex-col space-y-2 mt-4 pt-4 border-t border-gray-200">
                    <a href="<?php echo $basePath; ?>login.php" class="px-3 py-2 text-center text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                        Login
                    </a>
                    <a href="<?php echo $basePath; ?>register.php" class="px-3 py-2 text-center bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors">
                        Sign Up
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    mobileMenuToggle?.addEventListener('click', function() {
        if (mobileMenu.classList.contains('opacity-0')) {
            mobileMenu.classList.remove('opacity-0', 'invisible', '-translate-y-4');
            mobileMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
        } else {
            mobileMenu.classList.add('opacity-0', 'invisible', '-translate-y-4');
            mobileMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    });

    // User dropdown toggle
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userDropdown = document.getElementById('userDropdown');

    userMenuToggle?.addEventListener('click', function(e) {
        e.stopPropagation();
        if (userDropdown.classList.contains('opacity-0')) {
            userDropdown.classList.remove('opacity-0', 'invisible', 'translate-y-2');
            userDropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
        } else {
            userDropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
            userDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    });

    // Cart dropdown toggle
    const cartIcon = document.getElementById('cartIcon');
    const miniCart = document.getElementById('miniCart');
    const closeMiniCart = document.getElementById('closeMiniCart');

    cartIcon?.addEventListener('click', function(e) {
        e.stopPropagation();
        if (miniCart.classList.contains('opacity-0')) {
            miniCart.classList.remove('opacity-0', 'invisible', 'translate-y-2');
            miniCart.classList.add('opacity-100', 'visible', 'translate-y-0');
            loadMiniCart();
        } else {
            miniCart.classList.add('opacity-0', 'invisible', 'translate-y-2');
            miniCart.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    });

    closeMiniCart?.addEventListener('click', function() {
        miniCart.classList.add('opacity-0', 'invisible', 'translate-y-2');
        miniCart.classList.remove('opacity-100', 'visible', 'translate-y-0');
    });

    // Notification dropdown toggle
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    notificationIcon?.addEventListener('click', function(e) {
        e.stopPropagation();
        if (notificationDropdown.classList.contains('opacity-0')) {
            notificationDropdown.classList.remove('opacity-0', 'invisible', 'translate-y-2');
            notificationDropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
            loadNotifications();
        } else {
            notificationDropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
            notificationDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    });

    markAllReadBtn?.addEventListener('click', function() {
        markAllNotificationsAsRead();
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!userMenuToggle?.contains(e.target) && !userDropdown?.contains(e.target)) {
            userDropdown?.classList.add('opacity-0', 'invisible', 'translate-y-2');
            userDropdown?.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
        
        if (!cartIcon?.contains(e.target) && !miniCart?.contains(e.target)) {
            miniCart?.classList.add('opacity-0', 'invisible', 'translate-y-2');
            miniCart?.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }

        if (!notificationIcon?.contains(e.target) && !notificationDropdown?.contains(e.target)) {
            notificationDropdown?.classList.add('opacity-0', 'invisible', 'translate-y-2');
            notificationDropdown?.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    });

    // Initialize cart count
    updateCartCount();
    
    // Initialize wishlist count
    updateWishlistCount();
    
    // Initialize profile image
    loadNavProfileImage();
    
    // Initialize notifications
    updateNotificationCount();
    
    // Start notification polling if logged in
    <?php if ($isLoggedIn): ?>
        startNotificationPolling();
    <?php endif; ?>
    
    // Listen for profile image updates from other components
    window.addEventListener('profileImageUpdated', function(event) {
        updateNavProfileImage(event.detail.imageUrl);
    });
});

// Load profile image in navbar
async function loadNavProfileImage() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success && response.customer) {
            updateNavProfileImage(response.customer.profile_image);
        }
    } catch (error) {
        // Silently fail - user may not be logged in
    }
}

// Function to immediately refresh profile image from server
window.refreshProfileImage = async function() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success && response.customer) {
            window.updateGlobalProfileImage(response.customer.profile_image);
        }
    } catch (error) {
        console.error('Failed to refresh profile image:', error);
    }
};

// Update navbar profile image
function updateNavProfileImage(imageUrl) {
    const navProfileImage = document.getElementById('navProfileImage');
    const navProfilePlaceholder = document.getElementById('navProfilePlaceholder');
    
    if (navProfileImage && navProfilePlaceholder) {
        if (imageUrl) {
            navProfileImage.src = imageUrl;
            navProfileImage.style.display = 'block';
            navProfilePlaceholder.style.display = 'none';
        } else {
            navProfileImage.style.display = 'none';
            navProfilePlaceholder.style.display = 'flex';
        }
    }
}

// Global function to update profile image across all components
window.updateGlobalProfileImage = function(imageUrl) {
    // Update navbar
    updateNavProfileImage(imageUrl);
    
    // Update profile page if present
    if (typeof updateProfileImageDisplay === 'function') {
        updateProfileImageDisplay(imageUrl);
    }
    
    // Trigger custom event for other components that might need to listen
    window.dispatchEvent(new CustomEvent('profileImageUpdated', { 
        detail: { imageUrl: imageUrl }
    }));
};

// Helper function to get correct image URL
function getCartItemImage(imageUrl) {
    if (!imageUrl) return '../images/no-image.png';
    
    // If it's already a full URL (http/https) or starts with /, use it as is
    if (imageUrl.startsWith('http') || imageUrl.startsWith('/')) {
        return imageUrl;
    }
    
    // Otherwise, prepend the uploads path
    return `/Core1_ecommerce/uploads/${imageUrl}`;
}

// Cart functions
async function loadMiniCart() {
    try {
        const response = await customerAPI.cart.getItems();
        if (response.success) {
            renderMiniCartItems(response.data);
            document.getElementById('miniCartTotal').textContent = response.summary.total_amount.toFixed(2);
        }
    } catch (error) {
        console.error('Failed to load cart:', error);
    }
}

function renderMiniCartItems(items) {
    const container = document.getElementById('miniCartItems');
    
    if (items.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-4">Your cart is empty</p>';
        return;
    }

    const itemsHTML = items.map(item => `
        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
            <div class="flex items-center space-x-3">
                <img src="${getCartItemImage(item.image)}" 
                     alt="${item.name}" class="w-10 h-10 object-cover rounded">
                <div>
                    <p class="font-medium text-sm text-gray-900 line-clamp-1">${item.name}</p>
                    <p class="text-xs text-gray-500">₱${item.price.toFixed(2)} × ${item.quantity}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="font-medium text-amber-600">₱${item.subtotal.toFixed(2)}</span>
                <button onclick="removeFromCart(${item.cart_id})" class="text-red-500 hover:text-red-700 text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `).join('');

    container.innerHTML = itemsHTML;
}

async function updateCartCount() {
    try {
        const response = await customerAPI.cart.getCount();
        if (response.success) {
            document.getElementById('cartCount').textContent = response.data.total_quantity;
        }
    } catch (error) {
        console.error('Failed to update cart count:', error);
    }
}

async function updateWishlistCount() {
    try {
        // Check if customerAPI is loaded
        if (typeof customerAPI === 'undefined') {
            console.warn('customerAPI not loaded yet, retrying...');
            setTimeout(updateWishlistCount, 100);
            return;
        }
        
        const response = await customerAPI.wishlist.getCount();
        if (response.success) {
            document.getElementById('wishlistCount').textContent = response.data.count;
        }
    } catch (error) {
        // Silently fail - wishlist functionality is optional and user may not be logged in
        console.debug('Wishlist count update failed:', error.message);
    }
}

async function removeFromCart(cartId) {
    try {
        const response = await customerAPI.cart.removeItem(cartId);
        if (response.success) {
            showToast('Item removed from cart', 'success');
            loadMiniCart();
            updateCartCount();
        } else {
            showToast(response.message, 'error');
        }
    } catch (error) {
        showToast('Failed to remove item', 'error');
    }
}

async function logout() {
    try {
        const response = await customerAPI.auth.logout();
        if (response.success) {
            showToast('Logged out successfully', 'success');
            setTimeout(() => {
                window.location.href = '<?php echo $basePath; ?>index.php';
            }, 1000);
        }
    } catch (error) {
        showToast('Logout failed', 'error');
    }
}

// Checkout function with authentication check
async function proceedToCheckout() {
    try {
        // Check if user is logged in
        const profileResponse = await customerAPI.auth.getProfile();
        
        if (!profileResponse.success) {
            showToast('Please log in to proceed to checkout', 'warning');
            setTimeout(() => {
                window.location.href = '<?php echo $basePath; ?>login.php?redirect=checkout';
            }, 1500);
            return;
        }
        
        // Check if cart has items
        const cartResponse = await customerAPI.cart.getItems();
        if (!cartResponse.success || !cartResponse.data || cartResponse.data.length === 0) {
            showToast('Your cart is empty', 'error');
            return;
        }
        
        // Redirect to checkout page
        window.location.href = '<?php echo $basePath; ?>checkout.php';
        
    } catch (error) {
        console.error('Checkout error:', error);
        showToast('Please log in to proceed to checkout', 'warning');
        setTimeout(() => {
            window.location.href = '<?php echo $basePath; ?>login.php?redirect=checkout';
        }, 1500);
    }
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    
    // Remove existing classes and add base classes
    toast.className = 'fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0';
    
    // Add type-specific classes
    switch(type) {
        case 'success':
            toast.classList.add('bg-green-500', 'text-white');
            break;
        case 'error':
            toast.classList.add('bg-red-500', 'text-white');
            break;
        case 'info':
            toast.classList.add('bg-blue-500', 'text-white');
            break;
        default:
            toast.classList.add('bg-green-500', 'text-white');
    }
    
    toast.textContent = message;
    toast.style.display = 'block';
    
    // Show toast with animation
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);

    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }, 3000);
}

// Notification functions
let notificationPollingInterval;

async function updateNotificationCount() {
    try {
        if (typeof customerAPI === 'undefined') {
            console.warn('customerAPI not loaded yet, retrying...');
            setTimeout(updateNotificationCount, 100);
            return;
        }
        
        const response = await customerAPI.support.getUnreadNotifications();
        if (response.success && response.data) {
            const count = response.data.total_unread_messages || 0;
            const notificationCount = document.getElementById('notificationCount');
            
            if (notificationCount) {
                if (count > 0) {
                    notificationCount.textContent = count > 99 ? '99+' : count;
                    notificationCount.style.display = 'flex';
                } else {
                    notificationCount.style.display = 'none';
                }
            }
        }
    } catch (error) {
        // Silently fail - user may not be logged in
        console.debug('Notification count update failed:', error.message);
    }
}

async function loadNotifications() {
    const notificationsLoading = document.getElementById('notificationsLoading');
    const notificationsList = document.getElementById('notificationsList');
    
    if (!notificationsList) return;
    
    notificationsLoading.style.display = 'block';
    
    try {
        const response = await customerAPI.support.getUnreadNotifications();
        if (response.success && response.data) {
            renderNotifications(response.data.tickets);
        } else {
            notificationsList.innerHTML = '<div class="text-center py-4 text-gray-500">No new notifications</div>';
        }
    } catch (error) {
        console.error('Failed to load notifications:', error);
        notificationsList.innerHTML = '<div class="text-center py-4 text-red-500">Failed to load notifications</div>';
    } finally {
        notificationsLoading.style.display = 'none';
    }
}

function renderNotifications(notifications) {
    const notificationsList = document.getElementById('notificationsList');
    
    if (!notifications || notifications.length === 0) {
        notificationsList.innerHTML = '<div class="text-center py-4 text-gray-500">No new notifications</div>';
        return;
    }

    const notificationsHTML = notifications.map(notification => {
        const statusClass = getNotificationStatusClass(notification.status);
        const priorityClass = getNotificationPriorityClass(notification.priority);
        const timeAgo = getTimeAgo(notification.last_reply_at);
        
        return `
            <div class="border-b border-gray-100 last:border-b-0 py-3">
                <a href="<?php echo $basePath; ?>support/ticket-detail.php?id=${notification.ticket_id}" class="block hover:bg-gray-50 -m-2 p-2 rounded">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <p class="text-sm font-medium text-gray-900 truncate">#${notification.ticket_number}</p>
                                <span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${notification.status.replace('_', ' ')}</span>
                            </div>
                            <p class="text-sm text-gray-900 font-medium mb-1 line-clamp-2">${notification.subject}</p>
                            ${notification.preview ? `<p class="text-xs text-gray-600 line-clamp-2">${notification.preview}</p>` : ''}
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500">${timeAgo}</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full ${priorityClass}">${notification.priority}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        `;
    }).join('');

    notificationsList.innerHTML = notificationsHTML;
}

async function markAllNotificationsAsRead() {
    try {
        const response = await customerAPI.post('/support/notifications/mark-read');
        if (response.success) {
            showToast('All notifications marked as read', 'success');
            updateNotificationCount();
            loadNotifications();
            
            // Close dropdown
            const notificationDropdown = document.getElementById('notificationDropdown');
            notificationDropdown?.classList.add('opacity-0', 'invisible', 'translate-y-2');
            notificationDropdown?.classList.remove('opacity-100', 'visible', 'translate-y-0');
        }
    } catch (error) {
        console.error('Failed to mark notifications as read:', error);
        showToast('Failed to mark notifications as read', 'error');
    }
}

function startNotificationPolling() {
    // Update immediately
    updateNotificationCount();
    
    // Then update every 30 seconds
    notificationPollingInterval = setInterval(updateNotificationCount, 30000);
}

function stopNotificationPolling() {
    if (notificationPollingInterval) {
        clearInterval(notificationPollingInterval);
    }
}

function getNotificationStatusClass(status) {
    const statusClasses = {
        'open': 'bg-red-100 text-red-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'waiting_customer': 'bg-blue-100 text-blue-800',
        'resolved': 'bg-green-100 text-green-800',
        'closed': 'bg-gray-100 text-gray-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
}

function getNotificationPriorityClass(priority) {
    const priorityClasses = {
        'urgent': 'bg-red-100 text-red-800',
        'high': 'bg-orange-100 text-orange-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'low': 'bg-green-100 text-green-800'
    };
    return priorityClasses[priority] || 'bg-gray-100 text-gray-800';
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
    return date.toLocaleDateString();
}
</script>