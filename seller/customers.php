<?php
$page_title = "Customers";
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

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

<?php include 'includes/footer.php'; ?>