<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_support')) {
    header('Location: dashboard.php');
    exit();
}

$success_message = '';
$error_message = '';

// Get active chat sessions
try {
    $stmt = $pdo->query("
        SELECT cs.*, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email,
               u.id as customer_id,
               (SELECT COUNT(*) FROM chat_messages WHERE session_id = cs.id AND sender_type = 'customer') as unread_count,
               (SELECT message FROM chat_messages WHERE session_id = cs.id ORDER BY created_at DESC LIMIT 1) as last_message,
               (SELECT created_at FROM chat_messages WHERE session_id = cs.id ORDER BY created_at DESC LIMIT 1) as last_message_time
        FROM chat_sessions cs
        LEFT JOIN users u ON cs.user_id = u.id
        WHERE cs.status IN ('waiting', 'active')
        ORDER BY 
            CASE WHEN cs.status = 'waiting' THEN 1 ELSE 2 END,
            cs.started_at ASC
    ");
    $chat_sessions = $stmt->fetchAll();
    
    // Get chat session statistics
    $stats_stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_sessions,
            SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting_sessions,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_sessions,
            SUM(CASE WHEN DATE(started_at) = CURDATE() THEN 1 ELSE 0 END) as today_sessions
        FROM chat_sessions
    ");
    $stats = $stats_stmt->fetch();
    
} catch (PDOException $e) {
    $error_message = 'Error fetching chat sessions.';
    $chat_sessions = [];
    $stats = [];
}

// Page-specific variables
$page_title = 'Live Chat Management';
$page_description = 'Manage real-time customer chat sessions';

// Include layout start
include 'includes/layout_start.php';
?>

<div class="row align-items-center mb-2">
    <div class="col">
        <h2 class="h5 page-title">Live Chat Management</h2>
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-primary" onclick="refreshSessions()">
            <i class="fe fe-refresh-cw fe-12 mr-2"></i>Refresh
        </button>
        <a href="support.php" class="btn btn-outline-primary">
            <i class="fe fe-headphones fe-12 mr-2"></i>Support Tickets
        </a>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($success_message); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Chat Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Waiting</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo number_format($stats['waiting_sessions'] ?? 0); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fe fe-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Chats</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo number_format($stats['active_sessions'] ?? 0); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fe fe-message-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Today's Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo number_format($stats['today_sessions'] ?? 0); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fe fe-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo number_format($stats['total_sessions'] ?? 0); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fe fe-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Chat Interface -->
<div class="row">
    <!-- Chat Sessions List -->
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    Chat Sessions
                    <?php if (!empty($chat_sessions)): ?>
                        <span class="badge badge-primary ml-2"><?php echo count($chat_sessions); ?></span>
                    <?php endif; ?>
                </h6>
            </div>
            <div class="card-body p-0">
                <div id="chatSessionsList" style="max-height: 600px; overflow-y: auto;">
                    <?php if (empty($chat_sessions)): ?>
                        <div class="text-center py-5">
                            <i class="fe fe-message-circle fe-48 text-muted mb-3"></i>
                            <h6 class="text-muted">No Active Chat Sessions</h6>
                            <p class="text-muted small">Waiting for customers to start a chat...</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($chat_sessions as $session): ?>
                            <div class="chat-session-item p-3 border-bottom cursor-pointer" 
                                 data-session-id="<?php echo $session['id']; ?>"
                                 onclick="loadChatSession(<?php echo $session['id']; ?>)">
                                <div class="d-flex align-items-center">
                                    <div class="avatar mr-3">
                                        <div class="w-40 h-40 rounded-circle bg-primary d-flex align-items-center justify-content-center">
                                            <i class="fe fe-user text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($session['customer_name']); ?></h6>
                                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($session['customer_email']); ?></p>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-<?php echo $session['status'] === 'waiting' ? 'warning' : 'success'; ?>">
                                                    <?php echo ucfirst($session['status']); ?>
                                                </span>
                                                <?php if ($session['unread_count'] > 0): ?>
                                                    <span class="badge badge-danger ml-1"><?php echo $session['unread_count']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if ($session['last_message']): ?>
                                            <p class="text-muted small mb-1 text-truncate">
                                                <?php echo htmlspecialchars(substr($session['last_message'], 0, 50)) . (strlen($session['last_message']) > 50 ? '...' : ''); ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-muted small mb-0">
                                            <?php 
                                            $time = $session['last_message_time'] ?: $session['started_at'];
                                            echo 'Started: ' . date('M d, h:i A', strtotime($time)); 
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Messages Area -->
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <div id="chatHeader" class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Select a chat session</h6>
                    <div id="chatActions" style="display: none;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="endChatSession()">
                            <i class="fe fe-x"></i> End Chat
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Default State -->
                <div id="noChatSelected" class="text-center py-5">
                    <i class="fe fe-message-square fe-48 text-muted mb-3"></i>
                    <h6 class="text-muted">No Chat Selected</h6>
                    <p class="text-muted">Select a chat session from the left to start chatting with customers.</p>
                </div>

                <!-- Chat Messages Container -->
                <div id="chatMessagesContainer" style="display: none;">
                    <div id="chatMessages" style="height: 400px; overflow-y: auto; padding: 1rem;">
                        <!-- Messages will be loaded here -->
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="border-top p-3">
                        <form id="chatForm" onsubmit="sendMessage(event)">
                            <div class="input-group">
                                <textarea id="messageInput" 
                                          class="form-control" 
                                          rows="2" 
                                          placeholder="Type your message..."
                                          maxlength="1000"></textarea>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-send"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-right mt-2">
                                <small class="text-muted">
                                    <span id="charCount">0/1000</span> • Press Enter to send
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-session-item {
    transition: background-color 0.2s;
}
.chat-session-item:hover {
    background-color: #f8f9fa;
}
.chat-session-item.active {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}
.cursor-pointer {
    cursor: pointer;
}
.w-40 {
    width: 40px;
}
.h-40 {
    height: 40px;
}
.message-bubble {
    max-width: 70%;
    word-wrap: break-word;
}
.message-customer {
    background-color: #f1f3f4;
    border: 1px solid #e0e0e0;
}
.message-agent {
    background-color: #1976d2;
    color: white;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>

<script>
let currentSessionId = null;
let messagePollingInterval = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh sessions every 30 seconds
    setInterval(refreshSessions, 30000);
    
    // Setup message input character count
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('input', updateCharCount);
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(e);
            }
        });
    }
});

function refreshSessions() {
    fetch('api/chat_sessions.php', {
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateSessionsList(data.sessions);
            } else {
                console.error('Error refreshing sessions:', data.error);
            }
        })
        .catch(error => {
            console.error('Error refreshing sessions:', error);
        });
}

function updateSessionsList(sessions) {
    const container = document.getElementById('chatSessionsList');
    const currentActive = document.querySelector('.chat-session-item.active');
    const currentActiveId = currentActive ? currentActive.dataset.sessionId : null;
    
    if (sessions.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fe fe-message-circle fe-48 text-muted mb-3"></i>
                <h6 class="text-muted">No Active Chat Sessions</h6>
                <p class="text-muted small">Waiting for customers to start a chat...</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    sessions.forEach(session => {
        const isActive = session.id == currentActiveId;
        html += `
            <div class="chat-session-item p-3 border-bottom cursor-pointer ${isActive ? 'active' : ''}" 
                 data-session-id="${session.id}"
                 onclick="loadChatSession(${session.id})">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-3">
                        <div class="w-40 h-40 rounded-circle bg-primary d-flex align-items-center justify-content-center">
                            <i class="fe fe-user text-white"></i>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${escapeHtml(session.customer_name)}</h6>
                                <p class="text-muted small mb-1">${escapeHtml(session.customer_email)}</p>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-${session.status === 'waiting' ? 'warning' : 'success'}">
                                    ${session.status.charAt(0).toUpperCase() + session.status.slice(1)}
                                </span>
                                ${session.unread_count > 0 ? `<span class="badge badge-danger ml-1">${session.unread_count}</span>` : ''}
                            </div>
                        </div>
                        ${session.last_message ? `<p class="text-muted small mb-1 text-truncate">${escapeHtml(session.last_message.substring(0, 50))}${session.last_message.length > 50 ? '...' : ''}</p>` : ''}
                        <p class="text-muted small mb-0">
                            Started: ${new Date(session.started_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit'})}
                        </p>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function loadChatSession(sessionId) {
    // Clear previous polling
    if (messagePollingInterval) {
        clearInterval(messagePollingInterval);
    }
    
    // Update active session
    document.querySelectorAll('.chat-session-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-session-id="${sessionId}"]`).classList.add('active');
    
    currentSessionId = sessionId;
    
    // Show chat interface
    document.getElementById('noChatSelected').style.display = 'none';
    document.getElementById('chatMessagesContainer').style.display = 'block';
    document.getElementById('chatActions').style.display = 'block';
    
    // Load session details and messages
    loadMessages();
    
    // Start polling for new messages
    messagePollingInterval = setInterval(loadMessages, 3000);
}

function loadMessages() {
    if (!currentSessionId) return;
    
    fetch(`api/chat_messages.php?session_id=${currentSessionId}`, {
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateChatHeader(data.session);
                displayMessages(data.messages);
            } else {
                console.error('Error loading messages:', data.error);
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}

function updateChatHeader(session) {
    const header = document.getElementById('chatHeader');
    header.querySelector('h6').textContent = `Chat with ${session.customer_name}`;
}

function displayMessages(messages) {
    const container = document.getElementById('chatMessages');
    const shouldScroll = container.scrollTop + container.clientHeight >= container.scrollHeight - 50;
    
    let html = '';
    messages.forEach(message => {
        const isAgent = message.sender_type === 'agent';
        const time = new Date(message.created_at).toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit'});
        
        html += `
            <div class="d-flex ${isAgent ? 'justify-content-end' : 'justify-content-start'} mb-3">
                <div class="message-bubble ${isAgent ? 'message-agent' : 'message-customer'} px-3 py-2 rounded">
                    <div class="text-sm">${escapeHtml(message.message).replace(/\n/g, '<br>')}</div>
                    <div class="text-xs ${isAgent ? 'text-white-50' : 'text-muted'} mt-1">${time}</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    if (shouldScroll) {
        container.scrollTop = container.scrollHeight;
    }
}

function sendMessage(event) {
    event.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message || !currentSessionId) return;
    
    fetch('api/send_chat_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            session_id: currentSessionId,
            message: message
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            updateCharCount();
            loadMessages();
        } else {
            throw new Error(data.error || 'Unknown server error');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Error sending message: ' + error.message);
    });
}

function endChatSession() {
    if (!currentSessionId) {
        alert('No active chat session to end.');
        return;
    }
    
    if (confirm('Are you sure you want to end this chat session?')) {
        console.log('Ending chat session:', currentSessionId);
        fetch('api/end_chat_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                session_id: currentSessionId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Clear current session
                currentSessionId = null;
                if (messagePollingInterval) {
                    clearInterval(messagePollingInterval);
                    messagePollingInterval = null;
                }
                
                // Hide chat interface
                document.getElementById('noChatSelected').style.display = 'block';
                document.getElementById('chatMessagesContainer').style.display = 'none';
                document.getElementById('chatActions').style.display = 'none';
                
                // Refresh sessions list
                refreshSessions();
            } else {
                throw new Error(data.error || 'Unknown server error');
            }
        })
        .catch(error => {
            console.error('Error ending chat session:', error);
            alert('Error ending chat session: ' + error.message);
        });
    }
}

function updateCharCount() {
    const messageInput = document.getElementById('messageInput');
    const charCount = document.getElementById('charCount');
    const count = messageInput.value.length;
    charCount.textContent = `${count}/1000`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include 'includes/layout_end.php'; ?>