<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Lumino E-commerce</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/style.css">
    
    <style>
        .product-gallery {
            position: relative;
        }
        
        .main-image {
            transition: transform 0.5s ease;
        }
        
        .main-image:hover {
            transform: scale(1.05);
        }
        
        .thumbnail-image {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .thumbnail-image:hover,
        .thumbnail-image.active {
            border-color: #f59e0b;
            transform: scale(1.1);
        }
        
        .quantity-input {
            -moz-appearance: textfield;
        }
        
        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .rating-stars {
            color: #fbbf24;
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        .seller-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .related-product-card {
            transition: all 0.3s ease;
        }
        
        .related-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include '../components/navbar.php'; ?>

<!-- Product Details Section -->
<div class="container mx-auto px-4 py-8" id="productContainer">
    <!-- Loading Skeleton -->
    <div class="fade-in" id="loadingSkeleton">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <!-- Image Skeleton -->
            <div class="space-y-4">
                <div class="loading-skeleton rounded-2xl h-96"></div>
                <div class="flex space-x-2">
                    <div class="loading-skeleton rounded-lg w-20 h-20"></div>
                    <div class="loading-skeleton rounded-lg w-20 h-20"></div>
                    <div class="loading-skeleton rounded-lg w-20 h-20"></div>
                    <div class="loading-skeleton rounded-lg w-20 h-20"></div>
                </div>
            </div>
            <!-- Details Skeleton -->
            <div class="space-y-6">
                <div class="loading-skeleton rounded h-8 w-3/4"></div>
                <div class="loading-skeleton rounded h-10 w-1/2"></div>
                <div class="loading-skeleton rounded h-4 w-1/4"></div>
                <div class="loading-skeleton rounded h-20 w-full"></div>
                <div class="loading-skeleton rounded h-12 w-full"></div>
            </div>
        </div>
    </div>

    <!-- Product Content (Initially Hidden) -->
    <div id="productContent" class="hidden">
        <!-- Breadcrumb -->
        <nav class="text-sm breadcrumbs mb-6" id="breadcrumb">
            <div class="flex items-center space-x-2 text-gray-600">
                <a href="../index.php" class="hover:text-amber-600">Home</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="../products.php" class="hover:text-amber-600">Products</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium" id="breadcrumbProduct">Loading...</span>
            </div>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <!-- Product Images -->
            <div class="product-gallery">
                <div class="main-image-container mb-6">
                    <img id="mainImage" src="" alt="" 
                         class="main-image w-full h-96 lg:h-[500px] object-cover rounded-2xl shadow-lg bg-gray-200">
                </div>
                <div class="flex space-x-3 overflow-x-auto pb-2" id="thumbnails">
                    <!-- Thumbnails will be populated here -->
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div>
                    <h1 id="productName" class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3"></h1>
                    <div class="flex items-center justify-between mb-4">
                        <p id="productPrice" class="text-3xl lg:text-4xl font-black text-amber-600"></p>
                        <div class="flex items-center space-x-2">
                            <div class="rating-stars" id="productRating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="text-gray-600 text-sm">(4.8)</span>
                        </div>
                    </div>
                </div>

                <!-- Stock Status -->
                <div id="stockStatus" class="inline-block"></div>

                <!-- Product Description -->
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-gray-900">Description</h3>
                    <p id="productDescription" class="text-gray-700 leading-relaxed"></p>
                </div>

                <!-- Seller Info -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Sold by</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 seller-badge rounded-full flex items-center justify-center text-white font-bold text-lg" id="sellerInitial">
                                S
                            </div>
                            <div>
                                <h4 id="sellerName" class="font-semibold text-gray-900"></h4>
                                <p class="text-gray-600 text-sm">Trusted Seller</p>
                            </div>
                        </div>
                        <button onclick="viewSeller()" class="px-4 py-2 bg-white text-blue-600 rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors">
                            View Store
                        </button>
                    </div>
                </div>

                <!-- Quantity and Add to Cart -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <label class="text-lg font-semibold text-gray-900">Quantity:</label>
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                            <button onclick="changeQuantity(-1)" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantityInput" value="1" min="1" max="10" 
                                   class="quantity-input w-20 py-3 text-center border-0 focus:ring-2 focus:ring-amber-500 outline-none">
                            <button onclick="changeQuantity(1)" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button id="addToCartBtn" onclick="addToCart()" 
                                class="w-full py-4 px-8 bg-gradient-to-r from-amber-600 to-orange-600 text-white font-bold rounded-xl hover:from-amber-700 hover:to-orange-700 transition-all transform hover:-translate-y-1 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                            <i class="fas fa-shopping-cart mr-3"></i> Add to Cart
                        </button>
                        <button onclick="addToWishlist()" 
                                class="w-full py-4 px-8 bg-white text-gray-700 font-semibold border-2 border-gray-300 rounded-xl hover:border-red-400 hover:text-red-500 transition-all">
                            <i class="far fa-heart mr-3"></i> Add to Wishlist
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details Tabs -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <div class="border-b border-gray-200 mb-6">
                <div class="flex space-x-8">
                    <button onclick="switchTab('details')" class="tab-button active pb-4 px-2 border-b-2 border-amber-600 text-amber-600 font-semibold">
                        Product Details
                    </button>
                    <button onclick="switchTab('reviews')" class="tab-button pb-4 px-2 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-semibold">
                        Reviews (0)
                    </button>
                    <button onclick="switchTab('shipping')" class="tab-button pb-4 px-2 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-semibold">
                        Shipping Info
                    </button>
                </div>
            </div>

            <div id="tab-details" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-semibold text-lg mb-3 text-gray-900">Specifications</h4>
                        <div id="productSpecs" class="space-y-2 text-gray-700">
                            <!-- Specs will be populated here -->
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg mb-3 text-gray-900">Features</h4>
                        <div id="productFeatures" class="space-y-2 text-gray-700">
                            <!-- Features will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-reviews" class="tab-content hidden">
                <div class="text-center py-12">
                    <i class="fas fa-star text-6xl text-gray-300 mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-700 mb-2">No reviews yet</h4>
                    <p class="text-gray-500">Be the first to review this product!</p>
                </div>
            </div>

            <div id="tab-shipping" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-semibold text-lg mb-4 text-gray-900">Shipping Options</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-truck text-blue-600"></i>
                                    <span class="font-medium">Standard Shipping</span>
                                </div>
                                <span class="text-green-600 font-semibold">Free</span>
                            </div>
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-bolt text-yellow-600"></i>
                                    <span class="font-medium">Express Shipping</span>
                                </div>
                                <span class="font-semibold">₱99</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg mb-4 text-gray-900">Delivery Time</h4>
                        <div class="space-y-2 text-gray-700">
                            <p><strong>Metro Manila:</strong> 1-2 business days</p>
                            <p><strong>Luzon:</strong> 2-4 business days</p>
                            <p><strong>Visayas/Mindanao:</strong> 3-7 business days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div>
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-8 text-center">You May Also Like</h2>
            <div id="relatedProducts" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Related products will be populated here -->
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<script src="../assets/js/customer-api.js"></script>
<script>
    let productData = null;
    let sellerData = null;

    // Get product ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    if (!productId) {
        window.location.href = '../products.php';
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadProduct();
        updateCartCount();
        updateWishlistCount();
    });

    async function loadProduct() {
        try {
            const response = await customerAPI.products.getById(productId);

            if (response.success) {
                productData = response.data;
                await loadSeller(productData.seller_id);
                renderProduct();
                loadRelatedProducts();
                setTimeout(() => {
                    document.getElementById('loadingSkeleton').style.display = 'none';
                    document.getElementById('productContent').classList.remove('hidden');
                }, 500);
            } else {
                showToast('Product not found', 'error');
                setTimeout(() => {
                    window.location.href = '../products.php';
                }, 2000);
            }
        } catch (error) {
            showToast('Failed to load product', 'error');
            console.error('Error:', error);
        }
    }

    async function loadSeller(sellerId) {
        try {
            const response = await customerAPI.get(`/sellers/${sellerId}`);
            if (response.success) {
                sellerData = response.data;
            }
        } catch (error) {
            console.error('Failed to load seller:', error);
        }
    }

    function renderProduct() {
        if (!productData) return;

        // Update page title
        document.title = `${productData.name} - Lumino E-commerce`;

        // Update breadcrumb
        document.getElementById('breadcrumbProduct').textContent = productData.name;

        // Update product info
        document.getElementById('productName').textContent = productData.name;
        document.getElementById('productPrice').textContent = `₱${productData.price.toFixed(2)}`;
        document.getElementById('productDescription').textContent = productData.description;

        // Update stock status
        const stockElement = document.getElementById('stockStatus');
        if (productData.in_stock && productData.stock_quantity > 0) {
            stockElement.innerHTML = `
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                    ✓ ${productData.stock_quantity} in stock
                </span>
            `;
        } else {
            stockElement.innerHTML = `
                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                    ✗ Out of stock
                </span>
            `;
            document.getElementById('addToCartBtn').disabled = true;
            document.getElementById('quantityInput').disabled = true;
        }

        // Update images
        renderImages();

        // Update seller info
        renderSeller();

        // Update product specs/features
        renderSpecs();

        // Update quantity input max value
        document.getElementById('quantityInput').max = Math.min(productData.stock_quantity || 1, 10);
    }

    function renderImages() {
        const mainImage = document.getElementById('mainImage');
        const thumbnailsContainer = document.getElementById('thumbnails');

        if (productData.images && productData.images.length > 0) {
            // Set main image - handle both string and object formats
            const firstImageUrl = typeof productData.images[0] === 'string' 
                ? productData.images[0] 
                : productData.images[0].image_url;
            const firstImage = getImageUrl(firstImageUrl);
            mainImage.src = firstImage;
            mainImage.alt = productData.name;

            // Generate thumbnails
            thumbnailsContainer.innerHTML = productData.images.map((image, index) => {
                const imageUrl = typeof image === 'string' ? image : image.image_url;
                const altText = typeof image === 'string' ? productData.name : (image.alt_text || productData.name);
                return `
                    <img src="${getImageUrl(imageUrl)}" alt="${altText}"
                         class="thumbnail-image w-20 h-20 object-cover rounded-lg ${index === 0 ? 'active' : ''}"
                         onclick="changeMainImage('${getImageUrl(imageUrl)}', ${index})">
                `;
            }).join('');
        } else {
            mainImage.src = '../images/no-image.png';
            mainImage.alt = 'No image available';
        }
    }

    function renderSeller() {
        if (!sellerData) return;

        document.getElementById('sellerName').textContent = sellerData.store_name || 'Unknown Seller';
        
        // Set seller initial
        const initial = (sellerData.store_name || 'S').charAt(0).toUpperCase();
        document.getElementById('sellerInitial').textContent = initial;
    }

    function renderSpecs() {
        // Sample specs - you can customize this based on product category
        const specs = [
            'Brand: Premium Quality',
            'Material: High Grade',
            'Warranty: 1 Year',
            'Origin: Philippines'
        ];

        const features = [
            'High quality materials',
            'Durable construction',
            'Easy to use',
            'Great value for money'
        ];

        document.getElementById('productSpecs').innerHTML = specs.map(spec => 
            `<div class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> ${spec}</div>`
        ).join('');

        document.getElementById('productFeatures').innerHTML = features.map(feature => 
            `<div class="flex items-center"><i class="fas fa-star text-yellow-500 mr-2"></i> ${feature}</div>`
        ).join('');
    }

    async function loadRelatedProducts() {
        try {
            const response = await customerAPI.products.getAll({ 
                category: productData.category_slug, 
                limit: 4,
                exclude: productId
            });

            if (response.success && response.data.length > 0) {
                renderRelatedProducts(response.data);
            }
        } catch (error) {
            console.error('Failed to load related products:', error);
        }
    }

    function renderRelatedProducts(products) {
        const container = document.getElementById('relatedProducts');
        
        container.innerHTML = products.map(product => {
            // Handle both string and object image formats
            const firstImage = product.images && product.images.length > 0 
                ? (typeof product.images[0] === 'string' ? product.images[0] : product.images[0].image_url || product.images[0])
                : null;
            
            return `
                <div class="related-product-card bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="relative h-48">
                        <img src="${getImageUrl(firstImage)}" alt="${product.name}" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2">${product.name}</h4>
                        <p class="text-amber-600 font-bold text-lg mb-2">₱${product.price.toFixed(2)}</p>
                        <button onclick="viewProduct(${product.id})"
                                class="w-full py-2 px-4 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                            View Details
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function getImageUrl(image) {
        if (!image) return '../images/no-image.png';
        if (image.startsWith('http') || image.startsWith('/')) {
            return image;
        }
        return `/Core1_ecommerce/uploads/${image}`;
    }

    function changeMainImage(imageUrl, index) {
        document.getElementById('mainImage').src = imageUrl;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail-image').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    }

    function changeQuantity(change) {
        const input = document.getElementById('quantityInput');
        const currentValue = parseInt(input.value);
        const newValue = Math.max(1, Math.min(parseInt(input.max), currentValue + change));
        input.value = newValue;
    }

    async function addToCart() {
        if (!productData) return;

        const quantity = parseInt(document.getElementById('quantityInput').value);
        const button = document.getElementById('addToCartBtn');
        
        // Disable button and show loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i> Adding...';

        try {
            const response = await customerAPI.cart.addItem(productData.id, quantity);

            if (response.success) {
                showToast(`${productData.name} added to cart!`, 'success');
                updateCartCount();
            } else {
                showToast(response.message, 'error');
            }
        } catch (error) {
            showToast('Failed to add item to cart', 'error');
            console.error('Error:', error);
        } finally {
            // Reset button
            button.disabled = productData.in_stock ? false : true;
            button.innerHTML = '<i class="fas fa-shopping-cart mr-3"></i> Add to Cart';
        }
    }

    async function addToWishlist() {
        if (!productData) return;

        const button = event.target;
        const originalContent = button.innerHTML;
        
        // Check if user is logged in
        try {
            const profileResponse = await customerAPI.auth.getProfile();
            
            if (!profileResponse.success) {
                showToast('Please log in to add items to your wishlist', 'warning');
                setTimeout(() => {
                    window.location.href = '../login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
                }, 1500);
                return;
            }
        } catch (error) {
            showToast('Please log in to add items to your wishlist', 'warning');
            setTimeout(() => {
                window.location.href = '../login.php?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            }, 1500);
            return;
        }

        // Disable button and show loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i> Adding...';

        try {
            let response;
            
            // Try main API first, fallback to direct fetch if needed
            if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
                response = await customerAPI.wishlist.addItem(productData.id);
            } else {
                // Direct API call fallback
                const apiResponse = await fetch('/Core1_ecommerce/customer/api/wishlist.php/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ product_id: productData.id })
                });
                response = await apiResponse.json();
            }

            if (response.success) {
                showToast(response.message, 'success');
                // Update button to show added state
                button.innerHTML = '<i class="fas fa-heart mr-3"></i> Added to Wishlist';
                button.classList.remove('hover:border-red-400', 'hover:text-red-500');
                button.classList.add('border-red-400', 'text-red-500');
                
                // Update navbar wishlist count
                updateWishlistCount();
            } else {
                showToast(response.message, 'error');
            }
        } catch (error) {
            showToast('Failed to add item to wishlist', 'error');
            console.error('Error:', error);
        } finally {
            // Reset button if failed
            if (button.innerHTML.includes('Adding...')) {
                button.innerHTML = originalContent;
            }
            button.disabled = false;
        }
    }

    function viewSeller() {
        if (sellerData) {
            window.location.href = `../sellers/store.php?id=${sellerData.id}`;
        }
    }

    function viewProduct(id) {
        window.location.href = `detail.php?id=${id}`;
    }

    function switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'border-amber-600', 'text-amber-600');
            btn.classList.add('border-transparent', 'text-gray-600');
        });
        
        event.target.classList.remove('border-transparent', 'text-gray-600');
        event.target.classList.add('active', 'border-amber-600', 'text-amber-600');

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
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