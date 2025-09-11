<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Core1 E-commerce</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50">

<?php include 'components/navbar.php'; ?>

<!-- products section starts -->
<section class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-center text-gray-900 mb-8">
        Browse <span class="text-amber-600">Products</span>
    </h1>

    <!-- Search and Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                <input type="text" id="searchInput" placeholder="Search products..." 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" />
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
    <div id="productsContainer" class="flex justify-center items-center py-16 text-gray-500">
        <i class="fas fa-spinner fa-spin mr-3 text-2xl"></i> Loading products...
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
                    <span><i class="fas fa-envelope mr-2"></i>support@core1ecommerce.com</span>
                    <span><i class="fas fa-map-marker-alt mr-2"></i>Manila, Philippines</span>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-center">
            <p class="text-gray-400"> Created By <span class="text-amber-600 font-semibold">Core1 Team</span> | All rights reserved © 2024</p>
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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow group">
                <div class="relative h-64 overflow-hidden">
                    <img src="${getProductImage(product.images)}" alt="${product.name}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="addToWishlist(${product.id})" title="Add to Wishlist"
                                class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center text-gray-600 hover:text-red-500 hover:bg-white transition-all shadow-sm">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button onclick="viewProduct(${product.id})" title="View Details"
                                class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center text-gray-600 hover:text-blue-500 hover:bg-white transition-all shadow-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg text-gray-900 mb-1 line-clamp-2">${product.name}</h3>
                    <p class="text-sm text-gray-500 mb-2">${product.category_name || 'Uncategorized'}</p>
                    <p class="font-bold text-xl text-amber-600 mb-3">₱${product.price.toFixed(2)}</p>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">${product.description}</p>
                    <p class="text-sm mb-4 ${product.in_stock ? 'text-green-600' : 'text-red-500'}">
                        ${product.in_stock ? `${product.stock_quantity} in stock` : 'Out of stock'}
                    </p>
                    <button onclick="addToCart(${product.id}, '${product.name}', ${product.price})"
                            ${!product.in_stock ? 'disabled' : ''}
                            class="w-full py-3 px-4 rounded-lg font-medium transition-all ${
                                product.in_stock 
                                    ? 'bg-amber-600 text-white hover:bg-amber-700 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2' 
                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            }">
                        <i class="fas fa-shopping-cart mr-2"></i> 
                        ${product.in_stock ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                </div>
            </div>
        `).join('');

        container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">${productsHTML}</div>`;
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
        return 'images/no-image.png';
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
</script>

</body>
</html>