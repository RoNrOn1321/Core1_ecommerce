/**
 * Core1 E-commerce Seller API JavaScript SDK
 * 
 * A comprehensive JavaScript library for interacting with the seller backend API
 */

class SellerAPI {
    constructor(baseURL = 'http://localhost/Core1_ecommerce/seller/api') {
        this.baseURL = baseURL;
        this.isAuthenticated = false;
        this.currentSeller = null;
    }

    /**
     * Make an HTTP request to the API
     * @param {string} endpoint - API endpoint
     * @param {Object} options - Request options
     * @returns {Promise<Object>} - API response
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            credentials: 'include', // Include cookies for session management
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }

    /**
     * GET request helper
     */
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.request(url, { method: 'GET' });
    }

    /**
     * POST request helper
     */
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    /**
     * PUT request helper
     */
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    /**
     * DELETE request helper
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    // ========== AUTHENTICATION METHODS ==========

    /**
     * Login seller
     * @param {string} email - Seller email
     * @param {string} password - Seller password
     * @returns {Promise<Object>} - Login response
     */
    async login(email, password) {
        try {
            const response = await this.post('/auth/login.php', { email, password });
            this.isAuthenticated = true;
            this.currentSeller = response.data;
            return response;
        } catch (error) {
            this.isAuthenticated = false;
            this.currentSeller = null;
            throw error;
        }
    }

    /**
     * Register new seller
     * @param {Object} sellerData - Seller registration data
     * @returns {Promise<Object>} - Registration response
     */
    async register(sellerData) {
        return this.post('/auth/register.php', sellerData);
    }

    /**
     * Get current seller information
     * @returns {Promise<Object>} - Seller information
     */
    async getCurrentSeller() {
        try {
            const response = await this.get('/auth/me.php');
            this.currentSeller = response.data;
            this.isAuthenticated = true;
            return response;
        } catch (error) {
            this.isAuthenticated = false;
            this.currentSeller = null;
            throw error;
        }
    }

    /**
     * Logout seller
     * @returns {Promise<Object>} - Logout response
     */
    async logout() {
        const response = await this.post('/auth/logout.php');
        this.isAuthenticated = false;
        this.currentSeller = null;
        return response;
    }

    // ========== PRODUCT METHODS ==========

    /**
     * Get seller's products
     * @param {Object} filters - Filter options
     * @returns {Promise<Object>} - Products list
     */
    async getProducts(filters = {}) {
        return this.get('/products/index.php', filters);
    }

    /**
     * Create a new product
     * @param {Object} productData - Product data
     * @returns {Promise<Object>} - Created product response
     */
    async createProduct(productData) {
        return this.post('/products/index.php', productData);
    }

    /**
     * Get product details
     * @param {number} productId - Product ID
     * @returns {Promise<Object>} - Product details
     */
    async getProduct(productId) {
        return this.get(`/products/detail?id=${productId}`);
    }

    /**
     * Update product
     * @param {number} productId - Product ID
     * @param {Object} updateData - Update data
     * @returns {Promise<Object>} - Update response
     */
    async updateProduct(productId, updateData) {
        return this.put(`/products/detail?id=${productId}`, updateData);
    }

    /**
     * Delete product
     * @param {number} productId - Product ID
     * @returns {Promise<Object>} - Delete response
     */
    async deleteProduct(productId) {
        return this.delete(`/products/detail?id=${productId}`);
    }

    /**
     * Get product categories
     * @returns {Promise<Object>} - Categories list
     */
    async getCategories() {
        return this.get('/products/categories');
    }

    // ========== ORDER METHODS ==========

    /**
     * Get seller's orders
     * @param {Object} filters - Filter options
     * @returns {Promise<Object>} - Orders list
     */
    async getOrders(filters = {}) {
        return this.get('/orders/index.php', filters);
    }

    /**
     * Get order details
     * @param {number} orderId - Order ID
     * @returns {Promise<Object>} - Order details
     */
    async getOrder(orderId) {
        return this.get(`/orders/detail?id=${orderId}`);
    }

    /**
     * Update order status
     * @param {number} orderId - Order ID
     * @param {string} status - New status
     * @param {string} notes - Optional notes
     * @returns {Promise<Object>} - Update response
     */
    async updateOrderStatus(orderId, status, notes = '') {
        return this.put(`/orders/detail?id=${orderId}`, { status, notes });
    }

    /**
     * Update order tracking information
     * @param {number} orderId - Order ID
     * @param {Object} trackingData - Tracking information
     * @returns {Promise<Object>} - Update response
     */
    async updateOrderTracking(orderId, trackingData) {
        return this.put(`/orders/detail?id=${orderId}`, trackingData);
    }

    /**
     * Get order statistics
     * @returns {Promise<Object>} - Order statistics
     */
    async getOrderStats() {
        return this.get('/orders/stats.php');
    }

    // ========== STORE METHODS ==========

    /**
     * Get store profile
     * @returns {Promise<Object>} - Store profile
     */
    async getStoreProfile() {
        return this.get('/store/profile');
    }

    /**
     * Update store profile
     * @param {Object} profileData - Profile update data
     * @returns {Promise<Object>} - Update response
     */
    async updateStoreProfile(profileData) {
        return this.put('/store/profile', profileData);
    }

    /**
     * Get dashboard data
     * @returns {Promise<Object>} - Dashboard data
     */
    async getDashboard() {
        return this.get('/store/dashboard.php');
    }

    // ========== ANALYTICS METHODS ==========

    /**
     * Get store analytics
     * @param {number} period - Period in days (7, 30, 90, 365)
     * @returns {Promise<Object>} - Analytics data
     */
    async getAnalytics(period = 30) {
        return this.get('/analytics/', { period });
    }

    // ========== UTILITY METHODS ==========

    /**
     * Check if seller is authenticated
     * @returns {boolean} - Authentication status
     */
    isLoggedIn() {
        return this.isAuthenticated;
    }

    /**
     * Get current seller data
     * @returns {Object|null} - Current seller data
     */
    getCurrentSellerData() {
        return this.currentSeller;
    }

    /**
     * Format currency
     * @param {number} amount - Amount to format
     * @param {string} currency - Currency code
     * @returns {string} - Formatted currency
     */
    formatCurrency(amount, currency = 'PHP') {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: currency
        }).format(amount);
    }

    /**
     * Format date
     * @param {string|Date} date - Date to format
     * @returns {string} - Formatted date
     */
    formatDate(date) {
        return new Date(date).toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    /**
     * Format date and time
     * @param {string|Date} date - Date to format
     * @returns {string} - Formatted date and time
     */
    formatDateTime(date) {
        return new Date(date).toLocaleString('en-PH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    /**
     * Get status badge class for orders
     * @param {string} status - Order status
     * @returns {string} - CSS class name
     */
    getOrderStatusClass(status) {
        const statusClasses = {
            'pending': 'badge-warning',
            'processing': 'badge-info',
            'shipped': 'badge-primary',
            'delivered': 'badge-success',
            'cancelled': 'badge-danger'
        };
        return statusClasses[status] || 'badge-secondary';
    }

    /**
     * Get status badge class for products
     * @param {string} status - Product status
     * @returns {string} - CSS class name
     */
    getProductStatusClass(status) {
        const statusClasses = {
            'draft': 'badge-secondary',
            'published': 'badge-success',
            'archived': 'badge-warning'
        };
        return statusClasses[status] || 'badge-secondary';
    }
}

// Export for different module systems
if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = SellerAPI;
} else if (typeof define === 'function' && define.amd) {
    define([], function() {
        return SellerAPI;
    });
} else {
    window.SellerAPI = SellerAPI;
}