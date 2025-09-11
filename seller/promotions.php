<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions - Seller Dashboard</title>
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
                <li><a href="customers.php" class="sidebar-link"><i class="fas fa-users w-5"></i><span class="ml-3">Customers</span></a></li>
                <li><a href="analytics.php" class="sidebar-link"><i class="fas fa-chart-line w-5"></i><span class="ml-3">Analytics</span></a></li>
                <li><a href="reviews.php" class="sidebar-link"><i class="fas fa-star w-5"></i><span class="ml-3">Reviews</span></a></li>
                <li><a href="promotions.php" class="sidebar-link active"><i class="fas fa-percent w-5"></i><span class="ml-3">Promotions</span></a></li>
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
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Promotions & Discounts</h1>
                    <p class="text-gray-600">Create and manage promotional campaigns to boost your sales</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button id="createPromoBtn" class="btn-beige">
                        <i class="fas fa-plus mr-2"></i>Create Promotion
                    </button>
                </div>
            </div>

            <!-- Promotion Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Active Promotions</p>
                            <p class="text-2xl font-bold text-gray-900">8</p>
                            <p class="text-sm text-green-600">+2 this week</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Promo Sales</p>
                            <p class="text-2xl font-bold text-gray-900">$4,256</p>
                            <p class="text-sm text-green-600">+18.4%</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Customers Reached</p>
                            <p class="text-2xl font-bold text-gray-900">1,342</p>
                            <p class="text-sm text-blue-600">34% of total</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-percentage text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Conversion Rate</p>
                            <p class="text-2xl font-bold text-gray-900">12.8%</p>
                            <p class="text-sm text-green-600">+3.2%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Promotion Templates -->
            <div class="bg-white rounded-lg shadow-md mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Quick Start Templates</h3>
                    <p class="text-sm text-gray-600 mt-1">Choose from popular promotion types</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-beige transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-percent text-red-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Percentage Discount</h4>
                            <p class="text-sm text-gray-600">Give customers a percentage off their purchase</p>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-beige transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Fixed Amount Off</h4>
                            <p class="text-sm text-gray-600">Offer a fixed dollar amount discount</p>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-beige transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-gift text-blue-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Buy One Get One</h4>
                            <p class="text-sm text-gray-600">BOGO deals and bundle offers</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Promotions -->
            <div class="bg-white rounded-lg shadow-md mb-8">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Active Promotions</h3>
                        <div class="flex space-x-2">
                            <button class="filter-btn bg-beige text-white">All</button>
                            <button class="filter-btn">Percentage</button>
                            <button class="filter-btn">Fixed Amount</button>
                            <button class="filter-btn">BOGO</button>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    <!-- Promotion 1 -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-percent text-red-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">Summer Sale 2024</h4>
                                    <p class="text-sm text-gray-600">20% off all summer products</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                                <div class="flex space-x-2">
                                    <button class="text-beige hover:text-beige-dark">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Discount</p>
                                <p class="font-semibold text-gray-900">20%</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Valid Until</p>
                                <p class="font-semibold text-gray-900">Aug 31, 2024</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Uses</p>
                                <p class="font-semibold text-gray-900">134 / 500</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Revenue</p>
                                <p class="font-semibold text-beige">$1,856</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-beige h-2 rounded-full" style="width: 26.8%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">26.8% of usage limit reached</p>
                        </div>
                    </div>

                    <!-- Promotion 2 -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">Free Shipping Weekend</h4>
                                    <p class="text-sm text-gray-600">Free shipping on orders over $50</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Scheduled
                                </span>
                                <div class="flex space-x-2">
                                    <button class="text-beige hover:text-beige-dark">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Discount</p>
                                <p class="font-semibold text-gray-900">Free Shipping</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Starts</p>
                                <p class="font-semibold text-gray-900">Aug 10, 2024</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Min Order</p>
                                <p class="font-semibold text-gray-900">$50</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Expected Revenue</p>
                                <p class="font-semibold text-beige">$2,400</p>
                            </div>
                        </div>
                    </div>

                    <!-- Promotion 3 -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-gift text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">Buy 2 Get 1 Free</h4>
                                    <p class="text-sm text-gray-600">On selected accessories</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Ending Soon
                                </span>
                                <div class="flex space-x-2">
                                    <button class="text-beige hover:text-beige-dark">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Discount</p>
                                <p class="font-semibold text-gray-900">Buy 2 Get 1</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Ends</p>
                                <p class="font-semibold text-red-600">Tomorrow</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Uses</p>
                                <p class="font-semibold text-gray-900">89 / 200</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Revenue</p>
                                <p class="font-semibold text-beige">$945</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 44.5%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">44.5% of usage limit reached</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Performance -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Performance Insights</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">Top Performing Promotions</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-trophy text-green-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">Summer Sale 2024</p>
                                            <p class="text-sm text-gray-600">$1,856 revenue</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 font-semibold">+43%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-medal text-yellow-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">Buy 2 Get 1 Free</p>
                                            <p class="text-sm text-gray-600">$945 revenue</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 font-semibold">+28%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-award text-orange-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">Flash Sale Monday</p>
                                            <p class="text-sm text-gray-600">$687 revenue</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 font-semibold">+19%</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">Promotion Tips</h4>
                            <div class="space-y-3">
                                <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                    <p class="text-sm font-medium text-blue-800">Limited Time Offers</p>
                                    <p class="text-sm text-blue-700">Create urgency with time-limited promotions to boost conversions.</p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg border-l-4 border-green-400">
                                    <p class="text-sm font-medium text-green-800">Minimum Order Value</p>
                                    <p class="text-sm text-green-700">Set minimum order requirements to increase average order value.</p>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg border-l-4 border-purple-400">
                                    <p class="text-sm font-medium text-purple-800">Target Specific Products</p>
                                    <p class="text-sm text-purple-700">Focus promotions on slow-moving inventory to clear stock.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Promotion Modal -->
    <div id="promotionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Create New Promotion</h3>
                    <button id="closePromoModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Promotion Name</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Enter promotion name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Promotion Type</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                        <option>Percentage Discount</option>
                                        <option>Fixed Amount Off</option>
                                        <option>Free Shipping</option>
                                        <option>Buy One Get One</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label>
                                    <div class="flex">
                                        <input type="number" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:border-beige" placeholder="20">
                                        <span class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Usage Limit</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Describe your promotion..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" id="cancelPromoBtn" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="btn-beige">Create Promotion</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

        // Promotion modal functionality
        const createPromoBtn = document.getElementById('createPromoBtn');
        const promotionModal = document.getElementById('promotionModal');
        const closePromoModal = document.getElementById('closePromoModal');
        const cancelPromoBtn = document.getElementById('cancelPromoBtn');

        createPromoBtn.addEventListener('click', () => {
            promotionModal.classList.remove('hidden');
        });

        closePromoModal.addEventListener('click', () => {
            promotionModal.classList.add('hidden');
        });

        cancelPromoBtn.addEventListener('click', () => {
            promotionModal.classList.add('hidden');
        });

        promotionModal.addEventListener('click', (e) => {
            if (e.target === promotionModal) {
                promotionModal.classList.add('hidden');
            }
        });

        // Filter buttons functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('bg-beige', 'text-white'));
                btn.classList.add('bg-beige', 'text-white');
            });
        });
    </script>
</body>
</html>