<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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
        
        .search-section {
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .search-container {
            position: relative;
            background: #fff;
            border-radius: 5rem;
            padding: .5rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1);
            border: .1rem solid rgba(0, 0, 0, .1);
            transition: all .2s linear;
        }
        
        .search-container:hover {
            box-shadow: 0 .8rem 1.5rem rgba(0, 0, 0, .15);
            transform: translateY(-.2rem);
        }
        
        .search-inner {
            position: relative;
            width: 100%;
        }
        
        .search-input {
            width: 100%;
            padding: 1.5rem 2rem;
            padding-right: 7rem;
            font-size: 1.6rem;
            color: #333;
            background: transparent;
            border: none;
            outline: none;
            border-radius: 5rem;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }
        
        .search-input::placeholder {
            color: #999;
            text-transform: none;
        }
        
        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: var(--beige);
            color: white;
            border: none;
            border-radius: 50%;
            width: 4.5rem;
            height: 4.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s linear;
            font-size: 1.8rem;
        }
        
        .search-icon:hover {
            background: #333;
            transform: translateY(-50%) scale(1.05);
        }
        
        .filter-section {
            background: #fff;
            border-radius: .5rem;
            padding: 2rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1);
            border: .1rem solid rgba(0, 0, 0, .1);
            margin-top: 2rem;
        }
        
        .filter-input, .filter-select {
            padding: 1.2rem 1.5rem;
            font-size: 1.5rem;
            color: #333;
            background: #fff;
            border: .1rem solid rgba(0, 0, 0, .1);
            border-radius: .5rem;
            outline: none;
            transition: all .2s linear;
            width: 100%;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }
        
        .filter-input:focus, .filter-select:focus {
            border-color: var(--beige);
            box-shadow: 0 0 0 .2rem rgba(180, 141, 107, .1);
        }
        
        .search-button {
            background: #333;
            color: #fff;
            padding: 1.2rem 3rem;
            border-radius: 5rem;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            transition: all .2s linear;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }
        
        .search-button:hover {
            background: var(--beige);
            transform: translateY(-.2rem);
        }
        
        .filter-label {
            display: block;
            font-size: 1.4rem;
            color: #333;
            margin-bottom: .8rem;
            font-weight: bold;
        }
        
        .filter-label i {
            color: var(--beige);
            margin-right: .5rem;
        }
        
        .search-title {
            text-align: center;
            font-size: 3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .search-title span {
            color: var(--beige);
        }
        
        .search-subtitle {
            text-align: center;
            font-size: 1.4rem;
            color: #999;
            margin-bottom: 2rem;
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

    <!-- Search Section -->
    <section class="search-section">
        <div class="search-title">find your perfect <span>product</span></div>
        <div class="search-subtitle">discover amazing products from trusted sellers</div>
        
        <!-- Main Search Bar -->
        <div class="search-container" style="max-width: 60rem; margin: 0 auto 2rem auto;">
            <div class="search-inner">
                <input type="text" id="searchInput" placeholder="search for amazing products, brands, categories..." 
                       class="search-input" />
                <button class="search-icon" onclick="loadProducts(1)">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-section">
            <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: end; justify-content: center;">
                <!-- Category Filter -->
                <div style="min-width: 20rem; flex: 1;">
                    <label for="categoryFilter" class="filter-label">
                        <i class="fas fa-tags"></i>
                        category
                    </label>
                    <select id="categoryFilter" class="filter-select">
                        <option value="">all categories</option>
                    </select>
                </div>
    
                <!-- Price Range -->
                <div style="min-width: 25rem; flex: 1;">
                    <label class="filter-label">
                        <i class="fas fa-dollar-sign"></i>
                        price range
                    </label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="number" id="minPrice" placeholder="min" min="0" step="0.01"
                               class="filter-input" style="width: 8rem; text-align: center;" />
                        <span style="color: #999; font-size: 1.4rem;">to</span>
                        <input type="number" id="maxPrice" placeholder="max" min="0" step="0.01"
                               class="filter-input" style="width: 8rem; text-align: center;" />
                    </div>
                </div>
    
                <!-- Featured Filter -->
                <div style="min-width: 15rem; flex: 1;">
                    <label for="featuredFilter" class="filter-label">
                        <i class="fas fa-star"></i>
                        featured
                    </label>
                    <select id="featuredFilter" class="filter-select">
                        <option value="">all products</option>
                        <option value="1">featured only</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div style="min-width: 18rem; flex: 1;">
                    <label for="sortBy" class="filter-label">
                        <i class="fas fa-sort"></i>
                        sort by
                    </label>
                    <select id="sortBy" class="filter-select">
                        <option value="created_at">newest first</option>
                        <option value="name">name a-z</option>
                        <option value="price">price low-high</option>
                        <option value="price_desc">price high-low</option>
                    </select>
                </div>
    
                <!-- Search Button -->
                <div style="display: flex; align-items: end;">
                    <button onclick="loadProducts(1)" class="search-button">
                        <i class="fas fa-search" style="margin-right: .8rem;"></i>
                        search
                    </button>
                </div>
            </div>
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

<?php include 'components/footer.php'; ?>

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
        updateWishlistCount();

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
            console.log('Loading categories...');
            const response = await customerAPI.products.getCategories();
            console.log('Categories response:', response);
            
            if (response.success) {
                const select = document.getElementById('categoryFilter');
                console.log('Category select element:', select);
                console.log('Adding categories to dropdown...');
                
                response.data.forEach(category => {
                    console.log('Adding category:', category.name);
                    const option = document.createElement('option');
                    option.value = category.slug;
                    option.textContent = `${category.name} (${category.product_count})`;
                    select.appendChild(option);
                });
                
                console.log('Total options in dropdown:', select.options.length);
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
            featured: document.getElementById('featuredFilter').value,
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
                    <div class="absolute top-4 right-4 flex flex-col gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0 z-10">
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
                    ${product.featured ? `<div class="absolute top-16 right-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-sm font-bold flex items-center"><i class="fas fa-star mr-1"></i>Featured</div>` : ''}
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
            // Handle both string and object image formats
            const imageUrl = typeof images[0] === 'string' ? images[0] : images[0].image_url || images[0];
            if (!imageUrl) return '../images/no-image.png';
            
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

    async function addToWishlist(productId) {
        // Check if user is logged in
        try {
            const profileResponse = await customerAPI.auth.getProfile();
            
            if (!profileResponse.success) {
                showToast('Please log in to add items to your wishlist', 'warning');
                setTimeout(() => {
                    window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
                }, 1500);
                return;
            }
        } catch (error) {
            showToast('Please log in to add items to your wishlist', 'warning');
            setTimeout(() => {
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            }, 1500);
            return;
        }

        try {
            let response;
            
            // Try main API first, fallback to direct fetch if needed
            if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                response = await customerAPI.wishlist.addItem(productId);
            } else {
                // Direct API call fallback
                const apiResponse = await fetch('/Core1_ecommerce/customer/api/wishlist.php/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ product_id: productId })
                });
                response = await apiResponse.json();
            }

            if (response.success) {
                showToast(response.message, 'success');
                
                // Update wishlist button appearance
                const wishlistBtn = document.querySelector(`button[onclick="addToWishlist(${productId})"]`);
                if (wishlistBtn) {
                    wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
                    wishlistBtn.classList.remove('text-gray-400', 'hover:text-red-500');
                    wishlistBtn.classList.add('text-red-500');
                    wishlistBtn.setAttribute('title', 'Added to Wishlist');
                    wishlistBtn.onclick = null; // Disable further clicks
                }
                
                // Update navbar wishlist count
                updateWishlistCount();
            } else {
                showToast(response.message, 'error');
            }
        } catch (error) {
            showToast('Failed to add item to wishlist', 'error');
            console.error('Error:', error);
        }
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

    async function updateWishlistCount() {
        try {
            let response;
            
            // Try main API first, fallback to direct fetch if needed
            if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                response = await customerAPI.wishlist.getCount();
            } else {
                // Direct API call fallback
                const apiResponse = await fetch('/Core1_ecommerce/customer/api/wishlist.php/count', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                response = await apiResponse.json();
            }
            
            if (response.success) {
                const wishlistCount = document.getElementById('wishlistCount');
                if (wishlistCount) {
                    wishlistCount.textContent = response.data.count;
                }
            }
        } catch (error) {
            // Silently fail - wishlist functionality is optional and user may not be logged in
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