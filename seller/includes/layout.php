<?php
// Layout component for seller pages
function startLayout($pageTitle = 'Seller Portal', $includeNotifications = true) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Lumino Ecommerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'beige': '#b48d6b',
                        'beige-light': '#c8a382',
                        'beige-dark': '#9d7a5a',
                    }
                }
            }
        }
    </script>
    <?php if ($includeNotifications): ?>
    <style>
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        .notification-badge.urgent {
            animation: urgentPulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes urgentPulse {
            0%, 100% { transform: scale(1); background-color: #dc2626; }
            50% { transform: scale(1.1); background-color: #b91c1c; }
        }
        
        .sidebar-link {
            position: relative;
        }
        
        .notification-indicator {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #dc2626;
            border-radius: 50%;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
    </style>
    <?php endif; ?>
</head>
<body class="bg-gray-50 font-sans">
<?php include 'header.php'; ?>

<!-- Enhanced Sidebar with Real-time Notifications -->
<aside id="sidebar" class="fixed left-0 top-0 w-64 h-screen bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 pt-20">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Seller Portal</h3>
        <p class="text-sm text-gray-600 mt-1">Manage your store</p>
    </div>

    <nav class="mt-6">
        <ul class="space-y-2 px-4">
            <li>
                <a href="dashboard.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="products.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>">
                    <i class="fas fa-box w-5"></i>
                    <span class="ml-3">Products</span>
                    <span id="lowStockCount" class="ml-auto notification-badge bg-orange-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                    <div id="productNotificationDot" class="notification-indicator hidden"></div>
                </a>
            </li>
            <li>
                <a href="orders.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Orders</span>
                    <span id="pendingOrderCount" class="ml-auto notification-badge bg-red-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                    <div id="orderNotificationDot" class="notification-indicator hidden"></div>
                </a>
            </li>
            <li>
                <a href="customers.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'customers.php') ? 'active' : ''; ?>">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Customers</span>
                </a>
            </li>
            <li>
                <a href="analytics.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="ml-3">Analytics</span>
                </a>
            </li>
            <li>
                <a href="reviews.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reviews.php') ? 'active' : ''; ?>">
                    <i class="fas fa-star w-5"></i>
                    <span class="ml-3">Reviews</span>
                    <span id="newReviewCount" class="ml-auto notification-badge bg-blue-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                </a>
            </li>
            <li>
                <a href="promotions.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'promotions.php') ? 'active' : ''; ?>">
                    <i class="fas fa-percent w-5"></i>
                    <span class="ml-3">Promotions</span>
                </a>
            </li>
            <li>
                <a href="finances.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'finances.php') ? 'active' : ''; ?>">
                    <i class="fas fa-wallet w-5"></i>
                    <span class="ml-3">Finances</span>
                </a>
            </li>
            <li>
                <a href="support.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'support.php') ? 'active' : ''; ?>">
                    <i class="fas fa-headset w-5"></i>
                    <span class="ml-3">Support</span>
                    <span id="supportTicketCount" class="ml-auto notification-badge bg-purple-500 text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                    <div id="supportNotificationDot" class="notification-indicator hidden"></div>
                </a>
            </li>
            <li>
                <a href="settings.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout Button -->
    <div class="absolute bottom-6 left-4 right-4">
        <button onclick="logout()" class="w-full flex items-center justify-center px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
            <i class="fas fa-sign-out-alt mr-2"></i>
            Logout
        </button>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

<!-- Main Content -->
<main class="lg:ml-64 pt-20 min-h-screen">
<?php
}

function endLayout($includeNotifications = true) {
?>
</main>

<?php include 'footer.php'; ?>

<?php if ($includeNotifications): ?>
<script>
class NotificationManager {
    constructor() {
        this.updateInterval = 30000; // 30 seconds
        this.lastUpdate = 0;
        this.init();
    }
    
    init() {
        this.updateNotifications();
        setInterval(() => this.updateNotifications(), this.updateInterval);
        
        // Update immediately when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.updateNotifications();
            }
        });
    }
    
    async updateNotifications() {
        try {
            const response = await fetch('api/notifications/get.php');
            const data = await response.json();
            
            if (data.success) {
                this.updateSidebarCounts(data.data.sidebar_counts);
                this.updateNotificationIndicators(data.data);
            }
        } catch (error) {
            console.error('Error updating notifications:', error);
        }
    }
    
    updateSidebarCounts(counts) {
        // Update pending orders
        const pendingOrderElement = document.getElementById('pendingOrderCount');
        const orderDot = document.getElementById('orderNotificationDot');
        if (pendingOrderElement) {
            if (counts.orders > 0) {
                pendingOrderElement.textContent = counts.orders;
                pendingOrderElement.classList.remove('hidden');
                pendingOrderElement.classList.add(counts.orders > 5 ? 'urgent' : '');
                if (orderDot) orderDot.classList.remove('hidden');
            } else {
                pendingOrderElement.classList.add('hidden');
                if (orderDot) orderDot.classList.add('hidden');
            }
        }
        
        // Update low stock products
        const lowStockElement = document.getElementById('lowStockCount');
        const productDot = document.getElementById('productNotificationDot');
        if (lowStockElement) {
            if (counts.products > 0) {
                lowStockElement.textContent = counts.products;
                lowStockElement.classList.remove('hidden');
                if (productDot) productDot.classList.remove('hidden');
            } else {
                lowStockElement.classList.add('hidden');
                if (productDot) productDot.classList.add('hidden');
            }
        }
        
        // Update support tickets
        const supportElement = document.getElementById('supportTicketCount');
        const supportDot = document.getElementById('supportNotificationDot');
        if (supportElement) {
            if (counts.support > 0) {
                supportElement.textContent = counts.support;
                supportElement.classList.remove('hidden');
                supportElement.classList.add(counts.support > 3 ? 'urgent' : '');
                if (supportDot) supportDot.classList.remove('hidden');
            } else {
                supportElement.classList.add('hidden');
                if (supportDot) supportDot.classList.add('hidden');
            }
        }
        
        // Update page title with total notifications
        const totalNotifications = counts.orders + counts.products + counts.support;
        if (totalNotifications > 0) {
            document.title = `(${totalNotifications}) ${document.title.replace(/^\(\d+\)\s/, '')}`;
        } else {
            document.title = document.title.replace(/^\(\d+\)\s/, '');
        }
    }
    
    updateNotificationIndicators(data) {
        // Update favicon with notification count (optional)
        if (data.total_unread > 0) {
            this.updateFavicon(data.total_unread);
        }
        
        // Play notification sound for urgent notifications (optional)
        if (data.sidebar_counts.orders > this.lastOrderCount && this.lastOrderCount > 0) {
            this.playNotificationSound();
        }
        
        // Store last counts for comparison
        this.lastOrderCount = data.sidebar_counts.orders;
    }
    
    updateFavicon(count) {
        // Create a canvas to draw the notification count
        const canvas = document.createElement('canvas');
        canvas.width = 32;
        canvas.height = 32;
        const ctx = canvas.getContext('2d');
        
        // Draw red circle
        ctx.fillStyle = '#dc2626';
        ctx.beginPath();
        ctx.arc(16, 16, 16, 0, 2 * Math.PI);
        ctx.fill();
        
        // Draw count text
        ctx.fillStyle = 'white';
        ctx.font = 'bold 20px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(count > 99 ? '99+' : count.toString(), 16, 16);
        
        // Update favicon
        const link = document.querySelector("link[rel*='icon']") || document.createElement('link');
        link.type = 'image/x-icon';
        link.rel = 'shortcut icon';
        link.href = canvas.toDataURL();
        document.getElementsByTagName('head')[0].appendChild(link);
    }
    
    playNotificationSound() {
        // Create a short beep sound (optional)
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
    }
}

// Initialize notification manager
document.addEventListener('DOMContentLoaded', () => {
    new NotificationManager();
});

// Global logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'api/auth/logout.php';
    }
}
</script>
<?php endif; ?>

</body>
</html>
<?php
}
?>