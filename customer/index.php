<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumino E-commerce - Home</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<script src="assets/js/customer-api.js"></script>

<?php include 'components/navbar.php'; ?>

<!-- home section starts -->

<section class="home" id="home">

    <div class="content">
        <h3>Lumino</h3>
        <span> Shop Online</span>
        <p>Discover amazing products at unbeatable prices. From electronics to fashion, home goods to sports equipment - find everything you need in one convenient place with fast shipping and secure payments.</p>
        <a href="products.php" class="btn">shop now</a>
    </div>


</section>

<!-- home section ends -->

<!-- about section starts -->

<section class="about" id="about">

    <h1 class="heading"> <span> about </span> us </h1>

    <div class="row">

        <div class="video-container">
            <video src="images/about-vid.mp4" loop autoplay muted></video>
            <h3>Best online shopping experience</h3>
        </div>

        <div class="content">
            <h3>why choose us?</h3>
            <p>We offer a vast selection of quality products from trusted brands at competitive prices. Our user-friendly platform makes shopping easy and secure with multiple payment options and reliable customer support.</p>
            <p>With fast shipping, hassle-free returns, and 24/7 customer service, we're committed to providing you with the best online shopping experience. Join millions of satisfied customers who trust us for their shopping needs.</p>
            <a href="#" class="btn">Learn More</a>
        </div>

    </div>

</section>

<!-- about section ends -->

<!-- featured products section starts -->
<section class="featured-products" id="featured-products">
    <h1 class="heading">Featured <span>Products</span></h1>
    
    <div class="text-center mb-8">
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">Discover our handpicked selection of premium products, carefully chosen for their quality, popularity, and customer satisfaction.</p>
    </div>

    <div id="featuredProductsContainer" class="flex justify-center items-center py-20 text-gray-500">
        <div class="text-center">
            <div class="loading-spinner w-12 h-12 border-4 border-amber-200 border-t-amber-600 rounded-full mx-auto mb-4"></div>
            <p class="text-lg font-medium">Loading featured products...</p>
        </div>
    </div>

    <div class="text-center mt-8">
        <a href="products.php" class="btn">View All Products</a>
    </div>
</section>

<style>
.featured-products {
    padding: 4rem 0;
    background: #f8f9fa;
}

.featured-products .heading {
    text-align: center;
    font-size: 3rem;
    color: #333;
    margin-bottom: 2rem;
}

.featured-products .heading span {
    color: var(--beige);
}

.featured-product-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin: 1rem;
}

.featured-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.featured-product-image {
    height: 250px;
    overflow: hidden;
    position: relative;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.featured-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.featured-product-card:hover .featured-product-image img {
    transform: scale(1.1);
}

.featured-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.featured-product-content {
    padding: 1.5rem;
}

.featured-product-title {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.featured-product-price {
    font-size: 1.5rem;
    font-weight: black;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.featured-product-description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.featured-product-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.featured-add-cart-btn {
    flex: 1;
    background: var(--beige);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.featured-add-cart-btn:hover {
    background: #333;
    transform: translateY(-2px);
}

.featured-add-cart-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.featured-wishlist-btn, .featured-view-btn {
    width: 3rem;
    height: 3rem;
    border: 2px solid var(--beige);
    background: white;
    color: var(--beige);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.featured-wishlist-btn:hover, .featured-view-btn:hover {
    background: var(--beige);
    color: white;
}

.featured-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
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

@media (max-width: 768px) {
    .featured-products-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        padding: 0 1rem;
    }
    
    .featured-products .heading {
        font-size: 2rem;
    }
}
</style>

<!-- featured products section ends -->

<!-- icon section starts -->

<section class="icons-container">

    <div class="icons">
        <img src="images/icon-1.png" alt="">
        <div class="info">
            <h3>Free Delivery</h3>
            <span>on all orders</span>
        </div>
    </div>

    <div class="icons">
        <img src="images/icon-2.png" alt="">
        <div class="info">
            <h3>10 days returns</h3>
            <span>moneyback guarantee</span>
        </div>
    </div>

    <div class="icons">
        <img src="images/icon-3.png" alt="">
        <div class="info">
            <h3>Offer & Gifts</h3>
            <span>on all orders</span>
        </div>
    </div>

    <div class="icons">
        <img src="images/icon-4.png" alt="">
        <div class="info">
            <h3>Secured Payment</h3>
            <span>Protected by Paypal</span>
        </div>
    </div>

</section>

<!-- icon section ends -->


<!-- review section starts -->

<section class="review" id="review">

<h1 class="heading"> Customer's <span>review</span> </h1>

<div class="box-container">

    <div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-1.png" alt="">
            <div class="user-info">
            <h3>Vinny Hong</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>

<div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-2.png" alt="">
            <div class="user-info">
            <h3>Sanzu Haruchiyo</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>

<div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-3.png" alt="">
            <div class="user-info">
            <h3>Leon Winston</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>

<div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-4.png" alt="">
            <div class="user-info">
            <h3>Eiser Grayon</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>


</section>

<!-- review section ends -->

<!-- contact section starts -->

<section class="contact" id="contact">

    <h1 class="heading"> <span> Contact </span> Us </h1>

    <div class="row">

        <form action="">
            <input type="text" placeholder="name" class="box">
            <input type="email" placeholder="email" class="box">
            <input type="number" placeholder="number" class="box">
            <textarea name="" class="box" placeholder="message" id="" cols="30" rows="10"></textarea>
            <input type="submit" value="send message" class="btn">
        </form>

        <div class="image">
            <img src="images/contact-img.png" alt="">
        </div>

    </div>
</section>

<!-- contact section ends -->

<?php include 'components/footer.php'; ?>

<script>
// Load featured products when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadFeaturedProducts();
});

async function loadFeaturedProducts() {
    try {
        const response = await customerAPI.products.getFeatured(8);
        
        if (response.success) {
            renderFeaturedProducts(response.data);
        } else {
            showFeaturedError('Failed to load featured products');
        }
    } catch (error) {
        console.error('Error loading featured products:', error);
        showFeaturedError('Error loading featured products');
    }
}

function renderFeaturedProducts(products) {
    const container = document.getElementById('featuredProductsContainer');
    
    if (products.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No featured products available</h3>
                <p class="text-gray-500">Check back later for our latest featured items</p>
            </div>
        `;
        return;
    }

    const productsHTML = products.map(product => `
        <div class="featured-product-card fade-in">
            <div class="featured-product-image">
                <img src="${getFeaturedProductImage(product.images)}" alt="${product.name}">
                <div class="featured-badge">Featured</div>
            </div>
            <div class="featured-product-content">
                <h3 class="featured-product-title">${product.name}</h3>
                <div class="featured-product-price">₱${product.price.toFixed(2)}</div>
                <p class="featured-product-description">${product.description}</p>
                <div class="featured-product-actions">
                    <button onclick="addToFeaturedCart(${product.id}, '${product.name}', ${product.price})"
                            ${!product.in_stock ? 'disabled' : ''}
                            class="featured-add-cart-btn">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        ${product.in_stock ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                    <button onclick="addToFeaturedWishlist(${product.id})" 
                            class="featured-wishlist-btn" 
                            title="Add to Wishlist">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button onclick="viewFeaturedProduct(${product.id})" 
                            class="featured-view-btn"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    container.innerHTML = `<div class="featured-products-grid">${productsHTML}</div>`;
}

function showFeaturedError(message) {
    const container = document.getElementById('featuredProductsContainer');
    container.innerHTML = `
        <div class="text-center py-16">
            <i class="fas fa-exclamation-triangle text-6xl text-red-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-red-700 mb-2">${message}</h3>
            <button onclick="loadFeaturedProducts()" class="btn mt-4">Try Again</button>
        </div>
    `;
}

function getFeaturedProductImage(images) {
    if (images && images.length > 0) {
        const imageUrl = typeof images[0] === 'string' ? images[0] : images[0].image_url || images[0];
        if (!imageUrl) return '../images/no-image.png';
        
        if (imageUrl.startsWith('http') || imageUrl.startsWith('/')) {
            return imageUrl;
        }
        return `/Core1_ecommerce/uploads/${imageUrl}`;
    }
    return '../images/no-image.png';
}

async function addToFeaturedCart(productId, productName, price) {
    try {
        const response = await customerAPI.cart.addItem(productId, 1);

        if (response.success) {
            showFeaturedToast(`${productName} added to cart!`, 'success');
            updateCartCount();
        } else {
            showFeaturedToast(response.message, 'error');
        }
    } catch (error) {
        showFeaturedToast('Failed to add item to cart', 'error');
        console.error('Error:', error);
    }
}

async function addToFeaturedWishlist(productId) {
    try {
        const profileResponse = await customerAPI.auth.getProfile();
        
        if (!profileResponse.success) {
            showFeaturedToast('Please log in to add items to your wishlist', 'warning');
            setTimeout(() => {
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
            }, 1500);
            return;
        }
    } catch (error) {
        showFeaturedToast('Please log in to add items to your wishlist', 'warning');
        setTimeout(() => {
            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
        }, 1500);
        return;
    }

    try {
        let response;
        
        if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
            response = await customerAPI.wishlist.addItem(productId);
        } else {
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
            showFeaturedToast(response.message, 'success');
            
            const wishlistBtn = document.querySelector(`button[onclick="addToFeaturedWishlist(${productId})"]`);
            if (wishlistBtn) {
                wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
                wishlistBtn.classList.remove('text-gray-400', 'hover:text-red-500');
                wishlistBtn.classList.add('text-red-500');
                wishlistBtn.setAttribute('title', 'Added to Wishlist');
                wishlistBtn.onclick = null;
            }
            
            updateWishlistCount();
        } else {
            showFeaturedToast(response.message, 'error');
        }
    } catch (error) {
        showFeaturedToast('Failed to add item to wishlist', 'error');
        console.error('Error:', error);
    }
}

function viewFeaturedProduct(productId) {
    window.location.href = `products/detail.php?id=${productId}`;
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
        
        if (typeof customerAPI !== 'undefined' && customerAPI && customerAPI.wishlist) {
            response = await customerAPI.wishlist.getCount();
        } else {
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
        // Silently fail
    }
}

function showFeaturedToast(message, type = 'success') {
    let toast = document.getElementById('featuredToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'featuredToast';
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
        case 'warning':
            toast.classList.add('bg-yellow-500', 'text-white');
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

