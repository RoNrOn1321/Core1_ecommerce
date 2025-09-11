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
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </button>
                </div>

                <!-- Messages -->
                <div class="relative">
                    <button class="text-gray-700 hover:text-beige transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-beige text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">5</span>
                    </button>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-beige transition-colors">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
                             alt="Profile" class="w-8 h-8 rounded-full">
                        <span class="hidden md:block">John Seller</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                    <!-- Dropdown menu would go here -->
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
                        <span class="ml-auto bg-beige text-white text-xs px-2 py-1 rounded-full">24</span>
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="sidebar-link">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span class="ml-3">Orders</span>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">8</span>
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
                    <a href="promotions.php" class="sidebar-link">
                        <i class="fas fa-percent w-5"></i>
                        <span class="ml-3">Promotions</span>
                    </a>
                </li>
                <li>
                    <a href="finance.php" class="sidebar-link">
                        <i class="fas fa-wallet w-5"></i>
                        <span class="ml-3">Finances</span>
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
            <button class="w-full flex items-center justify-center px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
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
                <!-- Total Sales -->
                <div class="dashboard-card">
                    <div class="flex items-center">
                        <div class="stat-icon bg-beige">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Sales</p>
                            <p class="text-2xl font-bold text-gray-900">$12,426</p>
                            <p class="text-sm text-green-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +12.5% from last month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <div class="dashboard-card">
                    <div class="flex items-center">
                        <div class="stat-icon bg-blue-500">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">856</p>
                            <p class="text-sm text-green-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +8.2% from last month
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
                            <p class="text-2xl font-bold text-gray-900">124</p>
                            <p class="text-sm text-gray-600">24 active listings</p>
                        </div>
                    </div>
                </div>

                <!-- Customers -->
                <div class="dashboard-card">
                    <div class="flex items-center">
                        <div class="stat-icon bg-green-500">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Customers</p>
                            <p class="text-2xl font-bold text-gray-900">2,345</p>
                            <p class="text-sm text-green-600 flex items-center">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +5.1% from last month
                            </p>
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
                            <a href="#" class="text-beige hover:text-beige-dark text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-beige rounded-full flex items-center justify-center text-white font-semibold">
                                        #1
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Order #12345</p>
                                        <p class="text-sm text-gray-600">John Doe - 2 items</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">$89.99</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Delivered
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-beige rounded-full flex items-center justify-center text-white font-semibold">
                                        #2
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Order #12344</p>
                                        <p class="text-sm text-gray-600">Jane Smith - 1 item</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">$45.50</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Processing
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-beige rounded-full flex items-center justify-center text-white font-semibold">
                                        #3
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Order #12343</p>
                                        <p class="text-sm text-gray-600">Bob Johnson - 3 items</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">$156.75</p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Shipped
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <button class="btn-beige w-full flex flex-col items-center p-4">
                                <i class="fas fa-plus text-2xl mb-2"></i>
                                Add Product
                            </button>
                            <button class="btn-beige-outline w-full flex flex-col items-center p-4">
                                <i class="fas fa-eye text-2xl mb-2"></i>
                                View Orders
                            </button>
                            <button class="btn-beige-outline w-full flex flex-col items-center p-4">
                                <i class="fas fa-chart-bar text-2xl mb-2"></i>
                                View Reports
                            </button>
                            <button class="btn-beige-outline w-full flex flex-col items-center p-4">
                                <i class="fas fa-cog text-2xl mb-2"></i>
                                Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Your Products</h3>
                        <div class="flex space-x-2">
                            <button class="filter-btn">All</button>
                            <button class="filter-btn">Active</button>
                            <button class="filter-btn">Draft</button>
                            <button class="filter-btn">Out of Stock</button>
                        </div>
                    </div>
                </div>
                <div class="product-grid">
                    <!-- Product Card 1 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&h=200&fit=crop" alt="Product">
                            <div class="absolute top-4 left-4 bg-beige text-white px-2 py-1 rounded-md text-sm font-semibold">
                                20% OFF
                            </div>
                            <div class="product-actions">
                                <button><i class="fas fa-eye"></i></button>
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Premium Sneakers</h4>
                            <p class="text-gray-600 text-sm mb-3">Comfortable and stylish sneakers for everyday wear</p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-beige font-bold text-lg">$79.99</span>
                                    <span class="text-gray-500 line-through ml-2">$99.99</span>
                                </div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    In Stock
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=200&fit=crop" alt="Product">
                            <div class="product-actions">
                                <button><i class="fas fa-eye"></i></button>
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Classic Watch</h4>
                            <p class="text-gray-600 text-sm mb-3">Elegant timepiece perfect for any occasion</p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-beige font-bold text-lg">$199.99</span>
                                </div>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    Low Stock
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 3 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=300&h=200&fit=crop" alt="Product">
                            <div class="product-actions">
                                <button><i class="fas fa-eye"></i></button>
                                <button><i class="fas fa-edit"></i></button>
                                <button><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Designer Sunglasses</h4>
                            <p class="text-gray-600 text-sm mb-3">UV protection with premium style</p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-beige font-bold text-lg">$129.99</span>
                                </div>
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    Out of Stock
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

    <script>
        // Sidebar toggle functionality
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

        // Filter buttons functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('bg-beige', 'text-white'));
                btn.classList.add('bg-beige', 'text-white');
            });
        });

        // Set first filter as active
        if (filterBtns.length > 0) {
            filterBtns[0].classList.add('bg-beige', 'text-white');
        }
    </script>
</body>
</html>