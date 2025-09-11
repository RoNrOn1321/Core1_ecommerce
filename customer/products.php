<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Lumino E-commerce</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .product-card {
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        
        .product-image {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .price-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .search-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2px;
        }
        
        .search-inner {
            background: white;
            border-radius: 18px;
        }
        
        .category-badge {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #8b4513;
        }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include 'components/navbar.php'; ?>

<!-- products section starts -->
<section class="container mx-auto px-4 py-8">
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold text-gray-900 mb-4">
            Browse <span class="bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">Products</span>
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">Discover our curated collection of premium products, carefully selected for quality and style.</p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                <div class="search-container">
                    <div class="search-inner">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search for amazing products..." 
                                   class="w-full px-5 py-4 pr-12 border-0 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all text-lg" />
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="min-w-48">
                <label for="categoryFilter" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select id="categoryFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none bg-white">
                    <option value="">All Categories</option>
                </select>
            </div>

            <!-- Price Range -->
            <div class="min-w-56">
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                <div class="flex items-center gap-2">
                    <input type="number" id="minPrice" placeholder="Min" min="0" step="0.01"
                           class="w-20 px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none" />
                    <span class="text-gray-500">to</span>
                    <input type="number" id="maxPrice" placeholder="Max" min="0" step="0.01"
                           class="w-20 px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none" />
                </div>
            </div>

            <!-- Sort By -->
            <div class="min-w-44">
                <label for="sortBy" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select id="sortBy" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none bg-white">
                    <option value="created_at">Newest First</option>
                    <option value="name">Name A-Z</option>
                    <option value="price">Price Low-High</option>
                    <option value="price_desc">Price High-Low</option>
                </select>
            </div>

            <!-- Search Button -->
            <button onclick="loadProducts(1)" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 outline-none">
                <i class="fas fa-search mr-2"></i> Search
            </button>
        </div>
    </div>

    <!-- Results Info -->
    <div class="text-center mb-6 text-gray-600 font-medium" id="resultsInfo"></div>

    <!-- Products Grid -->
    <div id="productsContainer" class="flex justify-center items-center py-20 text-gray-500">
        <div class="text-center">
            <div class="loading-spinner w-12 h-12 border-4 border-amber-200 border-t-amber-600 rounded-full mx-auto mb-4"></div>
            <p class="text-lg font-medium">Loading amazing products...</p>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center items-center gap-2 mt-8" id="pagination"></div>
</section>

<!-- footer section starts -->
<footer class="bg-gray-900 text-white py-12 mt-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                <div class="flex flex-col space-y-2">
                    <a href="index.php" class="text-gray-300 hover:text-white transition-colors">Home</a>
                    <a href="index.php#about" class="text-gray-300 hover:text-white transition-colors">About</a>
                    <a href="products/index.php" class="text-gray-300 hover:text-white transition-colors">Products</a>
                    <a href="index.php#contact" class="text-gray-300 hover:text-white transition-colors">Contact</a>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold mb-4">Account</h3>
                <div class="flex flex-col space-y-2">
                    <a href="account/dashboard.php" class="text-gray-300 hover:text-white transition-colors">My Account</a>
                    <a href="account/orders.php" class="text-gray-300 hover:text-white transition-colors">My Orders</a>
                    <a href="account/wishlist.php" class="text-gray-300 hover:text-white transition-colors">My Wishlist</a>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold mb-4">Support</h3>
                <div class="flex flex-col space-y-2">
                    <a href="support/index.php" class="text-gray-300 hover:text-white transition-colors">Help Center</a>
                    <a href="support/chat.php" class="text-gray-300 hover:text-white transition-colors">Live Chat</a>
                    <a href="support/tickets.php" class="text-gray-300 hover:text-white transition-colors">Support Tickets</a>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold mb-4">Contact Info</h3>
                <div class="flex flex-col space-y-2 text-gray-300">
                    <span><i class="fas fa-phone mr-2"></i>+639-123-45678</span>
                    <span><i class="fas fa-envelope mr-2"></i>support@luminoecommerce.com</span>
                    <span><i class="fas fa-map-marker-alt mr-2"></i>Manila, Philippines</span>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-center">
            <p class="text-gray-400"> Created By <span class="text-amber-600 font-semibold">Lumino Team</span> | All rights reserved © 2024</p>
        </div>
    </div>
</footer>

<script src="assets/js/customer-api.js"></script>
<script>
    let currentPage = 1;
    let totalPages = 1;
    let currentFilters = {};

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadCategories();
        loadProducts(1);
        updateCartCount();

        // Event listeners
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loadProducts(1);
            }
        });

        // Auto-search with debounce
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadProducts(1);
            }, 500);
        });
    });

    async function loadCategories() {
        try {
            const response = await customerAPI.products.getCategories();
            
            if (response.success) {
                const select = document.getElementById('categoryFilter');
                response.data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.slug;
                    option.textContent = `${category.name} (${category.product_count})`;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Failed to load categories:', error);
        }
    }

    async function loadProducts(page = 1) {
        currentPage = page;
        
        // Collect filter values
        const filters = {
            page: page,
            limit: 12,
            search: document.getElementById('searchInput').value,
            category: document.getElementById('categoryFilter').value,
            min_price: document.getElementById('minPrice').value,
            max_price: document.getElementById('maxPrice').value,
            sort_by: document.getElementById('sortBy').value.replace('_desc', ''),
            sort_order: document.getElementById('sortBy').value.includes('_desc') ? 'DESC' : 'ASC'
        };

        currentFilters = filters;

        // Show loading
        document.getElementById('productsContainer').innerHTML = `
            <div class="flex justify-center items-center py-16 text-gray-500">
                <i class="fas fa-spinner fa-spin mr-3 text-2xl"></i> Loading products...
            </div>
        `;

        try {
            const response = await customerAPI.products.getAll(filters);

            if (response.success) {
                renderProducts(response.data);
                renderPagination(response.pagination);
                updateResultsInfo(response.pagination);
            } else {
                showToast('Failed to load products: ' + response.message, 'error');
            }
        } catch (error) {
            showToast('Failed to load products', 'error');
            console.error('Error:', error);
        }
    }

    function renderProducts(products) {
        const container = document.getElementById('productsContainer');

        if (products.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                    <p class="text-gray-500">Try adjusting your search or filters</p>
                </div>
            `;
            return;
        }

        const productsHTML = products.map(product => `
            <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group fade-in">
                <div class="product-image relative h-72 overflow-hidden">
                    <img src="${getProductImage(product.images)}" alt="${product.name}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-4 right-4 flex flex-col gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0">
                        <button onclick="addToWishlist(${product.id})" title="Add to Wishlist"
                                class="w-12 h-12 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-700 hover:text-red-500 hover:bg-white transition-all shadow-lg hover:scale-110">
                            <i class="fas fa-heart text-lg"></i>
                        </button>
                        <button onclick="viewProduct(${product.id})" title="View Details"
                                class="w-12 h-12 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-700 hover:text-blue-500 hover:bg-white transition-all shadow-lg hover:scale-110">
                            <i class="fas fa-eye text-lg"></i>
                        </button>
                    </div>
                    ${product.category_name ? `<div class="absolute top-4 left-4 category-badge px-3 py-1 rounded-full text-sm font-medium">${product.category_name}</div>` : ''}
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-xl text-gray-900 mb-2 line-clamp-2 group-hover:text-amber-600 transition-colors">${product.name}</h3>
                    <p class="price-tag font-black text-2xl mb-3">₱${product.price.toFixed(2)}</p>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed">${product.description}</p>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-sm font-medium ${
                            product.in_stock 
                                ? 'text-green-600 bg-green-50 px-3 py-1 rounded-full' 
                                : 'text-red-600 bg-red-50 px-3 py-1 rounded-full'
                        }">
                            ${product.in_stock ? `✓ ${product.stock_quantity} in stock` : '✗ Out of stock'}
                        </p>
                        <div class="flex items-center text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="text-gray-500 text-sm ml-1">(4.8)</span>
                        </div>
                    </div>
                    <button onclick="addToCart(${product.id}, '${product.name}', ${product.price})"
                            ${!product.in_stock ? 'disabled' : ''}
                            class="w-full py-4 px-6 rounded-xl font-semibold transition-all duration-300 ${
                                product.in_stock 
                                    ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white hover:from-amber-700 hover:to-orange-700 transform hover:-translate-y-1 hover:shadow-lg' 
                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            }">
                        <i class="fas fa-shopping-cart mr-3"></i> 
                        ${product.in_stock ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                </div>
            </div>
        `).join('');

        container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">${productsHTML}</div>`;
    }

    function renderPagination(pagination) {
        const container = document.getElementById('pagination');
        totalPages = pagination.total_pages;

        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = '';

        // Previous button
        paginationHTML += `
            <button onclick="loadProducts(${currentPage - 1})" 
                    ${!pagination.has_prev ? 'disabled' : ''}
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors ${!pagination.has_prev ? 'opacity-50 cursor-not-allowed' : ''}">
                <i class="fas fa-chevron-left mr-1"></i> Previous
            </button>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHTML += `
                    <button onclick="loadProducts(${i})" 
                            class="px-4 py-2 border rounded-lg transition-colors ${i === currentPage 
                                ? 'bg-amber-600 text-white border-amber-600' 
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50'}">
                        ${i}
                    </button>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHTML += '<span class="px-2 py-2 text-gray-500">...</span>';
            }
        }

        // Next button
        paginationHTML += `
            <button onclick="loadProducts(${currentPage + 1})" 
                    ${!pagination.has_next ? 'disabled' : ''}
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors ${!pagination.has_next ? 'opacity-50 cursor-not-allowed' : ''}">
                Next <i class="fas fa-chevron-right ml-1"></i>
            </button>
        `;

        container.innerHTML = paginationHTML;
    }

    function updateResultsInfo(pagination) {
        const info = document.getElementById('resultsInfo');
        const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
        const end = Math.min(start + pagination.per_page - 1, pagination.total);
        
        info.innerHTML = `
            Showing ${start}-${end} of ${pagination.total} products
        `;
    }

    function getProductImage(images) {
        if (images && images.length > 0) {
            const imageUrl = images[0];
            // Check if it's already a full URL
            if (imageUrl.startsWith('http') || imageUrl.startsWith('/')) {
                return imageUrl;
            }
            // Otherwise, prepend the uploads path
            return `/Core1_ecommerce/uploads/${imageUrl}`;
        }
        return '../images/no-image.png';
    }

    async function addToCart(productId, productName, price) {
        try {
            const response = await customerAPI.cart.addItem(productId, 1);

            if (response.success) {
                showToast(`${productName} added to cart!`, 'success');
                updateCartCount();
            } else {
                showToast(response.message, 'error');
            }
        } catch (error) {
            showToast('Failed to add item to cart', 'error');
            console.error('Error:', error);
        }
    }

    function addToWishlist(productId) {
        // TODO: Implement wishlist functionality
        showToast('Wishlist functionality coming soon!', 'info');
    }

    function viewProduct(productId) {
        window.location.href = `products/detail.php?id=${productId}`;
    }
    
    async function updateCartCount() {
        try {
            const response = await customerAPI.cart.getCount();
            if (response.success) {
                // Find cart count element and update it
                const cartCount = document.getElementById('cartCount') || document.querySelector('[data-cart-count]');
                if (cartCount) {
                    cartCount.textContent = response.data.total_quantity;
                }
            }
        } catch (error) {
            console.error('Failed to update cart count:', error);
        }
    }
    
    function showToast(message, type = 'success') {
        // Create toast element if it doesn't exist
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0';
            document.body.appendChild(toast);
        }
        
        // Remove existing classes and add base classes
        toast.className = 'fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0';
        
        // Add type-specific classes
        switch(type) {
            case 'success':
                toast.classList.add('bg-green-500', 'text-white');
                break;
            case 'error':
                toast.classList.add('bg-red-500', 'text-white');
                break;
            case 'info':
                toast.classList.add('bg-blue-500', 'text-white');
                break;
            default:
                toast.classList.add('bg-green-500', 'text-white');
        }
        
        toast.textContent = message;
        toast.style.display = 'block';
        
        // Show toast with animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 10);

        // Hide toast after 3 seconds
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.style.display = 'none';
            }, 300);
        }, 3000);
    }
</script>

</body>
</html>