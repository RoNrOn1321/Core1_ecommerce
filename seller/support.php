<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/layout.php';

$auth = new SellerAuth($pdo);
$auth->requireLogin();

startLayout('Support Tickets');
?>
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Support Tickets</h1>
                <p class="text-gray-600">Manage customer support requests and inquiries</p>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="waiting_customer">Waiting Customer</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="category-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <option value="order">Order</option>
                            <option value="product">Product</option>
                            <option value="payment">Payment</option>
                            <option value="shipping">Shipping</option>
                            <option value="technical">Technical</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Priority</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button id="search-btn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </div>
            </div>

            <!-- Support Tickets List -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Support Tickets</h2>
                </div>
                
                <div id="loading-spinner" class="text-center py-8 hidden">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    <p class="text-gray-600 mt-2">Loading tickets...</p>
                </div>

                <div id="tickets-container" class="divide-y divide-gray-200">
                    <!-- Tickets will be loaded here -->
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="p-6 border-t border-gray-200">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>

            <!-- Ticket Detail Modal -->
            <div id="ticket-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 id="modal-ticket-title" class="text-2xl font-bold text-gray-900"></h2>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span id="modal-ticket-number" class="text-sm text-gray-600"></span>
                                        <span id="modal-ticket-status" class="px-2 py-1 text-xs rounded-full"></span>
                                        <span id="modal-ticket-priority" class="px-2 py-1 text-xs rounded-full"></span>
                                        <span id="modal-ticket-category" class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full"></span>
                                    </div>
                                </div>
                                <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Messages Section -->
                                <div class="lg:col-span-2">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversation</h3>
                                    <div id="messages-container" class="space-y-4 max-h-96 overflow-y-auto mb-6">
                                        <!-- Messages will be loaded here -->
                                    </div>
                                    
                                    <!-- Response Form -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900 mb-3">Add Response</h4>
                                        <form id="response-form">
                                            <div class="mb-3">
                                                <textarea id="response-message" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                    rows="4" 
                                                    placeholder="Type your response..."></textarea>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center space-x-4">
                                                    <select id="response-status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        <option value="">Keep current status</option>
                                                        <option value="open">Open</option>
                                                        <option value="in_progress">In Progress</option>
                                                        <option value="waiting_customer">Waiting Customer</option>
                                                        <option value="resolved">Resolved</option>
                                                    </select>
                                                    <label class="flex items-center">
                                                        <input type="checkbox" id="response-internal" class="mr-2">
                                                        <span class="text-sm text-gray-700">Internal note</span>
                                                    </label>
                                                </div>
                                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                                                    <i class="fas fa-paper-plane mr-2"></i>Send Response
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Ticket Info Section -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Information</h3>
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                        <div>
                                            <span class="text-sm text-gray-600">Customer:</span>
                                            <p id="modal-customer-name" class="font-medium"></p>
                                            <p id="modal-customer-email" class="text-sm text-gray-600"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Created:</span>
                                            <p id="modal-created-at" class="font-medium"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Last Updated:</span>
                                            <p id="modal-updated-at" class="font-medium"></p>
                                        </div>
                                        <div id="modal-order-info" class="hidden">
                                            <span class="text-sm text-gray-600">Related Order:</span>
                                            <p id="modal-order-number" class="font-medium text-blue-600"></p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-600">Messages:</span>
                                            <p id="modal-message-count" class="font-medium"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/support.js"></script>

<?php endLayout(); ?>