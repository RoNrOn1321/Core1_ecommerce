<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Seller Dashboard</title>
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
                <li><a href="products.php" class="sidebar-link active"><i class="fas fa-box w-5"></i><span class="ml-3">Products</span><span class="ml-auto bg-beige text-white text-xs px-2 py-1 rounded-full">24</span></a></li>
                <li><a href="orders.php" class="sidebar-link"><i class="fas fa-shopping-cart w-5"></i><span class="ml-3">Orders</span><span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">8</span></a></li>
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
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Products</h1>
                    <p class="text-gray-600">Manage your product inventory and listings</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button id="addProductBtn" class="btn-beige">
                        <i class="fas fa-plus mr-2"></i>Add New Product
                    </button>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="Search products..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="flex flex-wrap gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>All Categories</option>
                            <option>Electronics</option>
                            <option>Clothing</option>
                            <option>Books</option>
                            <option>Home & Garden</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Draft</option>
                            <option>Out of Stock</option>
                        </select>
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-beige text-white">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox" class="rounded">
                                </th>
                                <th class="px-6 py-4 text-left">Product</th>
                                <th class="px-6 py-4 text-left">Category</th>
                                <th class="px-6 py-4 text-left">Price</th>
                                <th class="px-6 py-4 text-left">Stock</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-left">Created</th>
                                <th class="px-6 py-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><input type="checkbox" class="rounded"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=60&h=60&fit=crop" alt="Product" class="w-12 h-12 rounded-lg object-cover">
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">Premium Sneakers</div>
                                            <div class="text-sm text-gray-500">SKU: PSN001</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">Footwear</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">$79.99</div>
                                    <div class="text-sm text-gray-500 line-through">$99.99</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">45</div>
                                    <div class="text-sm text-gray-500">units</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">2024-01-15</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                                        <button class="text-beige hover:text-beige-dark"><i class="fas fa-edit"></i></button>
                                        <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><input type="checkbox" class="rounded"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=60&h=60&fit=crop" alt="Product" class="w-12 h-12 rounded-lg object-cover">
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">Classic Watch</div>
                                            <div class="text-sm text-gray-500">SKU: CW002</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">Accessories</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">$199.99</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">8</div>
                                    <div class="text-sm text-gray-500">units</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">2024-01-10</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                                        <button class="text-beige hover:text-beige-dark"><i class="fas fa-edit"></i></button>
                                        <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4"><input type="checkbox" class="rounded"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=60&h=60&fit=crop" alt="Product" class="w-12 h-12 rounded-lg object-cover">
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">Designer Sunglasses</div>
                                            <div class="text-sm text-gray-500">SKU: DS003</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">Accessories</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">$129.99</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">0</div>
                                    <div class="text-sm text-gray-500">units</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Out of Stock</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">2024-01-05</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                                        <button class="text-beige hover:text-beige-dark"><i class="fas fa-edit"></i></button>
                                        <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing 1 to 3 of 24 results
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
                        <button class="px-3 py-2 text-sm bg-beige text-white rounded-lg">1</button>
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                        <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Add New Product</h3>
                    <button id="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Enter product name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    <option>Select Category</option>
                                    <option>Electronics</option>
                                    <option>Clothing</option>
                                    <option>Books</option>
                                    <option>Home & Garden</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Product SKU">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price ($)</label>
                                <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="0">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Product description"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" id="cancelBtn" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="btn-beige">Save Product</button>
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

        // Modal functionality
        const addProductBtn = document.getElementById('addProductBtn');
        const productModal = document.getElementById('productModal');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');

        addProductBtn.addEventListener('click', () => {
            productModal.classList.remove('hidden');
        });

        closeModal.addEventListener('click', () => {
            productModal.classList.add('hidden');
        });

        cancelBtn.addEventListener('click', () => {
            productModal.classList.add('hidden');
        });

        productModal.addEventListener('click', (e) => {
            if (e.target === productModal) {
                productModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>