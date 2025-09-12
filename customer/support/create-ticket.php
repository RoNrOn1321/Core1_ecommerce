<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Support Ticket - Lumino</title>
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
                    <span class="text-gray-900">Create Ticket</span>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">Create Support Ticket</h1>
                <p class="text-gray-600 mt-2">Tell us about your issue and we'll help you resolve it</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-blue-100 p-6 rounded-lg">
                    <i class="fas fa-headset text-3xl text-blue-600 mb-2"></i>
                    <p class="text-sm text-blue-800 font-medium">Need immediate help?</p>
                    <p class="text-xs text-blue-600">Try our live chat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Create Ticket Form -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <!-- Authentication Check -->
                    <div id="authCheckLoader" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Checking authentication...</p>
                    </div>

                    <!-- Login Required Message -->
                    <div id="loginRequired" class="text-center py-12" style="display: none;">
                        <i class="fas fa-lock text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Login Required</h3>
                        <p class="text-gray-600 mb-6">You need to be logged in to create a support ticket.</p>
                        <div class="space-x-4">
                            <a href="../login.php?redirect=support/create-ticket.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-block">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="../register.php?redirect=support/create-ticket.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium inline-block">
                                <i class="fas fa-user-plus mr-2"></i>Sign Up
                            </a>
                        </div>
                    </div>

                    <!-- Ticket Form -->
                    <form id="createTicketForm" style="display: none;">
                        <!-- Personal Information (Pre-filled) -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="customerName" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="customerEmail" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Order Information (Optional) -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information (Optional)</h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Number</label>
                                    <select id="orderSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select an order (optional)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Or Enter Order Number</label>
                                    <input type="text" id="orderNumber" placeholder="e.g., ORD-123456" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Details</h3>
                            <div class="space-y-6">
                                <!-- Category -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                    <select id="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select a category</option>
                                        <option value="order">Order Issues</option>
                                        <option value="product">Product Questions</option>
                                        <option value="payment">Payment & Billing</option>
                                        <option value="shipping">Shipping & Delivery</option>
                                        <option value="technical">Technical Support</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Priority -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                                    <select id="priority" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="low">Low - General inquiry</option>
                                        <option value="medium" selected>Medium - Standard issue</option>
                                        <option value="high">High - Urgent matter</option>
                                        <option value="urgent">Urgent - Critical issue</option>
                                    </select>
                                    <p class="text-sm text-gray-500 mt-1">Select the appropriate priority level for your issue</p>
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                                    <input type="text" id="subject" required maxlength="200" placeholder="Brief description of your issue" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-sm text-gray-500 mt-1">Maximum 200 characters</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                    <textarea id="description" required rows="6" placeholder="Please provide detailed information about your issue. Include steps you've already tried, error messages, and any relevant details." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                                    <p class="text-sm text-gray-500 mt-1">Be as specific as possible to help us resolve your issue quickly</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attachments (Optional)</h3>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                                <div class="text-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-600 mb-2">Drop files here or <button type="button" id="browseFiles" class="text-blue-600 hover:text-blue-800 font-medium">browse</button></p>
                                    <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt" class="hidden">
                                    <p class="text-sm text-gray-500">Supported formats: JPG, PNG, GIF, PDF, DOC, DOCX, TXT (Max 5MB each)</p>
                                </div>
                                <div id="fileList" class="mt-4 space-y-2"></div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="index.php" class="text-gray-600 hover:text-gray-800 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Support
                            </a>
                            <div class="space-x-4">
                                <button type="button" id="saveDraftBtn" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                    <i class="fas fa-save mr-2"></i>Save Draft
                                </button>
                                <button type="submit" id="submitTicketBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                    <i class="fas fa-paper-plane mr-2"></i>Submit Ticket
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-12" style="display: none;">
                        <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Creating Ticket...</h3>
                        <p class="text-gray-600">Please wait while we submit your support ticket.</p>
                    </div>

                    <!-- Success State -->
                    <div id="successState" class="text-center py-12" style="display: none;">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Ticket Created Successfully!</h3>
                        <p class="text-gray-600 mb-4">Your support ticket has been submitted. We'll get back to you soon.</p>
                        <div id="ticketInfo" class="bg-green-50 p-4 rounded-lg mb-6">
                            <p class="font-medium text-green-800">Ticket Number: <span id="ticketNumber"></span></p>
                            <p class="text-sm text-green-600">Please save this number for your records.</p>
                        </div>
                        <div class="space-x-4">
                            <a href="my-tickets.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-block">
                                <i class="fas fa-list-alt mr-2"></i>View My Tickets
                            </a>
                            <a href="index.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium inline-block">
                                <i class="fas fa-home mr-2"></i>Back to Support
                            </a>
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
let selectedFiles = [];
let customerOrders = [];

document.addEventListener('DOMContentLoaded', function() {
    checkAuthentication();
    setupFileUpload();
    setupFormValidation();
});

async function checkAuthentication() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success && response.customer) {
            // User is authenticated, show form
            document.getElementById('authCheckLoader').style.display = 'none';
            document.getElementById('createTicketForm').style.display = 'block';
            
            // Pre-fill customer information
            document.getElementById('customerName').value = response.customer.first_name + ' ' + response.customer.last_name;
            document.getElementById('customerEmail').value = response.customer.email;
            
            // Load customer orders for dropdown
            await loadCustomerOrders();
            
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

async function loadCustomerOrders() {
    try {
        // This would typically call an orders API endpoint
        // For now, we'll simulate with an empty array
        const orderSelect = document.getElementById('orderSelect');
        
        // In a real implementation, you would fetch orders like this:
        // const response = await customerAPI.orders.getList({ limit: 20 });
        // if (response.success) {
        //     response.data.forEach(order => {
        //         const option = document.createElement('option');
        //         option.value = order.id;
        //         option.textContent = `${order.order_number} - ₱${order.total_amount} (${order.status})`;
        //         orderSelect.appendChild(option);
        //     });
        // }
        
        // Placeholder for demonstration
        const sampleOrders = [
            { id: 1, order_number: 'ORD-123456', total_amount: 2500.00, status: 'delivered' },
            { id: 2, order_number: 'ORD-123457', total_amount: 1800.00, status: 'shipped' },
            { id: 3, order_number: 'ORD-123458', total_amount: 3200.00, status: 'processing' }
        ];
        
        sampleOrders.forEach(order => {
            const option = document.createElement('option');
            option.value = order.id;
            option.textContent = `${order.order_number} - ₱${order.total_amount.toFixed(2)} (${order.status})`;
            orderSelect.appendChild(option);
        });
        
    } catch (error) {
        console.error('Failed to load orders:', error);
    }
}

function setupFileUpload() {
    const fileInput = document.getElementById('fileInput');
    const browseFiles = document.getElementById('browseFiles');
    const fileList = document.getElementById('fileList');
    
    browseFiles.addEventListener('click', () => {
        fileInput.click();
    });
    
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (validateFile(file)) {
                selectedFiles.push(file);
                renderFileList();
            }
        });
        fileInput.value = ''; // Reset input
    });
    
    // Drag and drop
    const dropZone = fileInput.parentElement.parentElement;
    
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    });
    
    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    });
    
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        
        const files = Array.from(e.dataTransfer.files);
        files.forEach(file => {
            if (validateFile(file)) {
                selectedFiles.push(file);
            }
        });
        renderFileList();
    });
}

function validateFile(file) {
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

function renderFileList() {
    const fileList = document.getElementById('fileList');
    
    if (selectedFiles.length === 0) {
        fileList.innerHTML = '';
        return;
    }
    
    const filesHTML = selectedFiles.map((file, index) => `
        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
            <div class="flex items-center space-x-3">
                <i class="fas fa-file text-gray-400"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                </div>
            </div>
            <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');
    
    fileList.innerHTML = filesHTML;
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    renderFileList();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function setupFormValidation() {
    const form = document.getElementById('createTicketForm');
    const submitBtn = document.getElementById('submitTicketBtn');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        await submitTicket();
    });
    
    // Save draft functionality
    document.getElementById('saveDraftBtn').addEventListener('click', () => {
        saveDraft();
    });
}

function validateForm() {
    const category = document.getElementById('category').value;
    const priority = document.getElementById('priority').value;
    const subject = document.getElementById('subject').value.trim();
    const description = document.getElementById('description').value.trim();
    
    if (!category) {
        showToast('Please select a category', 'error');
        return false;
    }
    
    if (!priority) {
        showToast('Please select a priority level', 'error');
        return false;
    }
    
    if (!subject) {
        showToast('Please enter a subject', 'error');
        return false;
    }
    
    if (subject.length > 200) {
        showToast('Subject must be 200 characters or less', 'error');
        return false;
    }
    
    if (!description) {
        showToast('Please provide a description', 'error');
        return false;
    }
    
    return true;
}

async function submitTicket() {
    // Show loading state
    document.getElementById('createTicketForm').style.display = 'none';
    document.getElementById('loadingState').style.display = 'block';
    
    try {
        // Prepare ticket data
        const orderSelect = document.getElementById('orderSelect');
        const orderNumber = document.getElementById('orderNumber').value.trim();
        
        const ticketData = {
            category: document.getElementById('category').value,
            priority: document.getElementById('priority').value,
            subject: document.getElementById('subject').value.trim(),
            description: document.getElementById('description').value.trim(),
            order_id: orderSelect.value || null,
            order_number: orderNumber || null
        };
        
        // Submit ticket via API
        const response = await customerAPI.support.createTicket(ticketData);
        
        if (response.success) {
            // Show success state
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('successState').style.display = 'block';
            document.getElementById('ticketNumber').textContent = response.data.ticket_number;
            
            // Clear form data from localStorage
            localStorage.removeItem('ticketDraft');
            
        } else {
            throw new Error(response.message || 'Failed to create ticket');
        }
        
    } catch (error) {
        console.error('Failed to create ticket:', error);
        
        // Hide loading, show form again
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('createTicketForm').style.display = 'block';
        
        showToast(error.message || 'Failed to create ticket. Please try again.', 'error');
    }
}

function saveDraft() {
    const draftData = {
        category: document.getElementById('category').value,
        priority: document.getElementById('priority').value,
        subject: document.getElementById('subject').value,
        description: document.getElementById('description').value,
        orderSelect: document.getElementById('orderSelect').value,
        orderNumber: document.getElementById('orderNumber').value,
        timestamp: new Date().toISOString()
    };
    
    localStorage.setItem('ticketDraft', JSON.stringify(draftData));
    showToast('Draft saved successfully', 'success');
}

function loadDraft() {
    try {
        const draft = localStorage.getItem('ticketDraft');
        if (draft) {
            const draftData = JSON.parse(draft);
            
            document.getElementById('category').value = draftData.category || '';
            document.getElementById('priority').value = draftData.priority || 'medium';
            document.getElementById('subject').value = draftData.subject || '';
            document.getElementById('description').value = draftData.description || '';
            document.getElementById('orderSelect').value = draftData.orderSelect || '';
            document.getElementById('orderNumber').value = draftData.orderNumber || '';
            
            showToast('Draft loaded successfully', 'info');
        }
    } catch (error) {
        console.error('Failed to load draft:', error);
    }
}

// Load draft when form is shown
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(loadDraft, 1000); // Delay to ensure form is ready
});

// Toast notification function
function showToast(message, type = 'success') {
    // Check if the global toast element exists (from navbar)
    const globalToast = document.getElementById('toast');
    if (globalToast) {
        // Use the global toast functionality from navbar
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
    } else {
        // Fallback implementation
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
}
</script>

</body>
</html>