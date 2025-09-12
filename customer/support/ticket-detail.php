<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket Details - Lumino</title>
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
        <div id="headerContent">
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="../index.php" class="hover:text-blue-600">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="index.php" class="hover:text-blue-600">Support</a>
                <i class="fas fa-chevron-right"></i>
                <a href="my-tickets.php" class="hover:text-blue-600">My Tickets</a>
                <i class="fas fa-chevron-right"></i>
                <span class="text-gray-900">Ticket Details</span>
            </nav>
            <div class="flex items-center justify-between">
                <div>
                    <h1 id="ticketTitle" class="text-3xl font-bold text-gray-900">Loading...</h1>
                    <p id="ticketSubtitle" class="text-gray-600 mt-2">Please wait while we load your ticket details</p>
                </div>
                <div class="space-x-4">
                    <a href="my-tickets.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <!-- Authentication Check -->
        <div id="authCheckLoader" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">Checking access permissions...</p>
        </div>

        <!-- Login Required -->
        <div id="loginRequired" class="max-w-md mx-auto text-center py-12" style="display: none;">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <i class="fas fa-lock text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Login Required</h3>
                <p class="text-gray-600 mb-6">You need to be logged in to view ticket details.</p>
                <a href="../login.php?redirect=support/ticket-detail.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-block">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login to Continue
                </a>
            </div>
        </div>

        <!-- Ticket Not Found -->
        <div id="ticketNotFound" class="max-w-md mx-auto text-center py-12" style="display: none;">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Ticket Not Found</h3>
                <p class="text-gray-600 mb-6">The ticket you're looking for doesn't exist or you don't have permission to view it.</p>
                <a href="my-tickets.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-block">
                    <i class="fas fa-list-alt mr-2"></i>View My Tickets
                </a>
            </div>
        </div>

        <!-- Ticket Details -->
        <div id="ticketContent" style="display: none;">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Ticket Information -->
                    <div class="bg-white rounded-xl shadow-lg mb-8">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 id="ticketSubject" class="text-xl font-bold text-gray-900 mb-2"></h2>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span id="ticketNumber"></span>
                                        <span id="ticketCreated"></span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span id="ticketStatus" class="px-3 py-1 text-sm font-medium rounded-full"></span>
                                    <span id="ticketPriority" class="px-3 py-1 text-sm font-medium rounded-full"></span>
                                </div>
                            </div>
                        </div>
                        <div id="ticketDescription" class="p-6">
                            <!-- Ticket description will be loaded here -->
                        </div>
                    </div>

                    <!-- Messages/Conversation -->
                    <div class="bg-white rounded-xl shadow-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Conversation</h3>
                                <span id="messageCount" class="text-sm text-gray-500"></span>
                            </div>
                        </div>
                        
                        <div id="messagesContainer" class="max-h-96 overflow-y-auto">
                            <!-- Messages loading -->
                            <div id="messagesLoading" class="p-8 text-center">
                                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Loading conversation...</p>
                            </div>
                            
                            <!-- Messages will be loaded here -->
                            <div id="messagesList"></div>
                        </div>

                        <!-- Reply Form (only if ticket is not closed) -->
                        <div id="replySection" class="border-t border-gray-200 p-6" style="display: none;">
                            <form id="replyForm">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Add a Reply</label>
                                    <textarea id="replyMessage" rows="4" 
                                              placeholder="Type your response here..." 
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                              required></textarea>
                                </div>
                                
                                <!-- File Upload -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Attachments (Optional)</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                                        <div class="text-center">
                                            <i class="fas fa-paperclip text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-600">
                                                <button type="button" id="attachFileBtn" class="text-blue-600 hover:text-blue-800 font-medium">Click to attach files</button>
                                                or drag and drop
                                            </p>
                                            <input type="file" id="attachmentInput" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt" class="hidden">
                                            <p class="text-xs text-gray-500 mt-1">Max 5MB per file</p>
                                        </div>
                                        <div id="attachmentsList" class="mt-3 space-y-2"></div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Your reply will be sent to our support team
                                    </p>
                                    <button type="submit" id="sendReplyBtn" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                        <i class="fas fa-paper-plane mr-2"></i>Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Ticket Summary -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Summary</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span id="sidebarStatus" class="inline-block mt-1"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Priority</label>
                                <span id="sidebarPriority" class="inline-block mt-1"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Category</label>
                                <span id="sidebarCategory" class="text-sm text-gray-900"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Created</label>
                                <span id="sidebarCreated" class="text-sm text-gray-900"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                <span id="sidebarUpdated" class="text-sm text-gray-900"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Related Order (if applicable) -->
                    <div id="relatedOrderSection" class="bg-white rounded-xl shadow-lg p-6 mb-6" style="display: none;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Order</h3>
                        <div id="relatedOrderContent">
                            <!-- Order details will be loaded here -->
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            <button onclick="window.print()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                <i class="fas fa-print mr-2"></i>Print Ticket
                            </button>
                            <button id="refreshBtn" onclick="loadTicketDetails()" class="w-full bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors font-medium">
                                <i class="fas fa-sync mr-2"></i>Refresh
                            </button>
                            <button id="emailUpdatesBtn" class="w-full bg-green-100 text-green-700 px-4 py-2 rounded-lg hover:bg-green-200 transition-colors font-medium">
                                <i class="fas fa-envelope mr-2"></i>Email Updates
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
$base_path = '../';
include '../components/footer.php'; 
?>

<script>
let currentTicket = null;
let ticketId = null;
let selectedAttachments = [];

document.addEventListener('DOMContentLoaded', function() {
    // Get ticket ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    ticketId = urlParams.get('id');
    
    if (!ticketId) {
        showTicketNotFound();
        return;
    }
    
    checkAuthenticationAndLoadTicket();
    setupEventListeners();
    startRealTimeUpdates();
    
    // Check for notifications immediately when page loads
    if (typeof updateNotificationCount === 'function') {
        setTimeout(() => {
            updateNotificationCount();
        }, 1000); // Wait 1 second for API to be ready
    }
});

async function checkAuthenticationAndLoadTicket() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success && response.customer) {
            // User is authenticated
            document.getElementById('authCheckLoader').style.display = 'none';
            await loadTicketDetails();
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

async function loadTicketDetails() {
    try {
        const response = await customerAPI.support.getTicket(ticketId);
        
        if (response.success && response.data) {
            currentTicket = response.data;
            renderTicketDetails();
            await loadTicketMessages();
            document.getElementById('ticketContent').style.display = 'block';
        } else {
            showTicketNotFound();
        }
    } catch (error) {
        console.error('Failed to load ticket:', error);
        showTicketNotFound();
    }
}

function renderTicketDetails() {
    if (!currentTicket) return;
    
    // Update header
    document.getElementById('ticketTitle').textContent = `Ticket #${currentTicket.ticket_number}`;
    document.getElementById('ticketSubtitle').textContent = currentTicket.subject;
    
    // Update main content
    document.getElementById('ticketSubject').textContent = currentTicket.subject;
    document.getElementById('ticketNumber').textContent = `#${currentTicket.ticket_number}`;
    document.getElementById('ticketCreated').textContent = `Created ${new Date(currentTicket.created_at).toLocaleDateString()}`;
    
    // Update status and priority
    const statusClass = getStatusClass(currentTicket.status);
    const priorityClass = getPriorityClass(currentTicket.priority);
    
    document.getElementById('ticketStatus').className = `px-3 py-1 text-sm font-medium rounded-full ${statusClass}`;
    document.getElementById('ticketStatus').textContent = currentTicket.status.replace('_', ' ');
    
    document.getElementById('ticketPriority').className = `px-3 py-1 text-sm font-medium rounded-full ${priorityClass}`;
    document.getElementById('ticketPriority').textContent = currentTicket.priority;
    
    // Update description
    document.getElementById('ticketDescription').innerHTML = `
        <div class="prose max-w-none">
            <p class="text-gray-700 leading-relaxed">${escapeHtml(currentTicket.description).replace(/\n/g, '<br>')}</p>
        </div>
    `;
    
    // Update sidebar
    document.getElementById('sidebarStatus').className = `px-2 py-1 text-xs font-medium rounded-full ${statusClass}`;
    document.getElementById('sidebarStatus').textContent = currentTicket.status.replace('_', ' ');
    
    document.getElementById('sidebarPriority').className = `px-2 py-1 text-xs font-medium rounded-full ${priorityClass}`;
    document.getElementById('sidebarPriority').textContent = currentTicket.priority;
    
    document.getElementById('sidebarCategory').textContent = currentTicket.category.replace('_', ' ');
    document.getElementById('sidebarCreated').textContent = new Date(currentTicket.created_at).toLocaleString();
    document.getElementById('sidebarUpdated').textContent = new Date(currentTicket.updated_at).toLocaleString();
    
    // Show reply section only if ticket is not closed
    if (currentTicket.status !== 'closed') {
        document.getElementById('replySection').style.display = 'block';
    }
    
    // Show related order if exists
    if (currentTicket.order_id) {
        showRelatedOrder(currentTicket.order_id);
    }
}

async function loadTicketMessages() {
    document.getElementById('messagesLoading').style.display = 'block';
    document.getElementById('messagesList').innerHTML = '';
    
    try {
        const response = await customerAPI.support.getTicketMessages(ticketId);
        
        if (response.success && response.data) {
            renderMessages(response.data.messages);
            document.getElementById('messagesLoading').style.display = 'none';
            
            // Initialize lastMessageCount for real-time updates
            lastMessageCount = response.data.messages.length;
            
            // Mark this ticket as viewed to update notifications
            await markTicketAsViewed(ticketId);
        } else {
            throw new Error(response.message || 'Failed to load messages');
        }
        
    } catch (error) {
        console.error('Failed to load messages:', error);
        document.getElementById('messagesLoading').style.display = 'none';
        document.getElementById('messagesList').innerHTML = `
            <div class="p-6 text-center text-red-600">
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <p>Failed to load conversation</p>
                <button onclick="loadTicketMessages()" class="mt-2 text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-sync mr-1"></i>Try Again
                </button>
            </div>
        `;
    }
}

function renderMessages(messages) {
    const messageCount = messages.length;
    document.getElementById('messageCount').textContent = `${messageCount} message${messageCount !== 1 ? 's' : ''}`;
    
    const messagesHTML = messages.map((message, index) => {
        const isCustomer = message.sender_type === 'customer';
        const isFirst = index === 0;
        
        return `
            <div class="p-6 ${index > 0 ? 'border-t border-gray-200' : ''}">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full ${isCustomer ? 'bg-blue-100' : 'bg-gray-100'} flex items-center justify-center">
                            <i class="fas ${isCustomer ? 'fa-user' : 'fa-headset'} text-sm ${isCustomer ? 'text-blue-600' : 'text-gray-600'}"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-2">
                            <h4 class="text-sm font-medium text-gray-900">${message.sender_name}</h4>
                            <span class="text-xs text-gray-500">${new Date(message.created_at).toLocaleString()}</span>
                            ${isFirst ? '<span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Initial Message</span>' : ''}
                        </div>
                        <div class="text-sm text-gray-700 leading-relaxed">
                            ${escapeHtml(message.message).replace(/\n/g, '<br>')}
                        </div>
                        ${message.attachments && message.attachments.length > 0 ? `
                            <div class="mt-3 space-y-2">
                                ${message.attachments.map(attachment => `
                                    <div class="flex items-center space-x-2 text-sm text-blue-600">
                                        <i class="fas fa-paperclip"></i>
                                        <a href="${attachment.url}" target="_blank" class="hover:underline">${attachment.name}</a>
                                        <span class="text-gray-500">(${formatFileSize(attachment.size)})</span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    document.getElementById('messagesList').innerHTML = messagesHTML;
}

function setupEventListeners() {
    // Reply form
    const replyForm = document.getElementById('replyForm');
    if (replyForm) {
        replyForm.addEventListener('submit', handleReplySubmit);
    }
    
    // File attachment
    const attachFileBtn = document.getElementById('attachFileBtn');
    const attachmentInput = document.getElementById('attachmentInput');
    
    if (attachFileBtn && attachmentInput) {
        attachFileBtn.addEventListener('click', () => {
            attachmentInput.click();
        });
        
        attachmentInput.addEventListener('change', handleFileSelection);
    }
    
    // Email updates toggle
    document.getElementById('emailUpdatesBtn').addEventListener('click', toggleEmailUpdates);
}

async function handleReplySubmit(e) {
    e.preventDefault();
    
    const replyMessage = document.getElementById('replyMessage').value.trim();
    if (!replyMessage) {
        showToast('Please enter a reply message', 'error');
        return;
    }
    
    const sendBtn = document.getElementById('sendReplyBtn');
    const originalText = sendBtn.innerHTML;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    sendBtn.disabled = true;
    
    try {
        const response = await customerAPI.support.sendReply(ticketId, replyMessage, selectedAttachments);
        
        if (response.success) {
            let successMessage = 'Reply sent successfully!';
            if (response.data && response.data.attachment_count > 0) {
                successMessage += ` (${response.data.attachment_count} attachment${response.data.attachment_count === 1 ? '' : 's'} uploaded)`;
            }
            showToast(successMessage, 'success');
            
            // Clear form
            document.getElementById('replyMessage').value = '';
            selectedAttachments = [];
            renderAttachmentsList();
            
            // Reload messages to show the new reply
            await loadTicketMessages();
            
            // Update ticket details if status changed
            if (response.data && response.data.ticket_status) {
                await loadTicketDetails();
            }
            
        } else {
            throw new Error(response.message || 'Failed to send reply');
        }
        
    } catch (error) {
        console.error('Failed to send reply:', error);
        showToast(error.message || 'Failed to send reply. Please try again.', 'error');
    } finally {
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
    }
}

function handleFileSelection(e) {
    const files = Array.from(e.target.files);
    
    files.forEach(file => {
        if (validateAttachment(file)) {
            selectedAttachments.push(file);
        }
    });
    
    renderAttachmentsList();
    e.target.value = ''; // Reset input
}

function validateAttachment(file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
    
    if (file.size > maxSize) {
        showToast(`File "${file.name}" is too large. Maximum size is 5MB.`, 'error');
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        showToast(`File "${file.name}" has unsupported format.`, 'error');
        return false;
    }
    
    return true;
}

function renderAttachmentsList() {
    const container = document.getElementById('attachmentsList');
    
    if (selectedAttachments.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    const attachmentsHTML = selectedAttachments.map((file, index) => `
        <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
            <div class="flex items-center space-x-2">
                <i class="fas fa-file text-gray-400"></i>
                <span class="text-sm text-gray-700">${file.name}</span>
                <span class="text-xs text-gray-500">(${formatFileSize(file.size)})</span>
            </div>
            <button type="button" onclick="removeAttachment(${index})" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');
    
    container.innerHTML = attachmentsHTML;
}

function removeAttachment(index) {
    selectedAttachments.splice(index, 1);
    renderAttachmentsList();
}

function showRelatedOrder(orderId) {
    // In a real implementation, you would fetch order details
    document.getElementById('relatedOrderSection').style.display = 'block';
    document.getElementById('relatedOrderContent').innerHTML = `
        <div class="text-sm">
            <p class="font-medium text-gray-900 mb-2">Order #ORD-${orderId}</p>
            <p class="text-gray-600 mb-2">Status: <span class="text-green-600 font-medium">Delivered</span></p>
            <a href="../account/orders.php?id=${orderId}" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-external-link-alt mr-1"></i>View Order Details
            </a>
        </div>
    `;
}

function toggleEmailUpdates() {
    // In a real implementation, this would update email preferences via API
    const btn = document.getElementById('emailUpdatesBtn');
    const currentText = btn.innerHTML;
    
    if (currentText.includes('Email Updates')) {
        btn.innerHTML = '<i class="fas fa-envelope-open mr-2"></i>Email Enabled';
        btn.className = 'w-full bg-green-100 text-green-700 px-4 py-2 rounded-lg hover:bg-green-200 transition-colors font-medium';
        showToast('Email updates enabled', 'success');
    } else {
        btn.innerHTML = '<i class="fas fa-envelope mr-2"></i>Email Updates';
        btn.className = 'w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium';
        showToast('Email updates disabled', 'info');
    }
}

function showTicketNotFound() {
    document.getElementById('authCheckLoader').style.display = 'none';
    document.getElementById('ticketNotFound').style.display = 'block';
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

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Toast notification function
function showToast(message, type = 'success') {
    // Check if the global toast element exists (from navbar)
    const globalToast = document.getElementById('toast');
    if (globalToast) {
        // Use the navbar's toast system
        const toast = globalToast;
        
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
            case 'warning':
                toast.classList.add('bg-yellow-500', 'text-white');
                break;
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

// Real-time updates
let pollingInterval;
let ticketNotificationPollingInterval;
let lastMessageCount = 0;

function startRealTimeUpdates() {
    // Poll every 5 seconds for new messages (more frequent for real-time feel)
    pollingInterval = setInterval(() => {
        if (ticketId && currentTicket && currentTicket.status !== 'closed') {
            checkForNewMessages().catch(error => {
                console.debug('Message polling error:', error);
            });
        }
    }, 5000);
    
    // Poll every 15 seconds for notification updates (less frequent than message updates)
    ticketNotificationPollingInterval = setInterval(() => {
        if (typeof updateNotificationCount === 'function') {
            try {
                updateNotificationCount();
            } catch (error) {
                console.debug('Notification polling error:', error);
            }
        }
    }, 15000);
}

function stopRealTimeUpdates() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
    if (ticketNotificationPollingInterval) {
        clearInterval(ticketNotificationPollingInterval);
    }
}

async function checkForNewMessages() {
    try {
        const response = await customerAPI.support.getTicketMessages(ticketId);
        
        if (response.success && response.data) {
            const messages = response.data.messages;
            const currentMessageCount = messages.length;
            
            // If there are new messages, reload the conversation
            if (currentMessageCount > lastMessageCount) {
                renderMessages(messages);
                
                // Show notification for new agent replies
                const newMessages = messages.slice(lastMessageCount);
                const agentReplies = newMessages.filter(msg => msg.sender_type === 'agent');
                
                if (agentReplies.length > 0) {
                    showToast(`You received ${agentReplies.length} new ${agentReplies.length === 1 ? 'reply' : 'replies'} from support!`, 'info');
                    
                    // Mark notifications as read since user is viewing the ticket
                    try {
                        await markTicketAsViewed(ticketId);
                    } catch (markError) {
                        console.debug('Failed to mark ticket as viewed:', markError);
                    }
                    
                    // Play a subtle notification sound (optional)
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('Support Reply Received', {
                            body: `New reply on ticket #${currentTicket.ticket_number}`,
                            icon: '/Core1_ecommerce/customer/images/logo1.png'
                        });
                    }
                }
            }
            
            lastMessageCount = currentMessageCount;
        }
    } catch (error) {
        console.debug('Failed to check for new messages:', error);
    }
}

async function markTicketAsViewed(ticketId) {
    try {
        // Mark support notifications as read in the new unified system
        try {
            await customerAPI.notifications.markAllAsRead(['support']);
        } catch (notifError) {
            console.debug('Failed to mark notifications as read:', notifError);
        }
        
        // Also update the old support system for backward compatibility
        try {
            await customerAPI.post('/support/notifications/mark-read');
        } catch (supportError) {
            console.debug('Failed to mark old support notifications as read:', supportError);
        }
        
        // Update notification count in navbar immediately
        if (typeof updateNotificationCount === 'function') {
            try {
                await updateNotificationCount();
            } catch (countError) {
                console.debug('Failed to update notification count:', countError);
            }
        }
    } catch (error) {
        console.debug('Failed to mark ticket as viewed:', error);
    }
}

// Request notification permission when page loads
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Stop polling when user leaves the page
window.addEventListener('beforeunload', stopRealTimeUpdates);
window.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        stopRealTimeUpdates();
    } else if (ticketId && currentTicket && currentTicket.status !== 'closed') {
        startRealTimeUpdates();
    }
});
</script>

</body>
</html>