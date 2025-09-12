<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Support Tickets - Lumino</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gray-50">

<script src="../assets/js/customer-api.js"></script>

<?php include '../components/navbar.php'; ?>

<!-- Header Section -->
<section class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                    <a href="../index.php" class="hover:text-blue-600">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="index.php" class="hover:text-blue-600">Support</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900">My Tickets</span>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">My Support Tickets</h1>
                <p class="text-gray-600 mt-2">Track and manage all your support requests</p>
            </div>
            <div class="space-x-4">
                <a href="create-ticket.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>New Ticket
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Tickets Dashboard -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <!-- Authentication Check -->
        <div id="authCheckLoader" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">Loading your tickets...</p>
        </div>

        <!-- Login Required -->
        <div id="loginRequired" class="max-w-md mx-auto text-center py-12" style="display: none;">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <i class="fas fa-lock text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Login Required</h3>
                <p class="text-gray-600 mb-6">You need to be logged in to view your support tickets.</p>
                <div class="space-y-4">
                    <a href="../login.php?redirect=support/my-tickets.php" class="block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login to View Tickets
                    </a>
                    <a href="../register.php?redirect=support/my-tickets.php" class="block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div id="ticketsContent" style="display: none;">
            <!-- Filter and Search -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                        <!-- Search -->
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search tickets..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex space-x-4">
                            <select id="statusFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="waiting_customer">Waiting for Me</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                            
                            <select id="categoryFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Categories</option>
                                <option value="order">Order Issues</option>
                                <option value="product">Product Questions</option>
                                <option value="payment">Payment & Billing</option>
                                <option value="shipping">Shipping & Delivery</option>
                                <option value="technical">Technical Support</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div id="quickStats" class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <!-- Stats will be populated here -->
            </div>

            <!-- Tickets List -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Support Tickets</h3>
                        <div id="ticketCount" class="text-sm text-gray-500">
                            <!-- Ticket count will be shown here -->
                        </div>
                    </div>
                </div>
                
                <!-- Loading State -->
                <div id="ticketsLoading" class="p-12 text-center">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Loading your tickets...</p>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="p-12 text-center" style="display: none;">
                    <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-500 mb-2">No tickets found</h3>
                    <p class="text-gray-400 mb-6">You haven't created any support tickets yet.</p>
                    <a href="create-ticket.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Create Your First Ticket
                    </a>
                </div>
                
                <!-- Tickets Table -->
                <div id="ticketsTable" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ticketsTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Tickets will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="mt-8 flex items-center justify-between" style="display: none;">
                <div class="text-sm text-gray-500">
                    Showing <span id="showingFrom">1</span> to <span id="showingTo">10</span> of <span id="totalTickets">0</span> tickets
                </div>
                <div class="flex space-x-2">
                    <button id="prevPage" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    <div id="pageNumbers" class="flex space-x-1">
                        <!-- Page numbers will be populated here -->
                    </div>
                    <button id="nextPage" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../components/footer.php'; ?>

<script>
let allTickets = [];
let filteredTickets = [];
let currentPage = 1;
let ticketsPerPage = 10;

document.addEventListener('DOMContentLoaded', function() {
    checkAuthenticationAndLoadTickets();
    setupEventListeners();
});

async function checkAuthenticationAndLoadTickets() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success && response.customer) {
            // User is authenticated
            document.getElementById('authCheckLoader').style.display = 'none';
            document.getElementById('ticketsContent').style.display = 'block';
            
            await loadTickets();
        } else {
            // User not authenticated
            document.getElementById('authCheckLoader').style.display = 'none';
            document.getElementById('loginRequired').style.display = 'block';
        }
    } catch (error) {
        console.error('Authentication check failed:', error);
        document.getElementById('authCheckLoader').style.display = 'none';
        document.getElementById('loginRequired').style.display = 'block';
    }
}

async function loadTickets() {
    document.getElementById('ticketsLoading').style.display = 'block';
    document.getElementById('ticketsTable').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';
    
    try {
        const response = await customerAPI.support.getTickets();
        
        if (response.success) {
            allTickets = response.data || [];
            
            if (allTickets.length === 0) {
                document.getElementById('ticketsLoading').style.display = 'none';
                document.getElementById('emptyState').style.display = 'block';
            } else {
                filteredTickets = [...allTickets];
                renderQuickStats();
                applyFilters();
                document.getElementById('ticketsLoading').style.display = 'none';
                document.getElementById('ticketsTable').style.display = 'block';
            }
        } else {
            throw new Error(response.message || 'Failed to load tickets');
        }
    } catch (error) {
        console.error('Failed to load tickets:', error);
        document.getElementById('ticketsLoading').style.display = 'none';
        showToast('Failed to load tickets. Please refresh the page.', 'error');
    }
}

function renderQuickStats() {
    const stats = {
        total: allTickets.length,
        open: allTickets.filter(t => t.status === 'open').length,
        inProgress: allTickets.filter(t => t.status === 'in_progress').length,
        resolved: allTickets.filter(t => t.status === 'resolved').length
    };
    
    const statsHTML = `
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-gray-900">${stats.total}</div>
            <div class="text-sm text-gray-500">Total Tickets</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-red-600">${stats.open}</div>
            <div class="text-sm text-gray-500">Open</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-yellow-600">${stats.inProgress}</div>
            <div class="text-sm text-gray-500">In Progress</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-2xl font-bold text-green-600">${stats.resolved}</div>
            <div class="text-sm text-gray-500">Resolved</div>
        </div>
    `;
    
    document.getElementById('quickStats').innerHTML = statsHTML;
}

function setupEventListeners() {
    // Search and filters
    document.getElementById('searchInput').addEventListener('input', debounce(applyFilters, 300));
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('categoryFilter').addEventListener('change', applyFilters);
    
    // Pagination
    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderTickets();
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', () => {
        const totalPages = Math.ceil(filteredTickets.length / ticketsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTickets();
        }
    });
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    
    filteredTickets = allTickets.filter(ticket => {
        const matchesSearch = !searchTerm || 
            ticket.subject.toLowerCase().includes(searchTerm) ||
            ticket.ticket_number.toLowerCase().includes(searchTerm);
        
        const matchesStatus = !statusFilter || ticket.status === statusFilter;
        const matchesCategory = !categoryFilter || ticket.category === categoryFilter;
        
        return matchesSearch && matchesStatus && matchesCategory;
    });
    
    currentPage = 1;
    renderTickets();
}

function renderTickets() {
    const startIndex = (currentPage - 1) * ticketsPerPage;
    const endIndex = startIndex + ticketsPerPage;
    const paginatedTickets = filteredTickets.slice(startIndex, endIndex);
    
    if (filteredTickets.length === 0) {
        document.getElementById('ticketsTable').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('ticketsTable').style.display = 'block';
    
    // Render table rows
    const tbody = document.getElementById('ticketsTableBody');
    tbody.innerHTML = paginatedTickets.map(ticket => {
        const statusClass = getStatusClass(ticket.status);
        const priorityClass = getPriorityClass(ticket.priority);
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900">#${ticket.ticket_number}</div>
                    <div class="text-sm text-gray-500">ID: ${ticket.id}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900 line-clamp-2">${escapeHtml(ticket.subject)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                        ${ticket.category.replace('_', ' ')}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${priorityClass}">
                        ${ticket.priority}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">
                        ${ticket.status.replace('_', ' ')}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${new Date(ticket.updated_at).toLocaleDateString()}
                    <div class="text-xs text-gray-400">${new Date(ticket.updated_at).toLocaleTimeString()}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="viewTicket('${ticket.id}')" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i>View
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    // Update ticket count
    document.getElementById('ticketCount').textContent = `${filteredTickets.length} ticket(s)`;
    
    // Render pagination
    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(filteredTickets.length / ticketsPerPage);
    const startIndex = (currentPage - 1) * ticketsPerPage;
    const endIndex = Math.min(startIndex + ticketsPerPage, filteredTickets.length);
    
    if (totalPages <= 1) {
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    document.getElementById('paginationContainer').style.display = 'flex';
    
    // Update showing text
    document.getElementById('showingFrom').textContent = startIndex + 1;
    document.getElementById('showingTo').textContent = endIndex;
    document.getElementById('totalTickets').textContent = filteredTickets.length;
    
    // Update buttons
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === totalPages;
    
    // Render page numbers
    const pageNumbers = document.getElementById('pageNumbers');
    const pages = [];
    
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        pages.push(i);
    }
    
    pageNumbers.innerHTML = pages.map(page => `
        <button onclick="goToPage(${page})" class="px-3 py-2 rounded-lg ${page === currentPage ? 'bg-blue-600 text-white' : 'border border-gray-300 hover:bg-gray-50'}">
            ${page}
        </button>
    `).join('');
}

function goToPage(page) {
    currentPage = page;
    renderTickets();
}

function viewTicket(ticketId) {
    window.location.href = `ticket-detail.php?id=${ticketId}`;
}

function getStatusClass(status) {
    const statusClasses = {
        'open': 'bg-red-100 text-red-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'waiting_customer': 'bg-blue-100 text-blue-800',
        'resolved': 'bg-green-100 text-green-800',
        'closed': 'bg-gray-100 text-gray-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
}

function getPriorityClass(priority) {
    const priorityClasses = {
        'urgent': 'bg-red-100 text-red-800',
        'high': 'bg-orange-100 text-orange-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'low': 'bg-green-100 text-green-800'
    };
    return priorityClasses[priority] || 'bg-gray-100 text-gray-800';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

// Toast notification function
function showToast(message, type = 'success') {
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
        return;
    }
    
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-0 opacity-100`;
    
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
        case 'warning':
            toast.classList.add('bg-yellow-500', 'text-white');
            break;
    }
    
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

</body>
</html>