    <!-- Sidebar -->
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
                        <span id="productCount" class="ml-auto bg-beige text-white text-xs px-2 py-1 rounded-full">0</span>
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="sidebar-link <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span class="ml-3">Orders</span>
                        <span id="pendingOrderCount" class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">0</span>
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