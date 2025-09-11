<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Store - Lumino E-commerce</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/style.css">
    
    <style>
        .store-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .store-banner {
            position: relative;
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            overflow: hidden;
        }
        
        .store-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="0,0 1000,100 1000,0"/></svg>');
            background-size: cover;
        }
        
        .store-logo {
            border: 4px solid white;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }
        
        .product-card {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: #f59e0b;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
        }
        
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .category-filter {
            background: white;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .category-filter.active,
        .category-filter:hover {
            border-color: #f59e0b;
            background: #fef3c7;
            color: #92400e;
        }
        
        .contact-seller-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .contact-seller-btn:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include '../components/navbar.php'; ?>

<!-- Loading Skeleton -->
<div id="loadingSkeleton" class="fade-in">
    <!-- Store Banner Skeleton -->
    <div class="loading-skeleton h-64 w-full"></div>
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between -mt-20 relative z-10 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-6">
                <div class="loading-skeleton w-32 h-32 rounded-full mb-4 lg:mb-0"></div>
                <div class="text-center lg:text-left space-y-3">
                    <div class="loading-skeleton h-8 w-48 rounded"></div>
                    <div class="loading-skeleton h-4 w-32 rounded"></div>
                </div>
            </div>
        </div>
        
        <!-- Stats Skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="loading-skeleton h-24 rounded-xl"></div>
            <div class="loading-skeleton h-24 rounded-xl"></div>
            <div class="loading-skeleton h-24 rounded-xl"></div>
            <div class="loading-skeleton h-24 rounded-xl"></div>
        </div>
        
        <!-- Products Grid Skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="loading-skeleton h-80 rounded-xl"></div>
            <div class="loading-skeleton h-80 rounded-xl"></div>
            <div class="loading-skeleton h-80 rounded-xl"></div>
            <div class="loading-skeleton h-80 rounded-xl"></div>
        </div>
    </div>
</div>

<!-- Store Content (Initially Hidden) -->
<div id="storeContent" class="hidden">
    <!-- Breadcrumb -->
    <nav class="bg-white border-b">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="../index.php" class="hover:text-amber-600">Home</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="../products.php" class="hover:text-amber-600">Sellers</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium" id="breadcrumbStore">Loading...</span>
            </div>
        </div>
    </nav>

    <!-- Store Banner -->
    <div class="store-banner relative h-64" id="storeBanner">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/30 to-transparent"></div>
    </div>

    <!-- Store Info -->
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between -mt-20 relative z-10 mb-8">
            <!-- Store Logo and Basic Info -->
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-6">
                <div class="store-logo w-32 h-32 bg-white rounded-full flex items-center justify-center text-4xl font-bold text-blue-600 mb-4 lg:mb-0 mx-auto lg:mx-0" id="storeLogo">
                    S
                </div>
                <div class="text-center lg:text-left text-white">
                    <h1 id="storeName" class="text-3xl lg:text-4xl font-bold mb-2"></h1>
                    <p id="storeDescription" class="text-blue-100 text-lg max-w-2xl"></p>
                    <div class="flex items-center justify-center lg:justify-start space-x-4 mt-3 text-sm">
                        <span class="flex items-center">
                            <i class="fas fa-store mr-2"></i>
                            <span id="businessType">Business</span>
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-2"></i>
                            Member since <span id="memberSince"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 mt-6 lg:mt-0">
                <button onclick="contactSeller()" 
                        class="contact-seller-btn px-6 py-3 text-white font-semibold rounded-xl transition-all">
                    <i class="fas fa-comment mr-2"></i> Contact Seller
                </button>
                <button onclick="followStore()" 
                        class="px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-300 hover:border-blue-500 hover:text-blue-600 transition-all">
                    <i class="far fa-heart mr-2"></i> Follow Store
                </button>
            </div>
        </div>

        <!-- Store Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <div class="stats-card text-center p-6 rounded-xl">
                <div class="text-3xl font-bold text-blue-600 mb-2" id="totalProducts">0</div>
                <div class="text-gray-600 font-medium">Products</div>
            </div>
            <div class="stats-card text-center p-6 rounded-xl">
                <div class="text-3xl font-bold text-green-600 mb-2">4.8</div>
                <div class="text-gray-600 font-medium">Rating</div>
            </div>
            <div class="stats-card text-center p-6 rounded-xl">
                <div class="text-3xl font-bold text-purple-600 mb-2" id="totalSales">1.2k</div>
                <div class="text-gray-600 font-medium">Sales</div>
            </div>
            <div class="stats-card text-center p-6 rounded-xl">
                <div class="text-3xl font-bold text-orange-600 mb-2">99%</div>
                <div class="text-gray-600 font-medium">Response Rate</div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4 lg:mb-0">Store Products</h2>
                
                <!-- Filters -->
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center space-x-2">
                        <input type="text" id="searchProducts" placeholder="Search products..." 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                    </div>
                    <select id="sortProducts" 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white">
                        <option value="created_at">Newest First</option>
                        <option value="name">Name A-Z</option>
                        <option value="price">Price Low-High</option>
                        <option value="price_desc">Price High-Low</option>
                    </select>
                </div>
            </div>

            <!-- Category Filters -->
            <div class="flex flex-wrap gap-3 mb-8" id="categoryFilters">
                <button onclick="filterByCategory('')" class="category-filter active px-4 py-2 rounded-full font-medium">
                    All Products
                </button>
                <!-- Categories will be populated here -->
            </div>

            <!-- Products Count -->
            <div class="text-gray-600 font-medium mb-6" id="productsCount">
                Loading products...
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Products will be populated here -->
            </div>

            <!-- Load More Button -->
            <div class="text-center" id="loadMoreContainer" style="display: none;">
                <button onclick="loadMoreProducts()" id="loadMoreBtn"
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Load More Products
                </button>
            </div>

            <!-- No Products Message -->
            <div id="noProductsMessage" class="text-center py-16 hidden">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                <p class="text-gray-500">This store hasn't added any products yet or no products match your filters.</p>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<script src="../assets/js/customer-api.js"></script>
<script>
    let storeData = null;
    let productsData = [];
    let currentPage = 1;
    let totalPages = 1;
    let currentFilters = { category: '', search: '', sort: 'created_at' };

    // Get seller ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const sellerId = urlParams.get('id');

    if (!sellerId) {
        window.location.href = '../products.php';
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadStore();
        updateCartCount();

        // Event listeners
        document.getElementById('searchProducts').addEventListener('input', debounce(function() {
            currentFilters.search = this.value;
            currentPage = 1;
            loadProducts();
        }, 500));

        document.getElementById('sortProducts').addEventListener('change', function() {
            currentFilters.sort = this.value;
            currentPage = 1;
            loadProducts();
        });
    });

    async function loadStore() {
        try {
            const response = await customerAPI.get(`/sellers/${sellerId}`);

            if (response.success) {
                storeData = response.data;
                renderStore();
                loadProducts();
                loadCategories();
                
                setTimeout(() => {
                    document.getElementById('loadingSkeleton').style.display = 'none';
                    document.getElementById('storeContent').classList.remove('hidden');
                }, 500);
            } else {
                showToast('Store not found', 'error');
                setTimeout(() => {
                    window.location.href = '../products.php';
                }, 2000);
            }
        } catch (error) {
            showToast('Failed to load store', 'error');
            console.error('Error:', error);
        }
    }

    function renderStore() {
        if (!storeData) return;

        // Update page title
        document.title = `${storeData.store_name} - Lumino E-commerce`;

        // Update breadcrumb
        document.getElementById('breadcrumbStore').textContent = storeData.store_name;

        // Update store info
        document.getElementById('storeName').textContent = storeData.store_name;
        document.getElementById('storeDescription').textContent = storeData.store_description || 'Welcome to our store!';
        document.getElementById('businessType').textContent = storeData.business_type === 'business' ? 'Business' : 'Individual Seller';
        
        // Set store logo initial
        const initial = storeData.store_name.charAt(0).toUpperCase();
        document.getElementById('storeLogo').textContent = initial;

        // Format member since date
        if (storeData.created_at) {
            const date = new Date(storeData.created_at);
            document.getElementById('memberSince').textContent = date.getFullYear();
        }

        // Set banner background if available
        const banner = document.getElementById('storeBanner');
        if (storeData.store_banner) {
            banner.style.backgroundImage = `url('${getImageUrl(storeData.store_banner)}')`;
            banner.style.backgroundSize = 'cover';
            banner.style.backgroundPosition = 'center';
        }

        // Update store logo if available
        const logo = document.getElementById('storeLogo');
        if (storeData.store_logo) {
            logo.innerHTML = `<img src="${getImageUrl(storeData.store_logo)}" alt="${storeData.store_name}" class="w-full h-full object-cover rounded-full">`;
        }
    }

    async function loadProducts() {
        try {
            const params = {
                seller_id: sellerId,
                page: currentPage,
                limit: 12,
                search: currentFilters.search,
                category: currentFilters.category,
                sort_by: currentFilters.sort.replace('_desc', ''),
                sort_order: currentFilters.sort.includes('_desc') ? 'DESC' : 'ASC'
            };

            const response = await customerAPI.products.getAll(params);

            if (response.success) {
                if (currentPage === 1) {
                    productsData = response.data;
                } else {
                    productsData = [...productsData, ...response.data];
                }
                
                renderProducts();
                updateProductsCount(response.pagination);
                updateLoadMoreButton(response.pagination);
                
                // Update store stats
                if (currentPage === 1) {
                    document.getElementById('totalProducts').textContent = response.pagination.total;
                }
            }
        } catch (error) {
            showToast('Failed to load products', 'error');
            console.error('Error:', error);
        }
    }

    async function loadCategories() {
        try {
            const response = await customerAPI.get(`/sellers/categories?seller_id=${sellerId}`);
            
            if (response.success && response.data.length > 0) {
                renderCategoryFilters(response.data);
            }
        } catch (error) {
            console.error('Failed to load categories:', error);
        }
    }

    function renderCategoryFilters(categories) {
        const container = document.getElementById('categoryFilters');
        const allButton = container.querySelector('button'); // Keep the "All Products" button
        
        categories.forEach(category => {
            const button = document.createElement('button');
            button.onclick = () => filterByCategory(category.slug);
            button.className = 'category-filter px-4 py-2 rounded-full font-medium';
            button.textContent = `${category.name} (${category.product_count})`;
            container.appendChild(button);
        });
    }

    function renderProducts() {
        const container = document.getElementById('productsGrid');
        const noProductsMessage = document.getElementById('noProductsMessage');

        if (productsData.length === 0) {
            container.innerHTML = '';
            noProductsMessage.classList.remove('hidden');
            return;
        }

        noProductsMessage.classList.add('hidden');

        const productsHTML = productsData.map(product => `
            <div class="product-card bg-white rounded-xl shadow-lg overflow-hidden group fade-in">
                <div class="relative h-48 overflow-hidden">
                    <img src="${getImageUrl(product.images && product.images.length > 0 ? (typeof product.images[0] === 'string' ? product.images[0] : product.images[0].image_url || product.images[0]) : null)}" alt="${product.name}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <button onclick="addToWishlist(${product.id})" title="Add to Wishlist"
                                class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-700 hover:text-red-500 shadow-lg hover:scale-110 transition-all">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button onclick="viewProduct(${product.id})" title="View Details"
                                class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-gray-700 hover:text-blue-500 shadow-lg hover:scale-110 transition-all">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    ${product.category_name ? `<div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-gray-700">${product.category_name}</div>` : ''}
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">${product.name}</h3>
                    <p class="text-blue-600 font-black text-xl mb-3">₱${product.price.toFixed(2)}</p>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">${product.description}</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium ${
                            product.in_stock 
                                ? 'text-green-600 bg-green-50 px-2 py-1 rounded-full' 
                                : 'text-red-600 bg-red-50 px-2 py-1 rounded-full'
                        }">
                            ${product.in_stock ? `✓ In stock` : '✗ Out of stock'}
                        </span>
                        <div class="flex items-center text-yellow-400 text-sm">
                            <i class="fas fa-star"></i>
                            <span class="text-gray-500 ml-1">(4.8)</span>
                        </div>
                    </div>
                    <button onclick="addToCart(${product.id})"
                            ${!product.in_stock ? 'disabled' : ''}
                            class="w-full py-3 px-4 rounded-xl font-semibold transition-all ${
                                product.in_stock 
                                    ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 transform hover:-translate-y-1 hover:shadow-lg' 
                                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            }">
                        <i class="fas fa-shopping-cart mr-2"></i> 
                        ${product.in_stock ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                </div>
            </div>
        `).join('');

        container.innerHTML = productsHTML;
    }

    function updateProductsCount(pagination) {
        const count = document.getElementById('productsCount');
        if (pagination.total === 0) {
            count.textContent = 'No products found';
        } else if (pagination.total === 1) {
            count.textContent = '1 product';
        } else {
            const showing = Math.min(currentPage * pagination.per_page, pagination.total);
            count.textContent = `Showing ${showing} of ${pagination.total} products`;
        }
    }

    function updateLoadMoreButton(pagination) {
        const container = document.getElementById('loadMoreContainer');
        const button = document.getElementById('loadMoreBtn');
        
        totalPages = pagination.total_pages;
        
        if (currentPage < totalPages) {
            container.style.display = 'block';
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-plus mr-2"></i> Load More Products';
        } else {
            container.style.display = 'none';
        }
    }

    function loadMoreProducts() {
        currentPage++;
        loadProducts();
        
        // Update button state
        const button = document.getElementById('loadMoreBtn');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';
    }

    function filterByCategory(categorySlug) {
        // Update active button
        document.querySelectorAll('.category-filter').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        // Update filter and reload products
        currentFilters.category = categorySlug;
        currentPage = 1;
        loadProducts();
    }

    function getImageUrl(image) {
        if (!image) return '../images/no-image.png';
        if (image.startsWith('http') || image.startsWith('/')) {
            return image;
        }
        return `/Core1_ecommerce/uploads/${image}`;
    }

    async function addToCart(productId) {
        try {
            const response = await customerAPI.cart.addItem(productId, 1);

            if (response.success) {
                const product = productsData.find(p => p.id === productId);
                showToast(`${product?.name || 'Product'} added to cart!`, 'success');
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
        showToast('Wishlist functionality coming soon!', 'info');
    }

    function viewProduct(productId) {
        window.location.href = `../products/detail.php?id=${productId}`;
    }

    function contactSeller() {
        if (storeData) {
            showToast('Chat functionality coming soon!', 'info');
            // TODO: Open chat with seller
        }
    }

    function followStore() {
        showToast('Follow functionality coming soon!', 'info');
        // TODO: Implement follow store functionality
    }

    async function updateCartCount() {
        try {
            const response = await customerAPI.cart.getCount();
            if (response.success) {
                const cartCount = document.getElementById('cartCount') || document.querySelector('[data-cart-count]');
                if (cartCount) {
                    cartCount.textContent = response.data.total_quantity;
                }
            }
        } catch (error) {
            console.error('Failed to update cart count:', error);
        }
    }

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

    function showToast(message, type = 'success') {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0';
            document.body.appendChild(toast);
        }
        
        toast.className = 'fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0';
        
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
        
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 10);

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