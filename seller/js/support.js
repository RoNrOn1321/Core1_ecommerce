class SupportTickets {
    constructor() {
        this.currentPage = 1;
        this.currentTicketId = null;
        this.filters = {
            status: '',
            category: '',
            priority: ''
        };
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadTickets();
    }
    
    bindEvents() {
        // Search and filter events
        document.getElementById('search-btn').addEventListener('click', () => {
            this.applyFilters();
        });
        
        // Filter change events
        ['status-filter', 'category-filter', 'priority-filter'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => {
                this.applyFilters();
            });
        });
        
        // Modal events
        document.getElementById('close-modal').addEventListener('click', () => {
            this.closeModal();
        });
        
        document.getElementById('ticket-modal').addEventListener('click', (e) => {
            if (e.target.id === 'ticket-modal') {
                this.closeModal();
            }
        });
        
        // Response form
        document.getElementById('response-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitResponse();
        });
        
        // Escape key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });
    }
    
    applyFilters() {
        this.filters.status = document.getElementById('status-filter').value;
        this.filters.category = document.getElementById('category-filter').value;
        this.filters.priority = document.getElementById('priority-filter').value;
        this.currentPage = 1;
        this.loadTickets();
    }
    
    async loadTickets() {
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: 20,
                ...this.filters
            });
            
            const response = await fetch(`api/support/list.php?${params}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderTickets(data.data.tickets);
                this.renderPagination(data.data.pagination);
            } else {
                this.showError('Failed to load tickets');
            }
        } catch (error) {
            console.error('Error loading tickets:', error);
            this.showError('Failed to load tickets');
        } finally {
            this.hideLoading();
        }
    }
    
    renderTickets(tickets) {
        const container = document.getElementById('tickets-container');
        
        if (tickets.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">No support tickets found</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = tickets.map(ticket => `
            <div class="p-6 hover:bg-gray-50 cursor-pointer transition duration-200" onclick="supportTickets.openTicket(${ticket.id})">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">${this.escapeHtml(ticket.subject)}</h3>
                            <span class="text-sm text-gray-600">#${ticket.ticket_number}</span>
                        </div>
                        <div class="flex items-center space-x-4 mb-3">
                            <span class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user mr-1"></i>
                                ${this.escapeHtml(ticket.customer_name)}
                            </span>
                            <span class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-envelope mr-1"></i>
                                ${this.escapeHtml(ticket.email)}
                            </span>
                            <span class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-comments mr-1"></i>
                                ${ticket.message_count} messages
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="${this.getStatusClasses(ticket.status)}">${this.formatStatus(ticket.status)}</span>
                            <span class="${this.getPriorityClasses(ticket.priority)}">${this.formatPriority(ticket.priority)}</span>
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">${this.formatCategory(ticket.category)}</span>
                        </div>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        <p>Created: ${this.formatDateTime(ticket.created_at)}</p>
                        <p>Updated: ${this.formatDateTime(ticket.updated_at)}</p>
                        ${ticket.last_message_at ? `<p>Last message: ${this.formatDateTime(ticket.last_message_at)}</p>` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    renderPagination(pagination) {
        const container = document.getElementById('pagination-container');
        
        if (pagination.pages <= 1) {
            container.innerHTML = '';
            return;
        }
        
        const prevDisabled = pagination.page === 1;
        const nextDisabled = pagination.page === pagination.pages;
        
        container.innerHTML = `
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Showing ${(pagination.page - 1) * pagination.limit + 1} to ${Math.min(pagination.page * pagination.limit, pagination.total)} of ${pagination.total} tickets
                </div>
                <div class="flex space-x-2">
                    <button onclick="supportTickets.changePage(${pagination.page - 1})" 
                        ${prevDisabled ? 'disabled' : ''} 
                        class="px-3 py-2 border border-gray-300 rounded-md text-sm ${prevDisabled ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">
                        Previous
                    </button>
                    <span class="px-3 py-2 text-sm text-gray-700">
                        Page ${pagination.page} of ${pagination.pages}
                    </span>
                    <button onclick="supportTickets.changePage(${pagination.page + 1})" 
                        ${nextDisabled ? 'disabled' : ''} 
                        class="px-3 py-2 border border-gray-300 rounded-md text-sm ${nextDisabled ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50'}">
                        Next
                    </button>
                </div>
            </div>
        `;
    }
    
    changePage(page) {
        this.currentPage = page;
        this.loadTickets();
    }
    
    async openTicket(ticketId) {
        this.currentTicketId = ticketId;
        
        try {
            const response = await fetch(`api/support/get.php?id=${ticketId}`);
            const data = await response.json();
            
            if (data.success) {
                this.showTicketModal(data.data);
            } else {
                this.showError('Failed to load ticket details');
            }
        } catch (error) {
            console.error('Error loading ticket:', error);
            this.showError('Failed to load ticket details');
        }
    }
    
    showTicketModal(ticket) {
        // Populate modal header
        document.getElementById('modal-ticket-title').textContent = ticket.subject;
        document.getElementById('modal-ticket-number').textContent = `#${ticket.ticket_number}`;
        document.getElementById('modal-ticket-status').textContent = this.formatStatus(ticket.status);
        document.getElementById('modal-ticket-status').className = `px-2 py-1 text-xs rounded-full ${this.getStatusClasses(ticket.status)}`;
        document.getElementById('modal-ticket-priority').textContent = this.formatPriority(ticket.priority);
        document.getElementById('modal-ticket-priority').className = `px-2 py-1 text-xs rounded-full ${this.getPriorityClasses(ticket.priority)}`;
        document.getElementById('modal-ticket-category').textContent = this.formatCategory(ticket.category);
        
        // Populate ticket info
        document.getElementById('modal-customer-name').textContent = ticket.customer_name;
        document.getElementById('modal-customer-email').textContent = ticket.email;
        document.getElementById('modal-created-at').textContent = this.formatDateTime(ticket.created_at);
        document.getElementById('modal-updated-at').textContent = this.formatDateTime(ticket.updated_at);
        document.getElementById('modal-message-count').textContent = ticket.message_count;
        
        // Handle order info
        if (ticket.order_number) {
            document.getElementById('modal-order-info').classList.remove('hidden');
            document.getElementById('modal-order-number').textContent = ticket.order_number;
        } else {
            document.getElementById('modal-order-info').classList.add('hidden');
        }
        
        // Render messages
        this.renderMessages(ticket.messages);
        
        // Show modal
        document.getElementById('ticket-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    renderMessages(messages) {
        const container = document.getElementById('messages-container');
        
        container.innerHTML = messages.map(message => `
            <div class="flex ${message.sender_type === 'customer' ? 'justify-start' : 'justify-end'}">
                <div class="max-w-3xl ${message.sender_type === 'customer' ? 'bg-gray-100' : 'bg-blue-100'} rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-sm ${message.sender_type === 'customer' ? 'text-gray-800' : 'text-blue-800'}">
                            ${this.escapeHtml(message.sender_name)}
                        </span>
                        <span class="text-xs text-gray-500">${this.formatDateTime(message.created_at)}</span>
                    </div>
                    <div class="text-gray-800 whitespace-pre-wrap">${this.escapeHtml(message.message)}</div>
                    ${message.is_internal ? '<div class="mt-2 text-xs text-orange-600 font-medium">Internal Note</div>' : ''}
                </div>
            </div>
        `).join('');
        
        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }
    
    async submitResponse() {
        const message = document.getElementById('response-message').value.trim();
        const status = document.getElementById('response-status').value;
        const isInternal = document.getElementById('response-internal').checked;
        
        if (!message) {
            this.showError('Please enter a response message');
            return;
        }
        
        const submitBtn = document.querySelector('#response-form button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('api/support/respond.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ticket_id: this.currentTicketId,
                    message: message,
                    status: status,
                    is_internal: isInternal
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Clear form
                document.getElementById('response-message').value = '';
                document.getElementById('response-status').value = '';
                document.getElementById('response-internal').checked = false;
                
                // Reload ticket details
                this.openTicket(this.currentTicketId);
                
                // Reload tickets list
                this.loadTickets();
                
                this.showSuccess('Response sent successfully');
            } else {
                this.showError(data.message || 'Failed to send response');
            }
        } catch (error) {
            console.error('Error sending response:', error);
            this.showError('Failed to send response');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }
    
    closeModal() {
        document.getElementById('ticket-modal').classList.add('hidden');
        document.body.style.overflow = '';
        this.currentTicketId = null;
    }
    
    showLoading() {
        document.getElementById('loading-spinner').classList.remove('hidden');
        document.getElementById('tickets-container').classList.add('hidden');
    }
    
    hideLoading() {
        document.getElementById('loading-spinner').classList.add('hidden');
        document.getElementById('tickets-container').classList.remove('hidden');
    }
    
    showError(message) {
        // You can implement a toast notification here
        alert(message);
    }
    
    showSuccess(message) {
        // You can implement a toast notification here
        alert(message);
    }
    
    // Utility methods
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatDateTime(dateString) {
        return new Date(dateString).toLocaleString();
    }
    
    formatStatus(status) {
        const statusMap = {
            'open': 'Open',
            'in_progress': 'In Progress',
            'waiting_customer': 'Waiting Customer',
            'resolved': 'Resolved',
            'closed': 'Closed'
        };
        return statusMap[status] || status;
    }
    
    formatPriority(priority) {
        return priority.charAt(0).toUpperCase() + priority.slice(1);
    }
    
    formatCategory(category) {
        return category.charAt(0).toUpperCase() + category.slice(1);
    }
    
    getStatusClasses(status) {
        const classes = {
            'open': 'bg-red-100 text-red-800',
            'in_progress': 'bg-yellow-100 text-yellow-800',
            'waiting_customer': 'bg-blue-100 text-blue-800',
            'resolved': 'bg-green-100 text-green-800',
            'closed': 'bg-gray-100 text-gray-800'
        };
        return `px-2 py-1 text-xs rounded-full ${classes[status] || 'bg-gray-100 text-gray-800'}`;
    }
    
    getPriorityClasses(priority) {
        const classes = {
            'low': 'bg-gray-100 text-gray-800',
            'medium': 'bg-yellow-100 text-yellow-800',
            'high': 'bg-orange-100 text-orange-800',
            'urgent': 'bg-red-100 text-red-800'
        };
        return `px-2 py-1 text-xs rounded-full ${classes[priority] || 'bg-gray-100 text-gray-800'}`;
    }
}

// Initialize the support tickets system
const supportTickets = new SupportTickets();