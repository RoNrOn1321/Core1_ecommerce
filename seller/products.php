<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$auth = new SellerAuth($pdo);

// Check if logged in, redirect to login if not
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$sellerInfo = $auth->getCurrentSeller();
?>
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
                        <span class="hidden md:block"><?php echo htmlspecialchars(($sellerInfo['first_name'] ?? '') . ' ' . ($sellerInfo['last_name'] ?? '') ?: 'Seller'); ?></span>
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
                            <input type="text" id="searchInput" placeholder="Search products..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="flex flex-wrap gap-2">
                        <select id="categoryFilter" data-categories class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option value="">All Categories</option>
                        </select>
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="productsTable" class="w-full">
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
                            <tr>
                                <td colspan="8" class="text-center py-8 text-gray-500">Loading products...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-container px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="results-text text-sm text-gray-700">
                        Loading...
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="pagination-prev px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" onclick="loadProducts(currentPage - 1, currentFilters)">Previous</button>
                        <span id="pageNumbers"></span>
                        <button class="pagination-next px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50" onclick="loadProducts(currentPage + 1, currentFilters)">Next</button>
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
                    <form id="productForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Enter product name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select name="category_id" data-categories class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                <input type="text" name="sku" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Product SKU">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price ($) *</label>
                                <input type="number" name="price" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Compare Price ($)</label>
                                <input type="number" name="compare_price" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="0" value="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                                <textarea name="short_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Brief product description"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Detailed product description"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>
                                <div id="imageUploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-beige transition-colors">
                                    <input type="file" id="imageInput" multiple accept="image/*" class="hidden">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, GIF, WebP up to 10MB each</p>
                                </div>
                                <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                                <input type="hidden" name="images" id="imagesInput">
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
        // Global variables
        let products = [];
        let categories = [];
        let currentPage = 1;
        let totalPages = 1;
        let currentFilters = {};

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
            const form = document.getElementById('productForm');
            form.reset();
            delete form.dataset.productId;
            uploadedImages = [];
            updateImagePreview();
            updateImagesInput();
            document.querySelector('#productModal h3').textContent = 'Add New Product';
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

        // API Functions
        async function loadProducts(page = 1, filters = {}) {
            try {
                const params = new URLSearchParams({
                    limit: 20,
                    offset: (page - 1) * 20,
                    ...filters
                });
                
                const response = await fetch(`api/products/index.php?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    products = data.products || [];
                    totalPages = Math.ceil(data.total / 20);
                    currentPage = page;
                    renderProducts();
                    renderPagination();
                } else {
                    showAlert('Error loading products: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error loading products', 'error');
            }
        }

        async function loadCategories() {
            try {
                const response = await fetch('api/products/categories.php');
                const data = await response.json();
                
                if (data.success) {
                    categories = data.categories || [];
                    renderCategoryOptions();
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        async function saveProduct(formData) {
            try {
                const response = await fetch('api/products/index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    productModal.classList.add('hidden');
                    showAlert('Product saved successfully', 'success');
                    loadProducts(currentPage, currentFilters);
                } else {
                    showAlert('Error saving product: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error saving product', 'error');
            }
        }

        async function updateProduct(productId, formData) {
            try {
                const response = await fetch(`api/products/detail.php?id=${productId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    productModal.classList.add('hidden');
                    showAlert('Product updated successfully', 'success');
                    loadProducts(currentPage, currentFilters);
                    delete document.getElementById('productForm').dataset.productId;
                } else {
                    showAlert('Error updating product: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error updating product', 'error');
            }
        }

        // Render functions
        function renderProducts() {
            const tbody = document.querySelector('#productsTable tbody');
            if (!tbody) return;
            
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-gray-500">No products found</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><input type="checkbox" class="rounded" data-product-id="${product.id}"></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img src="${product.primary_image || 'https://via.placeholder.com/60'}" alt="${product.name}" class="w-12 h-12 rounded-lg object-cover" onerror="this.src='https://via.placeholder.com/60'">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">${product.name}</div>
                                <div class="text-sm text-gray-500">SKU: ${product.sku || 'N/A'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700">${product.category_name || 'Uncategorized'}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">$${parseFloat(product.price).toFixed(2)}</div>
                        ${product.compare_price ? `<div class="text-sm text-gray-500 line-through">$${parseFloat(product.compare_price).toFixed(2)}</div>` : ''}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">${product.stock_quantity}</div>
                        <div class="text-sm text-gray-500">units</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(product.status, product.stock_quantity)}">
                            ${getStatusText(product.status, product.stock_quantity)}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">${new Date(product.created_at).toLocaleDateString()}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button onclick="viewProduct(${product.id})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                            <button onclick="editProduct(${product.id})" class="text-beige hover:text-beige-dark"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function renderCategoryOptions() {
            const selects = document.querySelectorAll('select[data-categories]');
            selects.forEach(select => {
                const currentValue = select.value;
                const placeholder = select.querySelector('option').textContent;
                select.innerHTML = `<option value="">${placeholder}</option>` +
                    categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
                if (currentValue) select.value = currentValue;
            });
        }

        function renderPagination() {
            const paginationContainer = document.querySelector('.pagination-container');
            if (!paginationContainer) return;
            
            const totalText = document.querySelector('.results-text');
            if (totalText) {
                const startItem = (currentPage - 1) * 20 + 1;
                const endItem = Math.min(currentPage * 20, products.length);
                totalText.textContent = `Showing ${startItem} to ${endItem} of ${products.length} results`;
            }
            
            const prevBtn = document.querySelector('.pagination-prev');
            const nextBtn = document.querySelector('.pagination-next');
            
            if (prevBtn) prevBtn.disabled = currentPage === 1;
            if (nextBtn) nextBtn.disabled = currentPage === totalPages;
        }

        // Utility functions
        function getStatusClass(status, stock) {
            if (status === 'active' && stock > 0) return 'bg-green-100 text-green-800';
            if (status === 'active' && stock <= 5) return 'bg-yellow-100 text-yellow-800';
            if (stock === 0) return 'bg-red-100 text-red-800';
            return 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status, stock) {
            if (stock === 0) return 'Out of Stock';
            if (stock <= 5) return 'Low Stock';
            if (status === 'active') return 'Active';
            return status.charAt(0).toUpperCase() + status.slice(1);
        }

        function showAlert(message, type = 'info') {
            const alert = document.createElement('div');
            alert.className = `fixed top-4 right-4 z-50 p-4 rounded-lg ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} text-white`;
            alert.textContent = message;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 3000);
        }

        // Product actions
        function viewProduct(id) {
            window.open(`api/products/detail.php?id=${id}`, '_blank');
        }

        async function editProduct(id) {
            try {
                const response = await fetch(`api/products/detail.php?id=${id}`);
                const data = await response.json();
                
                if (data.success) {
                    const product = data.product;
                    
                    document.querySelector('input[name="name"]').value = product.name || '';
                    document.querySelector('select[name="category_id"]').value = product.category_id || '';
                    document.querySelector('input[name="sku"]').value = product.sku || '';
                    document.querySelector('input[name="price"]').value = product.price || '';
                    document.querySelector('input[name="compare_price"]').value = product.compare_price || '';
                    document.querySelector('input[name="stock_quantity"]').value = product.stock_quantity || '0';
                    document.querySelector('select[name="status"]').value = product.status || 'draft';
                    document.querySelector('textarea[name="short_description"]').value = product.short_description || '';
                    document.querySelector('textarea[name="description"]').value = product.description || '';
                    
                    document.querySelector('#productModal h3').textContent = 'Edit Product';
                    document.getElementById('productForm').dataset.productId = id;
                    productModal.classList.remove('hidden');
                } else {
                    showAlert('Error loading product: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error loading product', 'error');
            }
        }

        async function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                try {
                    const response = await fetch(`api/products/detail.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        showAlert('Product deleted successfully', 'success');
                        loadProducts(currentPage, currentFilters);
                    } else {
                        showAlert('Error deleting product: ' + data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Error deleting product', 'error');
                }
            }
        }

        // Form handling
        document.getElementById('productForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const productData = Object.fromEntries(formData.entries());
            
            // Convert numeric fields
            if (productData.price) productData.price = parseFloat(productData.price);
            if (productData.compare_price) productData.compare_price = parseFloat(productData.compare_price);
            if (productData.stock_quantity) productData.stock_quantity = parseInt(productData.stock_quantity);
            if (productData.category_id) productData.category_id = parseInt(productData.category_id);
            
            // Add images if any uploaded
            if (uploadedImages.length > 0) {
                productData.images = uploadedImages;
            }
            
            const productId = form.dataset.productId;
            if (productId) {
                await updateProduct(productId, productData);
            } else {
                await saveProduct(productData);
            }
        });

        // Search and filter handling
        document.getElementById('searchInput').addEventListener('input', debounce((e) => {
            currentFilters.search = e.target.value;
            loadProducts(1, currentFilters);
        }, 300));

        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            currentFilters.category_id = e.target.value;
            loadProducts(1, currentFilters);
        });

        document.getElementById('statusFilter').addEventListener('change', (e) => {
            currentFilters.status = e.target.value;
            loadProducts(1, currentFilters);
        });

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Image upload functionality
        let uploadedImages = [];
        
        document.getElementById('imageUploadArea').addEventListener('click', () => {
            document.getElementById('imageInput').click();
        });
        
        document.getElementById('imageInput').addEventListener('change', handleImageUpload);
        
        // Drag and drop functionality
        const uploadArea = document.getElementById('imageUploadArea');
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-beige', 'bg-beige-light/10');
        });
        
        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-beige', 'bg-beige-light/10');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-beige', 'bg-beige-light/10');
            
            const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            if (files.length > 0) {
                handleImageFiles(files);
            }
        });
        
        async function handleImageUpload(e) {
            const files = Array.from(e.target.files);
            await handleImageFiles(files);
        }
        
        async function handleImageFiles(files) {
            for (const file of files) {
                if (file.size > 10 * 1024 * 1024) {
                    showAlert(`File ${file.name} is too large. Maximum size is 10MB.`, 'error');
                    continue;
                }
                
                await uploadImage(file);
            }
        }
        
        async function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const response = await fetch('api/products/upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    console.log('Image uploaded successfully:', data);
                    uploadedImages.push({
                        url: data.image_url,
                        filename: data.filename,
                        alt_text: ''
                    });
                    updateImagePreview();
                    updateImagesInput();
                    showAlert('Image uploaded successfully', 'success');
                } else {
                    showAlert('Error uploading image: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error uploading image:', error);
                showAlert('Error uploading image', 'error');
            }
        }
        
        function updateImagePreview() {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = uploadedImages.map((image, index) => `
                <div class="relative group">
                    <img src="${image.url}" alt="Product image" class="w-full h-24 object-cover rounded-lg" 
                         onerror="this.src='https://via.placeholder.com/200x150?text=Image+Error'; console.log('Image failed to load:', '${image.url}');">
                    <button type="button" onclick="removeImage(${index})" 
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                        ×
                    </button>
                    <input type="text" placeholder="Alt text" value="${image.alt_text}" 
                           onchange="updateImageAlt(${index}, this.value)"
                           class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg">
                </div>
            `).join('');
        }
        
        function removeImage(index) {
            uploadedImages.splice(index, 1);
            updateImagePreview();
            updateImagesInput();
        }
        
        function updateImageAlt(index, altText) {
            uploadedImages[index].alt_text = altText;
            updateImagesInput();
        }
        
        function updateImagesInput() {
            document.getElementById('imagesInput').value = JSON.stringify(uploadedImages);
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadCategories();
            loadProducts();
        });
    </script>
</body>
</html>