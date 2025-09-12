<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - Lumino</title>
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

<!-- Support Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">How can we help you?</h1>
            <p class="text-xl mb-8">Find answers, get support, or contact our team</p>
            
            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" id="supportSearch" placeholder="Search for help articles, FAQs..." 
                           class="w-full px-6 py-4 pl-12 text-gray-900 text-lg rounded-full border-0 focus:ring-4 focus:ring-white/20 focus:outline-none">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Actions -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Create New Ticket -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-6">
                        <i class="fas fa-ticket-alt text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Create Support Ticket</h3>
                    <p class="text-gray-600 mb-6">Need help with an order or product? Submit a support ticket and our team will get back to you.</p>
                    <button id="createTicketBtn" class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-plus mr-2"></i>Create Ticket
                    </button>
                </div>
            </div>

            <!-- My Tickets -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-6">
                        <i class="fas fa-list-alt text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">My Tickets</h3>
                    <p class="text-gray-600 mb-6">View and track all your support tickets in one place. Check status and replies.</p>
                    <button id="viewTicketsBtn" class="bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-eye mr-2"></i>View Tickets
                    </button>
                </div>
            </div>

            <!-- Live Chat -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-6">
                        <i class="fas fa-comments text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Live Chat</h3>
                    <p class="text-gray-600 mb-6">Get instant help from our support team through live chat. Available during business hours.</p>
                    <button id="liveChatBtn" class="bg-purple-600 text-white px-6 py-3 rounded-full hover:bg-purple-700 transition-colors font-medium">
                        <i class="fas fa-comment mr-2"></i>Start Chat
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Tickets Section (if logged in) -->
<section id="recentTicketsSection" class="py-12 bg-white" style="display: none;">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Your Recent Tickets</h2>
        <div id="recentTicketsContainer" class="max-w-4xl mx-auto">
            <!-- Recent tickets will be loaded here -->
        </div>
        <div class="text-center mt-8">
            <button id="viewAllTicketsBtn" class="bg-blue-600 text-white px-8 py-3 rounded-full hover:bg-blue-700 transition-colors font-medium">
                View All Tickets
            </button>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Frequently Asked Questions</h2>
        <div class="max-w-4xl mx-auto">
            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-lg shadow-sm">
                    <button class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 hover:bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                        <span>How do I track my order?</span>
                        <i class="fas fa-chevron-down transform transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4">
                        <p class="text-gray-600">You can track your order by logging into your account and visiting the "My Orders" section. You'll find tracking information and estimated delivery dates there.</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-lg shadow-sm">
                    <button class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 hover:bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                        <span>What is your return policy?</span>
                        <i class="fas fa-chevron-down transform transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4">
                        <p class="text-gray-600">We offer a 30-day return policy for most items. Items must be in original condition with tags attached. Please contact support to initiate a return.</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-lg shadow-sm">
                    <button class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 hover:bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                        <span>How can I change my shipping address?</span>
                        <i class="fas fa-chevron-down transform transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4">
                        <p class="text-gray-600">If your order hasn't shipped yet, you can change the shipping address in your account or by contacting our support team immediately.</p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-lg shadow-sm">
                    <button class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 hover:bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down transform transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4">
                        <p class="text-gray-600">We accept all major credit cards, PayMongo, GCash, and other digital wallet payments for your convenience.</p>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="bg-white rounded-lg shadow-sm">
                    <button class="faq-question w-full px-6 py-4 text-left font-medium text-gray-900 hover:bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                        <span>How long does shipping take?</span>
                        <i class="fas fa-chevron-down transform transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4">
                        <p class="text-gray-600">Standard shipping typically takes 3-7 business days. Express shipping options are available for faster delivery.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Get In Touch</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Email -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="fas fa-envelope text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Email Support</h3>
                    <p class="text-gray-600 mb-4">Send us an email and we'll respond within 24 hours</p>
                    <a href="mailto:support@lumino.com" class="text-blue-600 hover:text-blue-800 font-medium">support@lumino.com</a>
                </div>

                <!-- Phone -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-phone text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Phone Support</h3>
                    <p class="text-gray-600 mb-4">Call us during business hours for immediate assistance</p>
                    <a href="tel:+639123456789" class="text-green-600 hover:text-green-800 font-medium">+63 912 345 6789</a>
                </div>

                <!-- Hours -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <i class="fas fa-clock text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Business Hours</h3>
                    <p class="text-gray-600 mb-4">We're here to help during these hours</p>
                    <div class="text-purple-600 font-medium">
                        <div>Mon-Fri: 9:00 AM - 6:00 PM</div>
                        <div>Sat: 10:00 AM - 4:00 PM</div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in and load recent tickets
    checkAuthAndLoadData();
    
    // FAQ functionality
    setupFAQs();
    
    // Button event listeners
    document.getElementById('createTicketBtn').addEventListener('click', function() {
        window.location.href = 'create-ticket.php';
    });
    
    document.getElementById('viewTicketsBtn').addEventListener('click', function() {
        window.location.href = 'my-tickets.php';
    });
    
    document.getElementById('liveChatBtn').addEventListener('click', function() {
        window.location.href = 'chat.php';
    });
    
    const viewAllTicketsBtn = document.getElementById('viewAllTicketsBtn');
    if (viewAllTicketsBtn) {
        viewAllTicketsBtn.addEventListener('click', function() {
            window.location.href = 'my-tickets.php';
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('supportSearch');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch(this.value);
        }
    });
});

async function checkAuthAndLoadData() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success) {
            // User is logged in, load recent tickets
            await loadRecentTickets();
            document.getElementById('recentTicketsSection').style.display = 'block';
        }
    } catch (error) {
        // User not logged in, hide recent tickets section
        document.getElementById('recentTicketsSection').style.display = 'none';
    }
}

async function loadRecentTickets() {
    try {
        const response = await customerAPI.support.getTickets();
        if (response.success && response.data.length > 0) {
            const recentTickets = response.data.slice(0, 3); // Show only 3 recent tickets
            renderRecentTickets(recentTickets);
        } else {
            document.getElementById('recentTicketsContainer').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-500 mb-2">No tickets yet</h3>
                    <p class="text-gray-400">Create your first support ticket to get help.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Failed to load recent tickets:', error);
    }
}

function renderRecentTickets(tickets) {
    const container = document.getElementById('recentTicketsContainer');
    const ticketsHTML = tickets.map(ticket => {
        const statusClass = getStatusClass(ticket.status);
        const priorityClass = getPriorityClass(ticket.priority);
        
        return `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4 mb-3">
                            <h3 class="text-lg font-semibold text-gray-900">${escapeHtml(ticket.subject)}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${ticket.status.replace('_', ' ')}</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${priorityClass}">${ticket.priority}</span>
                        </div>
                        <p class="text-gray-600 mb-3">#${ticket.ticket_number}</p>
                        <p class="text-sm text-gray-500">
                            Category: ${ticket.category} • Created: ${new Date(ticket.created_at).toLocaleDateString()}
                        </p>
                    </div>
                    <div class="ml-4">
                        <button onclick="viewTicket('${ticket.id}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    container.innerHTML = ticketsHTML;
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

function viewTicket(ticketId) {
    window.location.href = `ticket-detail.php?id=${ticketId}`;
}

function setupFAQs() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.parentNode.querySelector('.faq-answer');
            const icon = this.querySelector('.fa-chevron-down');
            
            // Close all other FAQs
            faqQuestions.forEach(otherQuestion => {
                if (otherQuestion !== this) {
                    const otherAnswer = otherQuestion.parentNode.querySelector('.faq-answer');
                    const otherIcon = otherQuestion.querySelector('.fa-chevron-down');
                    otherAnswer.classList.add('hidden');
                    otherIcon.classList.remove('rotate-180');
                }
            });
            
            // Toggle current FAQ
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                answer.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        });
    });
}

function performSearch(query) {
    if (!query.trim()) return;
    
    // In a real application, this would search through help articles/FAQs
    showToast(`Searching for "${query}"... Feature coming soon!`, 'info');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Toast notification function (reuse from navbar)
function showToast(message, type = 'success') {
    // Use the showToast function from the global scope if available
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
        return;
    }
    
    // Fallback toast implementation
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