<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Seller Dashboard</title>
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
                <li><a href="orders.php" class="sidebar-link"><i class="fas fa-shopping-cart w-5"></i><span class="ml-3">Orders</span><span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">8</span></a></li>
                <li><a href="customers.php" class="sidebar-link active"><i class="fas fa-users w-5"></i><span class="ml-3">Customers</span></a></li>
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
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Customers</h1>
                <p class="text-gray-600">Manage your customer relationships and view customer insights</p>
            </div>

            <!-- Customer Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-beige rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Customers</p>
                            <p class="text-2xl font-bold text-gray-900">2,345</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">New This Month</p>
                            <p class="text-2xl font-bold text-gray-900">124</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-redo text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Returning</p>
                            <p class="text-2xl font-bold text-gray-900">856</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">VIP Customers</p>
                            <p class="text-2xl font-bold text-gray-900">67</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="Search customers..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>All Customers</option>
                            <option>VIP Customers</option>
                            <option>New Customers</option>
                            <option>Returning Customers</option>
                            <option>Inactive</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>Sort by Name</option>
                            <option>Sort by Orders</option>
                            <option>Sort by Spent</option>
                            <option>Sort by Join Date</option>
                        </select>
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Customer Card 1 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=60&h=60&fit=crop&crop=face" 
                             alt="Customer" class="w-16 h-16 rounded-full">
                        <div class="ml-4 flex-1">
                            <h3 class="font-semibold text-gray-900">John Doe</h3>
                            <p class="text-sm text-gray-600">john.doe@email.com</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mt-1">
                                VIP Customer
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="font-semibold text-gray-900">24</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="font-semibold text-beige">$1,856</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Order</p>
                            <p class="font-semibold text-gray-900">Jan 15, 2024</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="font-semibold text-gray-900">Mar 2023</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex space-x-2">
                            <button class="flex-1 btn-beige text-sm py-2">
                                <i class="fas fa-eye mr-2"></i>View Profile
                            </button>
                            <button class="px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer Card 2 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b5bc?w=60&h=60&fit=crop&crop=face" 
                             alt="Customer" class="w-16 h-16 rounded-full">
                        <div class="ml-4 flex-1">
                            <h3 class="font-semibold text-gray-900">Jane Smith</h3>
                            <p class="text-sm text-gray-600">jane.smith@email.com</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mt-1">
                                Regular Customer
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="font-semibold text-gray-900">8</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="font-semibold text-beige">$456</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Order</p>
                            <p class="font-semibold text-gray-900">Jan 14, 2024</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="font-semibold text-gray-900">Aug 2023</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex space-x-2">
                            <button class="flex-1 btn-beige text-sm py-2">
                                <i class="fas fa-eye mr-2"></i>View Profile
                            </button>
                            <button class="px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer Card 3 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face" 
                             alt="Customer" class="w-16 h-16 rounded-full">
                        <div class="ml-4 flex-1">
                            <h3 class="font-semibold text-gray-900">Bob Johnson</h3>
                            <p class="text-sm text-gray-600">bob.johnson@email.com</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mt-1">
                                New Customer
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="font-semibold text-gray-900">2</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="font-semibold text-beige">$189</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Order</p>
                            <p class="font-semibold text-gray-900">Jan 13, 2024</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="font-semibold text-gray-900">Jan 2024</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex space-x-2">
                            <button class="flex-1 btn-beige text-sm py-2">
                                <i class="fas fa-eye mr-2"></i>View Profile
                            </button>
                            <button class="px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer Card 4 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&h=60&fit=crop&crop=face" 
                             alt="Customer" class="w-16 h-16 rounded-full">
                        <div class="ml-4 flex-1">
                            <h3 class="font-semibold text-gray-900">Sarah Wilson</h3>
                            <p class="text-sm text-gray-600">sarah.wilson@email.com</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 mt-1">
                                VIP Customer
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="font-semibold text-gray-900">31</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="font-semibold text-beige">$2,145</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Order</p>
                            <p class="font-semibold text-gray-900">Jan 12, 2024</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="font-semibold text-gray-900">Feb 2023</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex space-x-2">
                            <button class="flex-1 btn-beige text-sm py-2">
                                <i class="fas fa-eye mr-2"></i>View Profile
                            </button>
                            <button class="px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer Card 5 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=60&h=60&fit=crop&crop=face" 
                             alt="Customer" class="w-16 h-16 rounded-full">
                        <div class="ml-4 flex-1">
                            <h3 class="font-semibold text-gray-900">Mike Davis</h3>
                            <p class="text-sm text-gray-600">mike.davis@email.com</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mt-1">
                                Regular Customer
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="font-semibold text-gray-900">12</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="font-semibold text-beige">$678</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Order</p>
                            <p class="font-semibold text-gray-900">Jan 10, 2024</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="font-semibold text-gray-900">Jun 2023</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex space-x-2">
                            <button class="flex-1 btn-beige text-sm py-2">
                                <i class="fas fa-eye mr-2"></i>View Profile
                            </button>
                            <button class="px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer Card 6 -->
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=60&h=60&fit=crop&crop=face" 
                             alt="Customer" class="w-16 h-16 rounded-full">
                        <div class="ml-4 flex-1">
                            <h3 class="font-semibold text-gray-900">Emily Brown</h3>
                            <p class="text-sm text-gray-600">emily.brown@email.com</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 mt-1">
                                Inactive
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="font-semibold text-gray-900">5</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="font-semibold text-beige">$234</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Order</p>
                            <p class="font-semibold text-gray-900">Oct 15, 2023</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="font-semibold text-gray-900">May 2023</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex space-x-2">
                            <button class="flex-1 btn-beige text-sm py-2">
                                <i class="fas fa-eye mr-2"></i>View Profile
                            </button>
                            <button class="px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50">
                                <i class="fas fa-envelope"></i>
                            </button>
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