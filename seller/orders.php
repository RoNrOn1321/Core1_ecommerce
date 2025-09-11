<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Seller Dashboard</title>
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
                <li><a href="orders.php" class="sidebar-link active"><i class="fas fa-shopping-cart w-5"></i><span class="ml-3">Orders</span><span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">8</span></a></li>
                <li><a href="customers.php" class="sidebar-link"><i class="fas fa-users w-5"></i><span class="ml-3">Customers</span></a></li>
                <li><a href="analytics.php" class="sidebar-link"><i class="fas fa-chart-line w-5"></i><span class="ml-3">Analytics</span></a></li>
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
                            <p class="text-2xl font-bold text-gray-900">12</p>
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
                            <p class="text-2xl font-bold text-gray-900">8</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Completed</p>
                            <p class="text-2xl font-bold text-gray-900">156</p>
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
                            <p class="text-2xl font-bold text-gray-900">4</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="Search orders..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Processing</option>
                            <option>Shipped</option>
                            <option>Delivered</option>
                            <option>Cancelled</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>Last 30 days</option>
                            <option>Last 7 days</option>
                            <option>Last 3 months</option>
                            <option>This year</option>
                        </select>
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                <!-- Order Card 1 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">#ORD-12345</h3>
                                    <p class="text-sm text-gray-600">Placed on January 15, 2024 at 2:30 PM</p>
                                </div>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Processing
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Customer</p>
                                    <p class="text-gray-900">John Doe</p>
                                    <p class="text-sm text-gray-600">john.doe@email.com</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Total Amount</p>
                                    <p class="text-xl font-bold text-beige">$89.99</p>
                                    <p class="text-sm text-gray-600">2 items</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Shipping Address</p>
                                    <p class="text-gray-900">123 Main St</p>
                                    <p class="text-sm text-gray-600">New York, NY 10001</p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <h4 class="font-medium text-gray-900 mb-2">Items</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=50&h=50&fit=crop" alt="Product" class="w-10 h-10 rounded object-cover">
                                        <div class="ml-3 flex-1">
                                            <p class="font-medium text-gray-900">Premium Sneakers</p>
                                            <p class="text-sm text-gray-600">Qty: 1 × $79.99</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=50&h=50&fit=crop" alt="Product" class="w-10 h-10 rounded object-cover">
                                        <div class="ml-3 flex-1">
                                            <p class="font-medium text-gray-900">Classic Watch</p>
                                            <p class="text-sm text-gray-600">Qty: 1 × $10.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 lg:mt-0 lg:ml-6">
                            <div class="flex flex-col space-y-2">
                                <button class="btn-beige text-sm px-4 py-2">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </button>
                                <button class="btn-beige-outline text-sm px-4 py-2">
                                    <i class="fas fa-truck mr-2"></i>Mark as Shipped
                                </button>
                                <button class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                    <i class="fas fa-print mr-2"></i>Print Label
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Card 2 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">#ORD-12344</h3>
                                    <p class="text-sm text-gray-600">Placed on January 14, 2024 at 11:15 AM</p>
                                </div>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Delivered
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Customer</p>
                                    <p class="text-gray-900">Jane Smith</p>
                                    <p class="text-sm text-gray-600">jane.smith@email.com</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Total Amount</p>
                                    <p class="text-xl font-bold text-beige">$45.50</p>
                                    <p class="text-sm text-gray-600">1 item</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Shipping Address</p>
                                    <p class="text-gray-900">456 Oak Ave</p>
                                    <p class="text-sm text-gray-600">Los Angeles, CA 90210</p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <h4 class="font-medium text-gray-900 mb-2">Items</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=50&h=50&fit=crop" alt="Product" class="w-10 h-10 rounded object-cover">
                                        <div class="ml-3 flex-1">
                                            <p class="font-medium text-gray-900">Designer Sunglasses</p>
                                            <p class="text-sm text-gray-600">Qty: 1 × $45.50</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 lg:mt-0 lg:ml-6">
                            <div class="flex flex-col space-y-2">
                                <button class="btn-beige text-sm px-4 py-2">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </button>
                                <button class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                    <i class="fas fa-undo mr-2"></i>Process Return
                                </button>
                                <button class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                    <i class="fas fa-receipt mr-2"></i>View Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Card 3 -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">#ORD-12343</h3>
                                    <p class="text-sm text-gray-600">Placed on January 13, 2024 at 9:45 AM</p>
                                </div>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Shipped
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Customer</p>
                                    <p class="text-gray-900">Bob Johnson</p>
                                    <p class="text-sm text-gray-600">bob.johnson@email.com</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Total Amount</p>
                                    <p class="text-xl font-bold text-beige">$156.75</p>
                                    <p class="text-sm text-gray-600">3 items</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Shipping Address</p>
                                    <p class="text-gray-900">789 Pine St</p>
                                    <p class="text-sm text-gray-600">Chicago, IL 60601</p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <h4 class="font-medium text-gray-900 mb-2">Tracking</h4>
                                <p class="text-sm text-gray-600">Tracking #: <span class="font-mono text-beige">1Z999AA1234567890</span></p>
                                <p class="text-sm text-gray-600">Expected delivery: January 16, 2024</p>
                            </div>
                        </div>

                        <div class="mt-4 lg:mt-0 lg:ml-6">
                            <div class="flex flex-col space-y-2">
                                <button class="btn-beige text-sm px-4 py-2">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </button>
                                <button class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                    <i class="fas fa-truck mr-2"></i>Track Package
                                </button>
                                <button class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                    <i class="fas fa-envelope mr-2"></i>Contact Customer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex items-center justify-center">
                <div class="flex items-center space-x-2">
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
                    <button class="px-3 py-2 text-sm bg-beige text-white rounded-lg">1</button>
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
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
    </script>
</body>
</html>