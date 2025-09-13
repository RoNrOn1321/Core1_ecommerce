<?php
$page_title = "Reviews";

// Include necessary files
require_once 'config/database.php';
require_once 'includes/auth.php';

// Initialize authentication
$auth = new SellerAuth($pdo);
$auth->requireWebLogin();

$sellerId = $_SESSION['seller_id'];
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Reviews & Ratings</h1>
                <p class="text-gray-600">Manage customer feedback and improve your store reputation</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="statusFilter" onchange="loadReviews(1)" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Reviews</option>
                            <option value="pending">Pending Approval</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <select id="ratingFilter" onchange="loadReviews(1)" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                        <select id="productFilter" onchange="loadReviews(1)" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Products</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button onclick="loadReviews(1)" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6" id="statsCards">
                <!-- Loading stats -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 bg-gray-200 rounded-full mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-8 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 bg-gray-200 rounded-full mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-8 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 bg-gray-200 rounded-full mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-8 bg-gray-200 rounded"></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 bg-gray-200 rounded-full mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-8 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Reviews</h2>
                </div>
                
                <div id="reviewsContainer" class="p-6">
                    <!-- Loading state -->
                    <div class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-2 text-gray-600">Loading reviews...</span>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div id="pagination" class="p-6 border-t border-gray-200">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
        </div>
    </main>

<!-- Response Modal -->
<div id="responseModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Respond to Review</h3>
                    <button onclick="closeResponseModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="reviewDetails" class="mb-4">
                    <!-- Review details will be shown here -->
                </div>
                <form id="responseForm" onsubmit="submitResponse(event)">
                    <input type="hidden" id="responseReviewId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Response</label>
                        <textarea id="responseText" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Write a professional response to this review..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeResponseModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Submit Response
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let currentFilters = {};
let sellerAPI;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the API
    sellerAPI = new SellerAPI();
    
    loadProducts();
    loadReviews(1);
    loadStats();
});

async function loadProducts() {
    try {
        const response = await sellerAPI.getProducts({ limit: 100 });
        console.log('Products API response:', response); // Debug log
        if (response.success && response.products) {
            const select = document.getElementById('productFilter');
            response.products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.name;
                select.appendChild(option);
            });
        } else {
            console.warn('No products found or API error:', response);
        }
    } catch (error) {
        console.error('Failed to load products:', error);
    }
}

async function loadStats() {
    try {
        const response = await sellerAPI.get('/reviews/', { limit: 1000 });
        console.log('Reviews stats API response:', response); // Debug log
        if (response.success && response.data) {
            const reviews = response.data;
            const total = reviews.length;
            const pending = reviews.filter(r => !r.is_approved).length;
            const approved = reviews.filter(r => r.is_approved).length;
            const avgRating = reviews.length > 0 
                ? (reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length).toFixed(1)
                : 0;

            document.getElementById('statsCards').innerHTML = `
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-star text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                            <p class="text-2xl font-bold text-gray-900">${total}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">${pending}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Approved</p>
                            <p class="text-2xl font-bold text-gray-900">${approved}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Avg Rating</p>
                            <p class="text-2xl font-bold text-gray-900">${avgRating}</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            console.warn('No stats data found or API error:', response);
        }
    } catch (error) {
        console.error('Failed to load stats:', error);
    }
}

async function loadReviews(page = 1) {
    currentPage = page;
    
    const filters = {
        page: page,
        limit: 10,
        status: document.getElementById('statusFilter').value,
        rating: document.getElementById('ratingFilter').value,
        product_id: document.getElementById('productFilter').value
    };
    
    currentFilters = filters;
    
    document.getElementById('reviewsContainer').innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading reviews...</span>
        </div>
    `;
    
    try {
        const response = await sellerAPI.get('/reviews/', filters);
        
        if (response.success) {
            renderReviews(response.data);
            renderPagination(response.pagination);
        } else {
            showError('Failed to load reviews: ' + response.message);
        }
    } catch (error) {
        showError('Failed to load reviews');
        console.error('Error:', error);
    }
}

function renderReviews(reviews) {
    const container = document.getElementById('reviewsContainer');
    
    if (reviews.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-star text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews found</h3>
                <p class="text-gray-500">No reviews match your current filters.</p>
            </div>
        `;
        return;
    }
    
    const reviewsHTML = reviews.map(review => `
        <div class="border border-gray-200 rounded-lg p-6 mb-4 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                        ${review.customer_name.charAt(0)}
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">${review.customer_name}</h4>
                        <div class="flex items-center space-x-2 mt-1">
                            <div class="text-yellow-400">${generateStars(review.rating)}</div>
                            <span class="text-sm text-gray-500">${new Date(review.created_at).toLocaleDateString()}</span>
                            ${review.is_verified_purchase ? '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Verified Purchase</span>' : ''}
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                        review.is_approved 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-yellow-100 text-yellow-800'
                    }">
                        ${review.is_approved ? 'Approved' : 'Pending'}
                    </span>
                </div>
            </div>
            
            <div class="mb-4">
                <h5 class="font-medium text-gray-900 mb-2">${review.title}</h5>
                <p class="text-gray-700">${review.review_text || 'No additional comments'}</p>
            </div>
            
            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                <span>Product: <strong>${review.product_name}</strong></span>
                ${review.order_number ? `<span>Order: <strong>#${review.order_number}</strong></span>` : ''}
            </div>
            
            ${review.seller_response ? `
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-reply text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-800"><strong>Your Response:</strong></p>
                            <p class="text-sm text-blue-700 mt-1">${review.seller_response}</p>
                        </div>
                    </div>
                </div>
            ` : ''}
            
            <div class="flex space-x-2">
                ${!review.is_approved ? `
                    <button onclick="approveReview(${review.id})" 
                            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-1"></i>Approve
                    </button>
                ` : `
                    <button onclick="rejectReview(${review.id})" 
                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-1"></i>Reject
                    </button>
                `}
                
                ${!review.seller_response ? `
                    <button onclick="showResponseModal(${review.id}, '${review.customer_name.replace(/'/g, "\\'")}', '${review.title.replace(/'/g, "\\'")} ')" 
                            class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                        <i class="fas fa-reply mr-1"></i>Respond
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    container.innerHTML = reviewsHTML;
}

function renderPagination(pagination) {
    const container = document.getElementById('pagination');
    
    if (!pagination || pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let paginationHTML = '<div class="flex items-center justify-center space-x-2">';
    
    if (pagination.has_prev) {
        paginationHTML += `
            <button onclick="loadReviews(${pagination.current_page - 1})" 
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
    }
    
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const isActive = i === pagination.current_page;
        paginationHTML += `
            <button onclick="loadReviews(${i})" 
                    class="px-3 py-2 rounded-lg transition-colors ${isActive ? 'bg-blue-600 text-white' : 'border border-gray-300 hover:bg-gray-50'}">
                ${i}
            </button>
        `;
    }
    
    if (pagination.has_next) {
        paginationHTML += `
            <button onclick="loadReviews(${pagination.current_page + 1})" 
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
    }
    
    paginationHTML += '</div>';
    container.innerHTML = paginationHTML;
}

function generateStars(rating) {
    let starsHTML = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            starsHTML += '<i class="fas fa-star"></i>';
        } else {
            starsHTML += '<i class="far fa-star"></i>';
        }
    }
    return starsHTML;
}

async function approveReview(reviewId) {
    if (!confirm('Are you sure you want to approve this review?')) {
        return;
    }
    
    try {
        const response = await sellerAPI.put('/reviews/', {
            review_id: reviewId,
            action: 'approve'
        });
        
        if (response.success) {
            showSuccess(response.message);
            loadReviews(currentPage);
            loadStats();
        } else {
            showError(response.message);
        }
    } catch (error) {
        showError('Failed to approve review');
        console.error('Error:', error);
    }
}

async function rejectReview(reviewId) {
    if (!confirm('Are you sure you want to reject this review?')) {
        return;
    }
    
    try {
        const response = await sellerAPI.put('/reviews/', {
            review_id: reviewId,
            action: 'reject'
        });
        
        if (response.success) {
            showSuccess(response.message);
            loadReviews(currentPage);
            loadStats();
        } else {
            showError(response.message);
        }
    } catch (error) {
        showError('Failed to reject review');
        console.error('Error:', error);
    }
}

function showResponseModal(reviewId, customerName, reviewTitle) {
    document.getElementById('responseReviewId').value = reviewId;
    document.getElementById('reviewDetails').innerHTML = `
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-medium text-gray-900">Review by ${customerName}</h4>
            <p class="text-gray-700 mt-1">"${reviewTitle}"</p>
        </div>
    `;
    document.getElementById('responseText').value = '';
    document.getElementById('responseModal').classList.remove('hidden');
}

function closeResponseModal() {
    document.getElementById('responseModal').classList.add('hidden');
}

async function submitResponse(event) {
    event.preventDefault();
    
    const reviewId = document.getElementById('responseReviewId').value;
    const responseText = document.getElementById('responseText').value.trim();
    
    if (!responseText) {
        showError('Response cannot be empty');
        return;
    }
    
    try {
        const response = await sellerAPI.put('/reviews/', {
            review_id: reviewId,
            action: 'respond',
            response: responseText
        });
        
        if (response.success) {
            showSuccess(response.message);
            closeResponseModal();
            loadReviews(currentPage);
        } else {
            showError(response.message);
        }
    } catch (error) {
        showError('Failed to submit response');
        console.error('Error:', error);
    }
}

function showSuccess(message) {
    const toast = createToast(message, 'success');
    document.body.appendChild(toast);
    setTimeout(() => document.body.removeChild(toast), 3000);
}

function showError(message) {
    const toast = createToast(message, 'error');
    document.body.appendChild(toast);
    setTimeout(() => document.body.removeChild(toast), 3000);
}

function createToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    toast.textContent = message;
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    return toast;
}
</script>

<?php include 'includes/footer.php'; ?>