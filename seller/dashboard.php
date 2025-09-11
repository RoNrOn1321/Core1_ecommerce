<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Lumino Ecommerce</title>
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
</head>
<body class="bg-gray-50 font-sans">
    <!-- Auth Check & Redirect -->
    <div id="authCheck" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-lock text-4xl text-red-500 mb-4"></i>
                <h2 class="text-xl font-bold text-gray-800 mb-4">Authentication Required</h2>
                <p class="text-gray-600 mb-6">Please log in to access your seller dashboard.</p>
                <button onclick="redirectToLogin()" class="btn-beige w-full">
                    Go to Login
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-40">
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <div class="text-center">
                <div class="spinner mb-4"></div>
                <p class="text-gray-600">Loading dashboard...</p>
            </div>
        </div>
    </div>

    <!-- API Status Indicator -->
    <div id="apiStatus" class="fixed top-4 right-4 z-30">
        <div class="api-status offline">
            <div class="status-dot"></div>
            <span>API Offline</span>
        </div>
    </div>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-md z-50 h-20">
        <div class="flex items-center justify-between px-6 h-full">
            <!-- Logo -->
            <div class="flex items-center">
                <button id="sidebarToggle" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-2xl text-gray-700 hover:text-beige transition-colors"></i>
                </button>
                <div class="logo text-3xl font-bold text-gray-800">
                    Lumino<span class="text-beige">Shop</span>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="text-gray-700 hover:text-beige transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notificationCount" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </button>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-beige transition-colors">
                        <img id="profileImage" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
                             alt="Profile" class="w-8 h-8 rounded-full">
                        <span id="profileName" class="hidden md:block">Loading...</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                    <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                        <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <button onclick="logout()" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 w-64 h-screen bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 pt-20">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Seller Portal</h3>
            <p class="text-sm text-gray-600 mt-1">Manage your store</p>
        </div>

        <nav class="mt-6">
            <ul class="space-y-2 px-4">
                <li>
                    <a href="dashboard.php" class="sidebar-link active">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="products.php" class="sidebar-link">
                        <i class="fas fa-box w-5"></i>
                        <span class="ml-3">Products</span>
                        <span id="productCount" class="ml-auto bg-beige text-white text-xs px-2 py-1 rounded-full">0</span>
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="sidebar-link">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span class="ml-3">Orders</span>
                        <span id="pendingOrderCount" class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                    </a>
                </li>
                <li>
                    <a href="customers.php" class="sidebar-link">
                        <i class="fas fa-users w-5"></i>
                        <span class="ml-3">Customers</span>
                    </a>
                </li>
                <li>
                    <a href="analytics.php" class="sidebar-link">
                        <i class="fas fa-chart-line w-5"></i>
                        <span class="ml-3">Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="reviews.php" class="sidebar-link">
                        <i class="fas fa-star w-5"></i>
                        <span class="ml-3">Reviews</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="sidebar-link">
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

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
                <p class="text-gray-600">Welcome back! Here's what's happening with your store today.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="dashboard-card">
                    <div class="flex items-center">
                        <div class="stat-icon bg-beige">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p id="totalRevenue" class="text-2xl font-bold text-gray-900">₱0.00</p>
                            <p id="revenueChange" class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                Loading...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="dashboard-card">
                    <div class="flex items-center">
                        <div class="stat-icon bg-blue-500">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p id="totalOrders" class="text-2xl font-bold text-gray-900">0</p>
                            <p id="ordersChange" class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                Loading...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="dashboard-card">
                    <div class="flex items-center">
                        <div class="stat-icon bg-purple-500">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Products</p>
                            <p id="totalProducts" class="text-2xl font-bold text-gray-900">0</p>
                            <p id="publishedProducts" class="text-sm text-gray-600">0 published</p>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="dashboard-card">
                    <div class="flex items-center">
                        <div class="stat-icon bg-orange-500">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Low Stock</p>
                            <p id="lowStockCount" class="text-2xl font-bold text-gray-900">0</p>
                            <p class="text-sm text-orange-600">Items need restocking</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                            <a href="orders.php" class="text-beige hover:text-beige-dark text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="recentOrders" class="space-y-4">
                            <div class="text-center text-gray-500">Loading orders...</div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Distribution -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Order Status</h3>
                    </div>
                    <div class="p-6">
                        <div id="orderStatusChart" class="space-y-3">
                            <div class="text-center text-gray-500">Loading status chart...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products Section -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Top Performing Products</h3>
                        <a href="products.php" class="text-beige hover:text-beige-dark text-sm font-medium">Manage Products</a>
                    </div>
                </div>
                <div id="topProducts" class="p-6">
                    <div class="text-center text-gray-500">Loading top products...</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

    <!-- Notifications Container -->
    <div id="notificationsContainer" class="fixed top-20 right-4 z-40"></div>

    <!-- Scripts -->
    <script src="js/seller-api.js"></script>
    <script>
        // Initialize API client
        const api = new SellerAPI();
        let currentSeller = null;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', async () => {
            await initializeDashboard();
        });

        async function initializeDashboard() {
            try {
                showLoading(true);
                
                // Check authentication
                try {
                    const response = await api.getCurrentSeller();
                    currentSeller = response.data;
                    updateApiStatus(true);
                    
                    // Update profile info
                    updateProfileInfo(currentSeller);
                    
                    // Load dashboard data
                    await loadDashboardData();
                    
                } catch (authError) {
                    console.log('Not authenticated:', authError);
                    showAuthRequired();
                    return;
                }
                
            } catch (error) {
                console.error('Dashboard initialization error:', error);
                updateApiStatus(false);
                showNotification('Failed to load dashboard data', 'error');
            } finally {
                showLoading(false);
            }
        }

        async function loadDashboardData() {
            try {
                // Load all dashboard data in parallel
                const [dashboardData, orderStats, recentOrders] = await Promise.all([
                    api.getDashboard(),
                    api.getOrderStats(),
                    api.getOrders({ limit: 5 })
                ]);

                // Update dashboard cards
                updateDashboardCards(dashboardData.data);
                
                // Update recent orders
                updateRecentOrders(recentOrders.data.orders);
                
                // Update order status chart
                updateOrderStatusChart(orderStats.data);

            } catch (error) {
                console.error('Error loading dashboard data:', error);
                showNotification('Some dashboard data could not be loaded', 'warning');
            }
        }

        function updateDashboardCards(dashboardData) {
            // Revenue
            if (dashboardData.revenue) {
                document.getElementById('totalRevenue').textContent = 
                    api.formatCurrency(dashboardData.revenue.total_revenue || 0);
                
                const monthRevenue = dashboardData.revenue.month_revenue || 0;
                document.getElementById('revenueChange').innerHTML = `
                    <i class="fas fa-chart-line mr-1"></i>
                    ₱${monthRevenue.toFixed(2)} this month
                `;
            }

            // Orders
            if (dashboardData.orders) {
                document.getElementById('totalOrders').textContent = 
                    dashboardData.orders.total_orders || 0;
                
                const pendingOrders = dashboardData.orders.pending_orders || 0;
                document.getElementById('ordersChange').innerHTML = `
                    <i class="fas fa-clock mr-1 ${pendingOrders > 0 ? 'text-orange-500' : 'text-green-500'}"></i>
                    ${pendingOrders} pending orders
                `;
                
                // Update sidebar notification
                document.getElementById('pendingOrderCount').textContent = pendingOrders;
            }

            // Products
            if (dashboardData.products) {
                document.getElementById('totalProducts').textContent = 
                    dashboardData.products.total_products || 0;
                
                document.getElementById('publishedProducts').textContent = 
                    `${dashboardData.products.published_products || 0} published`;
                
                document.getElementById('lowStockCount').textContent = 
                    dashboardData.products.low_stock_products || 0;
                
                // Update sidebar notification
                document.getElementById('productCount').textContent = 
                    dashboardData.products.total_products || 0;
            }

            // Update top products
            if (dashboardData.top_products) {
                updateTopProducts(dashboardData.top_products);
            }
        }

        function updateRecentOrders(orders) {
            const container = document.getElementById('recentOrders');
            
            if (!orders || orders.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500">No recent orders</div>';
                return;
            }

            const ordersHtml = orders.map(order => `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-beige rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            #${order.id}
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">${order.order_number}</p>
                            <p class="text-sm text-gray-600">${order.first_name} ${order.last_name}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">${api.formatCurrency(order.seller_total || 0)}</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${api.getOrderStatusClass(order.status)}">
                            ${order.status}
                        </span>
                    </div>
                </div>
            `).join('');

            container.innerHTML = ordersHtml;
        }

        function updateOrderStatusChart(orderStats) {
            const container = document.getElementById('orderStatusChart');
            const statusData = orderStats.by_status || {};
            
            const statusItems = [
                { status: 'pending', count: statusData.pending || 0, color: 'bg-yellow-500', textColor: 'text-yellow-600' },
                { status: 'processing', count: statusData.processing || 0, color: 'bg-blue-500', textColor: 'text-blue-600' },
                { status: 'shipped', count: statusData.shipped || 0, color: 'bg-purple-500', textColor: 'text-purple-600' },
                { status: 'delivered', count: statusData.delivered || 0, color: 'bg-green-500', textColor: 'text-green-600' },
                { status: 'cancelled', count: statusData.cancelled || 0, color: 'bg-red-500', textColor: 'text-red-600' }
            ];

            const chartHtml = statusItems.map(item => `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 ${item.color} rounded-full mr-3"></div>
                        <span class="text-gray-700 capitalize">${item.status}</span>
                    </div>
                    <span class="font-semibold ${item.textColor}">${item.count}</span>
                </div>
            `).join('');

            container.innerHTML = chartHtml;
        }

        function updateTopProducts(products) {
            const container = document.getElementById('topProducts');
            
            if (!products || products.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500">No product data available</div>';
                return;
            }

            const productsHtml = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${products.map(product => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">${product.name}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${api.formatCurrency(product.price)}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${product.total_sold || 0}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${api.formatCurrency(product.total_revenue || 0)}</div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = productsHtml;
        }

        function updateProfileInfo(seller) {
            document.getElementById('profileName').textContent = 
                `${seller.first_name} ${seller.last_name}`;
        }

        function updateApiStatus(online) {
            const statusElement = document.getElementById('apiStatus');
            const statusClass = online ? 'api-status online' : 'api-status offline';
            const statusText = online ? 'API Online' : 'API Offline';
            
            statusElement.innerHTML = `
                <div class="${statusClass}">
                    <div class="status-dot"></div>
                    <span>${statusText}</span>
                </div>
            `;
        }

        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (show) {
                overlay.classList.remove('hidden');
            } else {
                overlay.classList.add('hidden');
            }
        }

        function showAuthRequired() {
            document.getElementById('authCheck').classList.remove('hidden');
        }

        function redirectToLogin() {
            window.location.href = 'login.php';
        }

        async function logout() {
            try {
                await api.logout();
                showNotification('Logged out successfully', 'success');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1000);
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = 'login.php';
            }
        }

        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationsContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            container.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Hide after 5 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        // Sidebar functionality
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        // Profile dropdown
        document.getElementById('profileDropdown').addEventListener('click', () => {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

        // Close profile dropdown when clicking outside
        document.addEventListener('click', (event) => {
            const profileDropdown = document.getElementById('profileDropdown');
            const profileMenu = document.getElementById('profileMenu');
            
            if (!profileDropdown.contains(event.target)) {
                profileMenu.classList.add('hidden');
            }
        });

        // Auto-refresh dashboard data every 5 minutes
        setInterval(async () => {
            try {
                await loadDashboardData();
                console.log('Dashboard data refreshed');
            } catch (error) {
                console.error('Auto-refresh failed:', error);
            }
        }, 5 * 60 * 1000);
    </script>
</body>
</html>