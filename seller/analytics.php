<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Seller Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-md z-50 h-20">
        <div class="flex items-center justify-between px-6 h-full">
            <div class="flex items-center">
                <button id="sidebarToggle" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-2xl text-gray-700 hover:text-beige transition-colors"></i>
                </button>
                <div class="logo text-3xl font-bold text-gray-800">
                    Lumino<span class="text-beige">Shop</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="text-gray-700 hover:text-beige transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </button>
                </div>
                <div class="relative">
                    <button class="text-gray-700 hover:text-beige transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-beige text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">5</span>
                    </button>
                </div>
                <div class="relative">
                    <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-beige transition-colors">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
                             alt="Profile" class="w-8 h-8 rounded-full">
                        <span class="hidden md:block">John Seller</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
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
                <li><a href="dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt w-5"></i><span class="ml-3">Dashboard</span></a></li>
                <li><a href="products.php" class="sidebar-link"><i class="fas fa-box w-5"></i><span class="ml-3">Products</span><span class="ml-auto bg-beige text-white text-xs px-2 py-1 rounded-full">24</span></a></li>
                <li><a href="orders.php" class="sidebar-link"><i class="fas fa-shopping-cart w-5"></i><span class="ml-3">Orders</span><span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">8</span></a></li>
                <li><a href="customers.php" class="sidebar-link"><i class="fas fa-users w-5"></i><span class="ml-3">Customers</span></a></li>
                <li><a href="analytics.php" class="sidebar-link active"><i class="fas fa-chart-line w-5"></i><span class="ml-3">Analytics</span></a></li>
                <li><a href="reviews.php" class="sidebar-link"><i class="fas fa-star w-5"></i><span class="ml-3">Reviews</span></a></li>
                <li><a href="promotions.php" class="sidebar-link"><i class="fas fa-percent w-5"></i><span class="ml-3">Promotions</span></a></li>
                <li><a href="finances.php" class="sidebar-link"><i class="fas fa-wallet w-5"></i><span class="ml-3">Finances</span></a></li>
                <li><a href="settings.php" class="sidebar-link"><i class="fas fa-cog w-5"></i><span class="ml-3">Settings</span></a></li>
            </ul>
        </nav>
        <div class="absolute bottom-6 left-4 right-4">
            <button class="w-full flex items-center justify-center px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Analytics</h1>
                    <p class="text-gray-600">Track your store performance and insights</p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                        <option>Last 30 days</option>
                        <option>Last 7 days</option>
                        <option>Last 3 months</option>
                        <option>This year</option>
                    </select>
                    <button class="btn-beige">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </button>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">$12,426</p>
                            <p class="text-sm text-green-600 flex items-center mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +12.5%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-beige rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Orders</p>
                            <p class="text-2xl font-bold text-gray-900">856</p>
                            <p class="text-sm text-green-600 flex items-center mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +8.2%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                            <p class="text-2xl font-bold text-gray-900">$14.52</p>
                            <p class="text-sm text-red-600 flex items-center mt-1">
                                <i class="fas fa-arrow-down mr-1"></i>
                                -2.1%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                            <p class="text-2xl font-bold text-gray-900">3.24%</p>
                            <p class="text-sm text-green-600 flex items-center mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +0.8%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-percentage text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Revenue Trend</h3>
                            <div class="flex space-x-2">
                                <button class="text-sm px-3 py-1 bg-beige text-white rounded-full">Revenue</button>
                                <button class="text-sm px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-full">Orders</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="revenueChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Top Selling Products</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=40&h=40&fit=crop" alt="Product" class="w-10 h-10 rounded object-cover">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Premium Sneakers</p>
                                        <p class="text-sm text-gray-600">124 sold</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-beige">$9,916</p>
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-beige h-2 rounded-full" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=40&h=40&fit=crop" alt="Product" class="w-10 h-10 rounded object-cover">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Classic Watch</p>
                                        <p class="text-sm text-gray-600">89 sold</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-beige">$17,791</p>
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-beige h-2 rounded-full" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=40&h=40&fit=crop" alt="Product" class="w-10 h-10 rounded object-cover">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Designer Sunglasses</p>
                                        <p class="text-sm text-gray-600">67 sold</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-beige">$8,709</p>
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-beige h-2 rounded-full" style="width: 55%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=40&h=40&fit=crop" alt="Product" class="w-10 h-10 rounded object-cover">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Wireless Headphones</p>
                                        <p class="text-sm text-gray-600">45 sold</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-beige">$6,750</p>
                                    <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-beige h-2 rounded-full" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Category Performance -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Sales by Category</h3>
                    </div>
                    <div class="p-6">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Customer Insights -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Customer Insights</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-beige rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold text-white">67%</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">Returning Customers</p>
                                <p class="text-xs text-gray-600 mt-1">+5% from last month</p>
                            </div>
                            <div class="text-center">
                                <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold text-white">33%</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">New Customers</p>
                                <p class="text-xs text-gray-600 mt-1">+12% from last month</p>
                            </div>
                        </div>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Average Customer Lifetime Value</span>
                                <span class="font-semibold text-beige">$456</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Customer Retention Rate</span>
                                <span class="font-semibold text-green-600">78%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avg. Orders per Customer</span>
                                <span class="font-semibold text-gray-900">4.2</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Table -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Monthly Performance</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customers</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Order Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">January 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$12,426</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">856</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">524</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$14.52</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+12.5%</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">December 2023</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$11,045</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">762</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">489</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$14.49</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+8.7%</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">November 2023</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$10,162</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">698</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">445</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$14.56</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-2.3%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

    <script>
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

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan 1', 'Jan 5', 'Jan 10', 'Jan 15', 'Jan 20', 'Jan 25', 'Jan 30'],
                datasets: [{
                    label: 'Revenue',
                    data: [1200, 1900, 1500, 2100, 1800, 2400, 2200],
                    borderColor: '#b48d6b',
                    backgroundColor: 'rgba(180, 141, 107, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports'],
                datasets: [{
                    data: [30, 25, 15, 20, 10],
                    backgroundColor: [
                        '#b48d6b',
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>