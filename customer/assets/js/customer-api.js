// Customer API JavaScript Client
const customerAPI = {
    baseURL: '/Core1_ecommerce/customer/api',
    
    // Generic request method
    async request(method, endpoint, data = null, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        
        const config = {
            method: method.toUpperCase(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            },
            credentials: 'same-origin', // Include cookies
            ...options
        };

        // Handle FormData vs JSON
        if (options.body instanceof FormData) {
            // Use FormData directly, don't set Content-Type
            config.body = options.body;
        } else {
            // JSON data
            config.headers['Content-Type'] = 'application/json';
            if (data && ['POST', 'PUT', 'PATCH'].includes(config.method)) {
                config.body = JSON.stringify(data);
            }
        }

        try {
            const response = await fetch(url, config);
            
            // Handle different content types
            const contentType = response.headers.get('content-type');
            let responseData;
            
            if (contentType && contentType.includes('application/json')) {
                responseData = await response.json();
            } else {
                responseData = { success: false, message: await response.text() };
            }

            // Add HTTP status to response
            responseData._status = response.status;
            responseData._ok = response.ok;

            return responseData;
            
        } catch (error) {
            console.error('API Request failed:', error);
            return {
                success: false,
                message: 'Network error occurred',
                error: error.message,
                _status: 0,
                _ok: false
            };
        }
    },

    // Convenience methods
    async get(endpoint, params = {}, options = {}) {
        let url = endpoint;
        if (Object.keys(params).length > 0) {
            const searchParams = new URLSearchParams();
            Object.entries(params).forEach(([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    searchParams.append(key, value);
                }
            });
            url += `?${searchParams.toString()}`;
        }
        return this.request('GET', url, null, options);
    },

    async post(endpoint, data, options = {}) {
        return this.request('POST', endpoint, data, options);
    },

    async put(endpoint, data, options = {}) {
        return this.request('PUT', endpoint, data, options);
    },

    async delete(endpoint, options = {}) {
        return this.request('DELETE', endpoint, null, options);
    },

    // Authentication methods
    auth: {
        async register(userData) {
            return customerAPI.post('/auth/register', userData);
        },

        async login(email, password) {
            return customerAPI.post('/auth/login', { email, password });
        },

        async logout() {
            return customerAPI.post('/auth/logout');
        },

        async getProfile() {
            return customerAPI.get('/auth/me');
        },

        async verifyEmail(token) {
            return customerAPI.post('/auth/verify', { token });
        },

        async forgotPassword(email) {
            return customerAPI.post('/auth/forgot-password', { email });
        },

        async resetPassword(token, newPassword) {
            return customerAPI.post('/auth/reset-password', { token, new_password: newPassword });
        }
    },

    // Products methods
    products: {
        async getAll(params = {}) {
            return customerAPI.get('/products', params);
        },

        async getById(id) {
            return customerAPI.get(`/products/${id}`);
        },

        async getCategories() {
            return customerAPI.get('/products/categories');
        },

        async getFeatured(limit = 8) {
            return customerAPI.get('/products/featured', { limit });
        },

        async search(query) {
            return customerAPI.get('/products/search', { q: query });
        }
    },

    // Cart methods
    cart: {
        async getItems() {
            return customerAPI.get('/cart');
        },

        async addItem(productId, quantity = 1) {
            return customerAPI.post('/cart/add', { product_id: productId, quantity });
        },

        async updateItem(cartId, quantity) {
            return customerAPI.put('/cart/update', { cart_id: cartId, quantity });
        },

        async removeItem(cartId) {
            return customerAPI.delete(`/cart/remove/${cartId}`);
        },

        async clear() {
            return customerAPI.delete('/cart/clear');
        },

        async getCount() {
            return customerAPI.get('/cart/count');
        }
    },

    // Orders methods
    orders: {
        async getAll(params = {}) {
            return customerAPI.get('/orders', params);
        },

        async getById(id) {
            return customerAPI.get(`/orders/${id}`);
        },

        async create(orderData) {
            return customerAPI.post('/orders', orderData);
        },

        async updateStatus(id, status) {
            return customerAPI.put(`/orders/${id}/status`, { status });
        }
    },

    // Addresses methods
    addresses: {
        async getAll() {
            return customerAPI.get('/addresses');
        },

        async getById(id) {
            return customerAPI.get(`/addresses/${id}`);
        },

        async create(addressData) {
            return customerAPI.post('/addresses', addressData);
        },

        async update(id, addressData) {
            return customerAPI.put(`/addresses/${id}`, addressData);
        },

        async delete(id) {
            return customerAPI.delete(`/addresses/${id}`);
        },

        async setDefault(id) {
            return customerAPI.put(`/addresses/${id}/default`);
        }
    },

    // Payment methods
    payment: {
        async createPaymentIntent(orderData) {
            return customerAPI.post('/payment/intent', orderData);
        },

        async confirmPayment(paymentIntentId, paymentMethodId) {
            return customerAPI.post('/payment/confirm', {
                payment_intent_id: paymentIntentId,
                payment_method_id: paymentMethodId
            });
        },

        async getPaymentMethods() {
            return customerAPI.get('/payment/methods');
        }
    },


    // Support methods
    support: {
        async getChatRooms() {
            return customerAPI.get('/support/chat/rooms');
        },

        async createChatRoom() {
            return customerAPI.post('/support/chat/rooms');
        },

        async getChatMessages(roomId) {
            return customerAPI.get(`/support/chat/rooms/${roomId}/messages`);
        },

        async sendMessage(roomId, message) {
            return customerAPI.post(`/support/chat/rooms/${roomId}/messages`, { message });
        },

        async getTickets() {
            return customerAPI.get('/support/tickets');
        },

        async createTicket(ticketData, files = null) {
            if (files && files.length > 0) {
                // Create FormData for file upload
                const formData = new FormData();
                
                // Add ticket data to FormData
                Object.keys(ticketData).forEach(key => {
                    if (ticketData[key] !== null && ticketData[key] !== undefined) {
                        formData.append(key, ticketData[key]);
                    }
                });
                
                // Add files to FormData
                files.forEach(file => {
                    formData.append('attachments[]', file);
                });
                
                // Use request method directly with FormData
                return customerAPI.request('POST', '/support/tickets', null, {
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        // Don't set Content-Type, let browser set it for FormData
                    }
                });
            } else {
                // Regular JSON request without files
                return customerAPI.post('/support/tickets', ticketData);
            }
        },

        async getTicket(id) {
            return customerAPI.get(`/support/tickets/${id}`);
        },

        async getTicketMessages(ticketId) {
            return customerAPI.get(`/support/messages/${ticketId}`);
        },

        async sendReply(ticketId, message, files = null) {
            if (files && files.length > 0) {
                // Create FormData for file upload
                const formData = new FormData();
                formData.append('message', message);
                
                // Add files to FormData
                files.forEach(file => {
                    formData.append('attachments[]', file);
                });
                
                // Use request method directly with FormData
                return customerAPI.request('POST', `/support/messages/${ticketId}`, null, {
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        // Don't set Content-Type, let browser set it for FormData
                    }
                });
            } else {
                // Regular JSON request without files
                return customerAPI.post(`/support/messages/${ticketId}`, { message });
            }
        },

        async getUnreadNotifications() {
            return customerAPI.get('/support/notifications/unread');
        }
    },

    // Universal Notifications API
    notifications: {
        async getUnread(types = null, limit = 20) {
            const params = { limit };
            if (types) params.types = Array.isArray(types) ? types.join(',') : types;
            return customerAPI.get('/notifications/unread', params);
        },

        async getAll(options = {}) {
            const params = {
                limit: options.limit || 50,
                offset: options.offset || 0,
                include_read: options.includeRead || false
            };
            if (options.type) params.type = options.type;
            return customerAPI.get('/notifications/all', params);
        },

        async getCount() {
            return customerAPI.get('/notifications/count');
        },

        async markAsRead(notificationId) {
            return customerAPI.post(`/notifications/mark-read/${notificationId}`);
        },

        async markAllAsRead(types = null) {
            const data = {};
            if (types) data.types = types;
            return customerAPI.post('/notifications/mark-read', data);
        },

        async markAsUnread(notificationId) {
            return customerAPI.post(`/notifications/mark-unread/${notificationId}`);
        },

        async archive(notificationIds) {
            return customerAPI.post('/notifications/archive', {
                notification_ids: Array.isArray(notificationIds) ? notificationIds : [notificationIds]
            });
        },

        async delete(notificationId) {
            return customerAPI.delete(`/notifications/${notificationId}`);
        },

        // Filtered getters for specific types
        async getOrderNotifications() {
            return this.getAll({ type: 'order' });
        },

        async getSupportNotifications() {
            return this.getAll({ type: 'support' });
        },

        async getProductNotifications() {
            return this.getAll({ type: 'product' });
        },

        async getPromotionNotifications() {
            return this.getAll({ type: 'promotion' });
        },

        async getPaymentNotifications() {
            return this.getAll({ type: 'payment' });
        },

        async getShippingNotifications() {
            return this.getAll({ type: 'shipping' });
        }
    },

    // Reviews methods
    reviews: {
        async getProductReviews(productId, params = {}) {
            return customerAPI.get('/reviews/list', { product_id: productId, ...params });
        },

        async getReviewableItems(orderId) {
            return customerAPI.get('/reviews/list', { order_id: orderId });
        },

        async getUserReviews(params = {}) {
            return customerAPI.get('/reviews/list', params);
        },

        async submitReview(reviewData) {
            return customerAPI.post('/reviews/submit', reviewData);
        },

        async updateReview(reviewId, reviewData) {
            return customerAPI.put('/reviews/update', { id: reviewId, ...reviewData });
        },

        async deleteReview(reviewId) {
            return customerAPI.delete(`/reviews/delete?id=${reviewId}`);
        }
    },

    // Utility methods
    utils: {
        // Format currency
        formatCurrency(amount, currency = 'PHP') {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 2
            }).format(amount);
        },

        // Format date
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-PH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        // Format relative time
        formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
            if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
            return 'Just now';
        },

        // Debounce function
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Show notification
        showNotification(message, type = 'info', duration = 3000) {
            // Create notification element if it doesn't exist
            let notification = document.getElementById('api-notification');
            if (!notification) {
                notification = document.createElement('div');
                notification.id = 'api-notification';
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 1rem 1.5rem;
                    border-radius: 0.5rem;
                    color: white;
                    font-weight: 500;
                    z-index: 10000;
                    max-width: 300px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    opacity: 0;
                    transform: translateX(100%);
                    transition: all 0.3s ease;
                `;
                document.body.appendChild(notification);
            }

            // Set background color based on type
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };

            notification.style.backgroundColor = colors[type] || colors.info;
            notification.textContent = message;
            
            // Show notification
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 10);

            // Hide notification after duration
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
            }, duration);
        },

        // Handle API errors
        handleError(response, defaultMessage = 'An error occurred') {
            if (response.success === false) {
                const message = response.message || defaultMessage;
                this.showNotification(message, 'error');
                return message;
            }
            return null;
        },

        // Handle API success
        handleSuccess(response, defaultMessage = 'Operation successful') {
            if (response.success === true) {
                const message = response.message || defaultMessage;
                this.showNotification(message, 'success');
                return message;
            }
            return null;
        }
    }
};

// Add request/response interceptors for common functionality
(function() {
    const originalRequest = customerAPI.request;
    
    customerAPI.request = async function(method, endpoint, data, options) {
        // Add loading indicator
        const loadingId = 'api-loading-' + Date.now();
        if (options.showLoading !== false) {
            this.utils.showNotification('Loading...', 'info', 1000);
        }

        try {
            const response = await originalRequest.call(this, method, endpoint, data, options);

            // Handle common response patterns
            if (!response._ok && response._status === 401) {
                this.utils.showNotification('Please log in to continue', 'warning');
                // Optionally redirect to login page
                // window.location.href = '/customer/login.php';
            } else if (!response._ok && response._status >= 500) {
                this.utils.showNotification('Server error occurred', 'error');
            }

            return response;
        } catch (error) {
            this.utils.showNotification('Network error occurred', 'error');
            throw error;
        }
    };
})();

// Add wishlist methods (workaround for parsing issue)
customerAPI.wishlist = {
    async getItems() {
        return customerAPI.get('/wishlist.php');
    },

    async addItem(productId) {
        return customerAPI.post('/wishlist.php/add', { product_id: productId });
    },

    async removeItem(wishlistId) {
        return customerAPI.delete(`/wishlist.php/remove/${wishlistId}`);
    },

    async clear() {
        return customerAPI.delete('/wishlist.php/clear');
    },

    async getCount() {
        return customerAPI.get('/wishlist.php/count');
    }
};

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = customerAPI;
}

// Make available globally
window.customerAPI = customerAPI;