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
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .filters-container {
            background: #f8f9fa;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 1rem;
        }

        .filters-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 150px;
        }

        .filter-group label {
            font-weight: 500;
            font-size: 1.2rem;
            color: #333;
        }

        .filter-group input, .filter-group select {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 1rem;
            background: white;
        }

        .filter-group input:focus, .filter-group select:focus {
            outline: none;
            border-color: #b48d6b;
            box-shadow: 0 0 0 2px rgba(180, 141, 107, 0.1);
        }

        .price-range {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .price-range input {
            max-width: 100px;
        }

        .search-container {
            flex: 1;
            min-width: 200px;
        }

        .search-container input {
            width: 100%;
            padding: 1rem;
            font-size: 1.2rem;
            border: 2px solid #ddd;
            border-radius: 0.8rem;
        }

        .apply-filters-btn {
            background: #b48d6b;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .apply-filters-btn:hover {
            background: #8b6b4d;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }

        .pagination button {
            padding: 0.8rem 1.2rem;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .pagination button:hover {
            background: #f8f9fa;
        }

        .pagination button.active {
            background: #b48d6b;
            color: white;
            border-color: #b48d6b;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .results-info {
            text-align: center;
            margin: 1rem 0;
            font-size: 1.2rem;
            color: #666;
        }

        .loading {
            text-align: center;
            padding: 3rem;
            font-size: 1.5rem;
            color: #666;
        }

        .no-products {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .product-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .product-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .product-card:hover .product-actions {
            opacity: 1;
        }

        .product-actions button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.9);
            color: #333;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .product-actions button:hover {
            background: #b48d6b;
            color: white;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .product-category {
            font-size: 1rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: #b48d6b;
            margin-bottom: 1rem;
        }

        .product-description {
            font-size: 1rem;
            color: #666;
            line-height: 1.4;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .stock-info {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .in-stock {
            color: #28a745;
        }

        .out-of-stock {
            color: #dc3545;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 1rem;
            background: #b48d6b;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .add-to-cart-btn:hover {
            background: #8b6b4d;
        }

        .add-to-cart-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: none;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }

        .toast.error {
            background: #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .filters-row {
                flex-direction: column;
                gap: 1rem;
            }

            .filter-group {
                width: 100%;
            }

            .price-range {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<!-- header section -->
<header>
    <input type="checkbox" name="" id="toggler">
    <label for="toggler" class="fas fa-bars"></label>
    <img class="logo" src="../images/logo1.png"> <a class="logo">Lumino<span>.</span></a>

    <nav class="navbar">
        <a href="../index.php">home</a>
        <a href="../index.php#about">about</a>
        <a href="index.php">products</a>
        <a href="../index.php#contact">contact</a>
    </nav>

    <div class="icons">
        <a href="#" class="fas fa-heart"></a>
        <button id="cartIcon" class="fas fa-shopping-cart" style="background:none; border:none; font-size:2.5rem; color:#333; margin-left:1.5rem; cursor:pointer; position:relative;">
            <span id="cartCount" style="position:absolute; top:-5px; right:-5px; background:#e74c3c; color:white; width:20px; height:20px; border-radius:50%; font-size:12px; display:flex; align-items:center; justify-content:center;">0</span>
        </button>
        <a href="#" class="fas fa-user"></a>
    </div>
</header>

<!-- header section ends -->

<!-- products section starts -->
<section class="products" style="padding: 5rem 9%; background: #f8f9fa;">
    <h1 class="heading" style="text-align: center; margin-bottom: 3rem;"> Browse <span>Products</span> </h1>

    <!-- Filters Section -->
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
<section class="footer">
    <div class="box-container">
        <div class="box">
            <h3>Quick Links</h3>
            <a href="../index.php">Home</a>
            <a href="../index.php#about">About</a>
            <a href="index.php">Products</a>
            <a href="../index.php#contact">Contact</a>
        </div>

        <div class="box">
            <h3>Extra Links</h3>
            <a href="../account/dashboard.php">My Account</a>
            <a href="../account/orders.php">My Orders</a>
            <a href="../account/wishlist.php">My Wishlist</a>
        </div>

        <div class="box">
            <h3>Contact Info</h3>
            <a href="#">+639-123-45678</a>
            <a href="#">support@luminoecommerce.com</a>
            <a href="#">Manila, Philippines</a>
        </div>
    </div>

    <div class="credit"> Created By <span> Lumino Team</span> | All rights reserved</div>
</section>

<script src="../assets/js/customer-api.js"></script>
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
            const response = await fetch('/Core1_ecommerce/customer/api/products/categories');
            const result = await response.json();
            
            if (result.success) {
                const select = document.getElementById('categoryFilter');
                result.data.forEach(category => {
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
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i> Loading products...
            </div>
        `;

        try {
            const queryString = new URLSearchParams(Object.entries(filters).filter(([k, v]) => v));
            const response = await fetch(`/Core1_ecommerce/customer/api/products?${queryString}`);
            const result = await response.json();

            if (result.success) {
                renderProducts(result.data);
                renderPagination(result.pagination);
                updateResultsInfo(result.pagination);
            } else {
                showError('Failed to load products: ' + result.message);
            }
        } catch (error) {
            showError('Failed to load products');
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
            return `/Core1_ecommerce/uploads/${images[0]}`;
        }
        return '../images/no-image.png';
    }

    async function addToCart(productId, productName, price) {
        try {
            const response = await customerAPI.post('/cart/add', {
                product_id: productId,
                quantity: 1
            });

            if (response.success) {
                showSuccess(`${productName} added to cart!`);
                updateCartCount();
            } else {
                showError(response.message);
            }
        } catch (error) {
            showError('Failed to add item to cart');
            console.error('Error:', error);
        }
    }

    async function updateCartCount() {
        try {
            const response = await customerAPI.get('/cart/count');
            if (response.success) {
                document.getElementById('cartCount').textContent = response.data.total_quantity;
            }
        } catch (error) {
            console.error('Failed to update cart count:', error);
        }
    }

    function addToWishlist(productId) {
        // TODO: Implement wishlist functionality
        showInfo('Wishlist functionality coming soon!');
    }

    function viewProduct(productId) {
        window.location.href = `detail.php?id=${productId}`;
    }

    function showSuccess(message) {
        showToast(message, 'success');
    }

    function showError(message) {
        showToast(message, 'error');
    }

    function showInfo(message) {
        showToast(message, 'info');
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        
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