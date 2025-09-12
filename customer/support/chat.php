<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat Support - Lumino</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .chat-container {
            height: 500px;
        }
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        .message-customer {
            background-color: #3B82F6;
            color: white;
        }
        .message-agent {
            background-color: #F3F4F6;
            color: #1F2937;
        }
        .typing-indicator {
            display: inline-block;
            position: relative;
        }
        .typing-indicator span {
            height: 8px;
            width: 8px;
            float: left;
            margin: 0 1px;
            background-color: #9CA3AF;
            display: block;
            border-radius: 50%;
            opacity: 0.4;
        }
        .typing-indicator span:nth-of-type(1) { animation: 1s blink infinite 0.3333s; }
        .typing-indicator span:nth-of-type(2) { animation: 1s blink infinite 0.6666s; }
        .typing-indicator span:nth-of-type(3) { animation: 1s blink infinite 0.9999s; }
        @keyframes blink {
            0%, 80%, 100% { opacity: 0.4; }
            40% { opacity: 1; }
        }
    </style>
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
                    <span class="text-gray-900">Live Chat</span>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">Live Chat Support</h1>
                <p class="text-gray-600 mt-2">Get instant help from our support team</p>
            </div>
            <div class="space-x-4">
                <a href="index.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Support
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Chat Interface -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Authentication Check -->
            <div id="authCheckLoader" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Connecting to chat...</p>
            </div>

            <!-- Login Required -->
            <div id="loginRequired" class="text-center py-12" style="display: none;">
                <div class="bg-white rounded-xl shadow-lg p-8 max-w-md mx-auto">
                    <i class="fas fa-lock text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Login Required</h3>
                    <p class="text-gray-600 mb-6">You need to be logged in to use live chat support.</p>
                    <a href="../login.php?redirect=support/chat.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login to Chat
                    </a>
                </div>
            </div>

            <!-- Chat Unavailable -->
            <div id="chatUnavailable" class="text-center py-12" style="display: none;">
                <div class="bg-white rounded-xl shadow-lg p-8 max-w-md mx-auto">
                    <i class="fas fa-clock text-4xl text-yellow-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Chat Currently Unavailable</h3>
                    <p class="text-gray-600 mb-6">Our support team is currently offline. Please try again during business hours or create a support ticket.</p>
                    <div class="space-y-3">
                        <div class="text-sm text-gray-600">
                            <div class="font-medium">Business Hours:</div>
                            <div>Mon-Fri: 9:00 AM - 6:00 PM</div>
                            <div>Saturday: 10:00 AM - 4:00 PM</div>
                        </div>
                        <a href="create-ticket.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-block">
                            <i class="fas fa-ticket-alt mr-2"></i>Create Support Ticket
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Chat Interface -->
            <div id="chatInterface" class="bg-white rounded-xl shadow-lg overflow-hidden" style="display: none;">
                <!-- Chat Header -->
                <div class="bg-blue-600 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-headset text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Support Team</h3>
                                <div class="flex items-center space-x-2">
                                    <div id="agentStatus" class="w-2 h-2 bg-green-400 rounded-full"></div>
                                    <span id="agentStatusText" class="text-sm opacity-90">Available</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div id="queuePosition" class="text-sm opacity-90" style="display: none;">
                                Position in queue: <span id="queueNumber">1</span>
                            </div>
                            <div id="estimatedWait" class="text-xs opacity-75" style="display: none;">
                                Est. wait: <span id="waitTime">2 min</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Welcome Message -->
                <div id="welcomeMessage" class="p-6 bg-blue-50 border-b border-blue-100">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-blue-800 font-medium mb-2">Welcome to Lumino Support!</p>
                            <p class="text-blue-700 text-sm">
                                Hi! I'm here to help you get connected with our support team. 
                                Please describe your issue and we'll have an agent assist you shortly.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div id="chatMessages" class="chat-container overflow-y-auto p-6 space-y-4">
                    <!-- Messages will be loaded here -->
                </div>

                <!-- Typing Indicator -->
                <div id="typingIndicator" class="px-6 py-2 border-t border-gray-100" style="display: none;">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-headset text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Agent is typing</span>
                            <div class="typing-indicator">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="border-t border-gray-200 p-6">
                    <form id="chatForm" class="space-y-4">
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <textarea id="messageInput" 
                                          rows="2" 
                                          placeholder="Type your message here..." 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          maxlength="1000"></textarea>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <button type="button" id="attachFileBtn" class="p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-paperclip text-gray-600"></i>
                                </button>
                                <button type="submit" id="sendMessageBtn" class="p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <div class="flex items-center space-x-4">
                                <span>Press Enter to send, Shift+Enter for new line</span>
                                <span id="charCount">0/1000</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" id="endChatBtn" class="text-red-600 hover:text-red-800 font-medium">
                                    <i class="fas fa-times mr-1"></i>End Chat
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt" class="hidden">
                </div>

                <!-- Quick Actions -->
                <div id="quickActions" class="px-6 pb-6">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="sendQuickMessage('I need help with my order')" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                            Order Help
                        </button>
                        <button onclick="sendQuickMessage('I have a question about a product')" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                            Product Question
                        </button>
                        <button onclick="sendQuickMessage('I need help with payment or billing')" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                            Payment Help
                        </button>
                        <button onclick="sendQuickMessage('I have a technical issue')" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                            Technical Issue
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chat Ended -->
            <div id="chatEnded" class="text-center py-12" style="display: none;">
                <div class="bg-white rounded-xl shadow-lg p-8 max-w-md mx-auto">
                    <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Chat Session Ended</h3>
                    <p class="text-gray-600 mb-6">Thank you for contacting our support team. We hope we were able to help you today!</p>
                    <div class="space-y-3">
                        <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                            <p class="font-medium mb-1">Need more help?</p>
                            <p>You can start a new chat session or create a support ticket for non-urgent matters.</p>
                        </div>
                        <div class="space-x-3">
                            <button onclick="startNewChat()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-comment mr-1"></i>New Chat
                            </button>
                            <a href="create-ticket.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors font-medium inline-block">
                                <i class="fas fa-ticket-alt mr-1"></i>Create Ticket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../components/footer.php'; ?>

<script>
let chatSession = null;
let messagesContainer = null;
let messageInput = null;
let isTyping = false;
let chatMessages = [];

document.addEventListener('DOMContentLoaded', function() {
    messagesContainer = document.getElementById('chatMessages');
    messageInput = document.getElementById('messageInput');
    
    checkAuthenticationAndInitializeChat();
    setupEventListeners();
});

async function checkAuthenticationAndInitializeChat() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success && response.customer) {
            // User is authenticated
            document.getElementById('authCheckLoader').style.display = 'none';
            
            // Check if chat is available (simulate business hours check)
            const now = new Date();
            const hour = now.getHours();
            const day = now.getDay();
            
            // Simulate business hours: Mon-Fri 9-18, Sat 10-16
            const isBusinessHours = (day >= 1 && day <= 5 && hour >= 9 && hour < 18) || 
                                   (day === 6 && hour >= 10 && hour < 16);
            
            if (isBusinessHours || true) { // Always show for demo
                await initializeChat();
                document.getElementById('chatInterface').style.display = 'block';
            } else {
                document.getElementById('chatUnavailable').style.display = 'block';
            }
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

async function initializeChat() {
    try {
        // In a real implementation, this would create a chat session via API
        // const response = await customerAPI.support.createChatRoom();
        
        // Simulate chat room creation
        chatSession = {
            id: 'chat_' + Date.now(),
            status: 'active',
            agent: null,
            created_at: new Date().toISOString()
        };
        
        // Simulate joining queue
        showQueueStatus();
        
        // Simulate agent assignment after a delay
        setTimeout(() => {
            assignAgent();
        }, 3000);
        
    } catch (error) {
        console.error('Failed to initialize chat:', error);
        showToast('Failed to connect to chat. Please try again.', 'error');
    }
}

function setupEventListeners() {
    const chatForm = document.getElementById('chatForm');
    const attachFileBtn = document.getElementById('attachFileBtn');
    const fileInput = document.getElementById('fileInput');
    const endChatBtn = document.getElementById('endChatBtn');
    
    // Chat form submission
    chatForm?.addEventListener('submit', handleMessageSubmit);
    
    // Message input events
    messageInput?.addEventListener('input', handleMessageInput);
    messageInput?.addEventListener('keydown', handleKeyDown);
    
    // File attachment
    attachFileBtn?.addEventListener('click', () => fileInput?.click());
    fileInput?.addEventListener('change', handleFileSelection);
    
    // End chat
    endChatBtn?.addEventListener('click', endChat);
}

function handleMessageSubmit(e) {
    e.preventDefault();
    
    const message = messageInput.value.trim();
    if (!message) return;
    
    sendMessage(message);
    messageInput.value = '';
    updateCharCount();
}

function handleMessageInput(e) {
    updateCharCount();
    
    // Show typing indicator to agent (in real implementation)
    if (!isTyping) {
        isTyping = true;
        // Send typing start event
        setTimeout(() => {
            isTyping = false;
            // Send typing stop event
        }, 3000);
    }
}

function handleKeyDown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('chatForm').dispatchEvent(new Event('submit'));
    }
}

function handleFileSelection(e) {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        if (validateFile(file)) {
            sendFileMessage(file);
        }
    });
    e.target.value = '';
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

function updateCharCount() {
    const count = messageInput?.value.length || 0;
    document.getElementById('charCount').textContent = `${count}/1000`;
}

function showQueueStatus() {
    document.getElementById('queuePosition').style.display = 'block';
    document.getElementById('estimatedWait').style.display = 'block';
    document.getElementById('agentStatusText').textContent = 'Connecting...';
    
    // Simulate queue updates
    let position = 3;
    const queueInterval = setInterval(() => {
        if (position > 1) {
            position--;
            document.getElementById('queueNumber').textContent = position;
            document.getElementById('waitTime').textContent = `${position * 30} sec`;
        } else {
            clearInterval(queueInterval);
            document.getElementById('queuePosition').style.display = 'none';
            document.getElementById('estimatedWait').style.display = 'none';
        }
    }, 1000);
}

function assignAgent() {
    document.getElementById('agentStatusText').textContent = 'Agent Connected';
    document.getElementById('agentStatus').className = 'w-2 h-2 bg-green-400 rounded-full';
    
    // Add agent introduction message
    addMessage({
        sender: 'agent',
        name: 'Support Agent',
        message: 'Hello! I\'m here to help you today. How can I assist you?',
        timestamp: new Date().toISOString()
    });
    
    // Hide quick actions after agent connects
    setTimeout(() => {
        document.getElementById('quickActions').style.display = 'none';
    }, 5000);
}

function sendMessage(message) {
    // Add customer message
    addMessage({
        sender: 'customer',
        name: 'You',
        message: message,
        timestamp: new Date().toISOString()
    });
    
    // Simulate agent response after delay
    setTimeout(() => {
        simulateAgentResponse(message);
    }, 2000);
}

function sendFileMessage(file) {
    addMessage({
        sender: 'customer',
        name: 'You',
        message: '',
        file: {
            name: file.name,
            size: file.size,
            type: file.type
        },
        timestamp: new Date().toISOString()
    });
    
    showToast('File uploaded successfully', 'success');
}

function sendQuickMessage(message) {
    messageInput.value = message;
    messageInput.focus();
}

function addMessage(messageData) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex ${messageData.sender === 'customer' ? 'justify-end' : 'justify-start'}`;
    
    const isCustomer = messageData.sender === 'customer';
    
    messageDiv.innerHTML = `
        <div class="flex items-end space-x-2 ${isCustomer ? 'flex-row-reverse space-x-reverse' : ''}">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${isCustomer ? 'bg-blue-100' : 'bg-gray-100'}">
                <i class="fas ${isCustomer ? 'fa-user' : 'fa-headset'} text-sm ${isCustomer ? 'text-blue-600' : 'text-gray-600'}"></i>
            </div>
            <div class="message-bubble ${isCustomer ? 'message-customer' : 'message-agent'} px-4 py-2 rounded-lg">
                <div class="text-xs opacity-75 mb-1">${messageData.name}</div>
                ${messageData.message ? `<div class="text-sm">${escapeHtml(messageData.message).replace(/\n/g, '<br>')}</div>` : ''}
                ${messageData.file ? `
                    <div class="flex items-center space-x-2 text-sm">
                        <i class="fas fa-paperclip"></i>
                        <span>${messageData.file.name}</span>
                        <span class="opacity-75">(${formatFileSize(messageData.file.size)})</span>
                    </div>
                ` : ''}
                <div class="text-xs opacity-75 mt-1">${new Date(messageData.timestamp).toLocaleTimeString()}</div>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    chatMessages.push(messageData);
}

function simulateAgentResponse(customerMessage) {
    // Show typing indicator
    document.getElementById('typingIndicator').style.display = 'block';
    
    setTimeout(() => {
        document.getElementById('typingIndicator').style.display = 'none';
        
        // Generate appropriate response based on customer message
        let response = "Thank you for contacting us. I understand your concern and I'm here to help you resolve this issue.";
        
        if (customerMessage.toLowerCase().includes('order')) {
            response = "I can help you with your order. Could you please provide me with your order number so I can look up the details for you?";
        } else if (customerMessage.toLowerCase().includes('product')) {
            response = "I'd be happy to help you with product information. Which specific product are you interested in or having issues with?";
        } else if (customerMessage.toLowerCase().includes('payment') || customerMessage.toLowerCase().includes('billing')) {
            response = "I can assist you with payment and billing questions. For security purposes, I may need to verify some account information. What specific payment issue are you experiencing?";
        } else if (customerMessage.toLowerCase().includes('technical')) {
            response = "I'm here to help with technical issues. Can you describe the problem you're experiencing in more detail? Any error messages you're seeing?";
        }
        
        addMessage({
            sender: 'agent',
            name: 'Support Agent',
            message: response,
            timestamp: new Date().toISOString()
        });
    }, 3000);
}

function endChat() {
    if (confirm('Are you sure you want to end this chat session?')) {
        document.getElementById('chatInterface').style.display = 'none';
        document.getElementById('chatEnded').style.display = 'block';
        
        // In real implementation, notify server about chat end
        chatSession.status = 'ended';
    }
}

function startNewChat() {
    // Reset chat state
    chatMessages = [];
    messagesContainer.innerHTML = '';
    document.getElementById('chatEnded').style.display = 'none';
    document.getElementById('quickActions').style.display = 'block';
    
    // Reinitialize chat
    initializeChat();
    document.getElementById('chatInterface').style.display = 'block';
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