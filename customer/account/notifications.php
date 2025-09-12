<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Lumino</title>
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
                <a href="profile.php" class="hover:text-blue-600">Account</a>
                <i class="fas fa-chevron-right"></i>
                <span class="text-gray-900">Notifications</span>
            </nav>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                    <p class="text-gray-600 mt-2">Stay updated with your orders, products, and account activity</p>
                </div>
                <div class="space-x-4">
                    <button id="markAllReadBtn" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-check-double mr-2"></i>Mark All Read
                    </button>
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
                <p class="text-gray-600 mb-6">You need to be logged in to view your notifications.</p>
                <a href="../login.php?redirect=account/notifications.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-block">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login to Continue
                </a>
            </div>
        </div>

        <!-- Notifications Content -->
        <div id="notificationsContent" style="display: none;">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter by Type</h3>
                        <div class="space-y-2">
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors active" data-type="all">
                                <i class="fas fa-bell mr-2"></i>All Notifications <span id="filter-count-all" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="order">
                                <i class="fas fa-shopping-bag mr-2 text-blue-500"></i>Orders <span id="filter-count-order" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="support">
                                <i class="fas fa-headset mr-2 text-purple-500"></i>Support <span id="filter-count-support" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="product">
                                <i class="fas fa-box mr-2 text-green-500"></i>Products <span id="filter-count-product" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="promotion">
                                <i class="fas fa-tag mr-2 text-yellow-500"></i>Promotions <span id="filter-count-promotion" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="payment">
                                <i class="fas fa-credit-card mr-2 text-indigo-500"></i>Payments <span id="filter-count-payment" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="shipping">
                                <i class="fas fa-truck mr-2 text-orange-500"></i>Shipping <span id="filter-count-shipping" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="system">
                                <i class="fas fa-cog mr-2 text-gray-500"></i>System <span id="filter-count-system" class="float-right text-sm text-gray-500">0</span>
                            </button>
                            <button class="notification-filter w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors" data-type="account">
                                <i class="fas fa-user mr-2 text-pink-500"></i>Account <span id="filter-count-account" class="float-right text-sm text-gray-500">0</span>
                            </button>
                        </div>
                        
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-medium text-gray-700">Show read notifications</label>
                                <input type="checkbox" id="includeReadToggle" class="rounded">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Notifications -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 id="notificationsTitle" class="text-lg font-semibold text-gray-900">All Notifications</h3>
                                <div class="flex items-center space-x-4">
                                    <span id="notificationCount" class="text-sm text-gray-500">Loading...</span>
                                    <button id="refreshBtn" class="text-blue-600 hover:text-blue-800 transition-colors">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="mainNotificationsList" class="max-h-screen overflow-y-auto">
                            <!-- Loading -->
                            <div id="mainNotificationsLoading" class="p-8 text-center">
                                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Loading notifications...</p>
                            </div>
                            
                            <!-- Notifications will be loaded here -->
                        </div>

                        <!-- Load More -->
                        <div id="loadMoreSection" class="p-6 border-t border-gray-200 text-center" style="display: none;">
                            <button id="loadMoreBtn" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                <i class="fas fa-chevron-down mr-2"></i>Load More
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../components/footer.php'; ?>

<script>
let currentFilter = 'all';
let includeRead = false;
let currentOffset = 0;
let hasMore = false;
let allNotifications = [];

document.addEventListener('DOMContentLoaded', function() {
    checkAuthenticationAndLoadNotifications();
    setupEventListeners();
});

async function checkAuthenticationAndLoadNotifications() {
    try {
        const response = await customerAPI.auth.getProfile();
        
        if (response.success && response.customer) {
            document.getElementById('authCheckLoader').style.display = 'none';
            document.getElementById('notificationsContent').style.display = 'block';
            
            await loadNotifications();
            await updateFilterCounts();
        } else {
            document.getElementById('authCheckLoader').style.display = 'none';
            document.getElementById('loginRequired').style.display = 'block';
        }
    } catch (error) {
        console.error('Authentication check failed:', error);
        document.getElementById('authCheckLoader').style.display = 'none';
        document.getElementById('loginRequired').style.display = 'block';
    }
}

function setupEventListeners() {
    // Filter buttons
    const filterButtons = document.querySelectorAll('.notification-filter');
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const type = button.dataset.type;
            switchFilter(type);
        });
    });

    // Include read toggle
    document.getElementById('includeReadToggle').addEventListener('change', (e) => {
        includeRead = e.target.checked;
        currentOffset = 0;
        loadNotifications();
    });

    // Mark all read
    document.getElementById('markAllReadBtn').addEventListener('click', markAllAsRead);

    // Refresh
    document.getElementById('refreshBtn').addEventListener('click', () => {
        currentOffset = 0;
        loadNotifications();
        updateFilterCounts();
    });

    // Load more
    document.getElementById('loadMoreBtn').addEventListener('click', loadMoreNotifications);
}

async function loadNotifications(append = false) {
    const loading = document.getElementById('mainNotificationsLoading');
    const list = document.getElementById('mainNotificationsList');
    
    if (!append) {
        loading.style.display = 'block';
    }
    
    try {
        const options = {
            limit: 20,
            offset: currentOffset,
            includeRead: includeRead
        };
        
        if (currentFilter !== 'all') {
            options.type = currentFilter;
        }
        
        const response = await customerAPI.notifications.getAll(options);
        
        if (response.success && response.data) {
            const notifications = response.data.notifications;
            hasMore = response.data.pagination.has_more;
            
            if (append) {
                allNotifications = [...allNotifications, ...notifications];
            } else {
                allNotifications = notifications;
            }
            
            renderNotifications(allNotifications);
            updateNotificationCount(response.data.pagination.total);
            
            // Show/hide load more button
            const loadMoreSection = document.getElementById('loadMoreSection');
            loadMoreSection.style.display = hasMore ? 'block' : 'none';
        }
    } catch (error) {
        console.error('Failed to load notifications:', error);
        list.innerHTML = '<div class="p-8 text-center text-red-600"><i class="fas fa-exclamation-triangle mb-2"></i><p>Failed to load notifications</p></div>';
    } finally {
        if (!append) {
            loading.style.display = 'none';
        }
    }
}

async function loadMoreNotifications() {
    currentOffset += 20;
    await loadNotifications(true);
}

function renderNotifications(notifications) {
    const list = document.getElementById('mainNotificationsList');
    const loading = document.getElementById('mainNotificationsLoading');
    
    if (!notifications || notifications.length === 0) {
        loading.style.display = 'none';
        list.innerHTML = loading.outerHTML + '<div class="p-8 text-center text-gray-500"><i class="fas fa-bell-slash text-4xl mb-4"></i><p class="text-lg">No notifications found</p><p class="text-sm">You\'re all caught up!</p></div>';
        return;
    }

    const notificationsHTML = notifications.map(notification => {
        const typeClass = getNotificationTypeClass(notification.type);
        const priorityClass = getNotificationPriorityClass(notification.priority);
        const timeAgo = getTimeAgo(notification.created_at);
        const icon = getNotificationIcon(notification.type);
        const isRead = notification.is_read;
        
        return `
            <div class="border-b border-gray-100 last:border-b-0 ${isRead ? 'bg-gray-50' : 'bg-white'}">
                <div class="p-6 hover:bg-gray-50 cursor-pointer transition-colors" onclick="handleNotificationClick('${notification.id}', '${notification.action_url || ''}', ${isRead})">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-10 h-10 ${typeClass} rounded-full flex items-center justify-center">
                                <i class="${icon} text-white"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-lg font-semibold text-gray-900 ${isRead ? 'opacity-70' : ''}">${notification.title}</h4>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">${timeAgo}</span>
                                    ${!isRead ? '<div class="w-3 h-3 bg-blue-500 rounded-full"></div>' : ''}
                                </div>
                            </div>
                            <p class="text-gray-700 mb-3 ${isRead ? 'opacity-70' : ''}">${notification.message}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full ${typeClass} bg-opacity-20">${notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}</span>
                                    ${notification.priority !== 'medium' ? `<span class="px-3 py-1 text-xs font-medium rounded-full ${priorityClass}">${notification.priority}</span>` : ''}
                                </div>
                                <div class="flex items-center space-x-2">
                                    ${!isRead ? `<button onclick="event.stopPropagation(); markAsRead('${notification.id}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Mark as read</button>` : ''}
                                    <button onclick="event.stopPropagation(); deleteNotification('${notification.id}')" class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    // Keep loading element available but hidden, then add notifications
    loading.style.display = 'none';
    list.innerHTML = loading.outerHTML + notificationsHTML;
}

function switchFilter(type) {
    currentFilter = type;
    currentOffset = 0;
    
    // Update filter button states
    const buttons = document.querySelectorAll('.notification-filter');
    buttons.forEach(button => {
        if (button.dataset.type === type) {
            button.classList.add('active', 'bg-blue-100', 'text-blue-700');
        } else {
            button.classList.remove('active', 'bg-blue-100', 'text-blue-700');
        }
    });
    
    // Update title
    const title = type === 'all' ? 'All Notifications' : type.charAt(0).toUpperCase() + type.slice(1) + ' Notifications';
    document.getElementById('notificationsTitle').textContent = title;
    
    loadNotifications();
}

async function updateFilterCounts() {
    try {
        const response = await customerAPI.notifications.getCount();
        
        if (response.success && response.data) {
            const data = response.data;
            
            document.getElementById('filter-count-all').textContent = data.total_unread;
            document.getElementById('filter-count-order').textContent = data.order_count;
            document.getElementById('filter-count-support').textContent = data.support_count;
            document.getElementById('filter-count-product').textContent = data.product_count;
            document.getElementById('filter-count-promotion').textContent = data.promotion_count;
            document.getElementById('filter-count-payment').textContent = data.payment_count;
            document.getElementById('filter-count-shipping').textContent = data.shipping_count;
            document.getElementById('filter-count-system').textContent = data.system_count;
            document.getElementById('filter-count-account').textContent = data.account_count;
        }
    } catch (error) {
        console.error('Failed to update filter counts:', error);
    }
}

function updateNotificationCount(total) {
    document.getElementById('notificationCount').textContent = `${total} notification${total !== 1 ? 's' : ''}`;
}

async function handleNotificationClick(notificationId, actionUrl, isRead) {
    try {
        if (!isRead) {
            await customerAPI.notifications.markAsRead(notificationId);
            updateFilterCounts();
            if (typeof updateNotificationCount === 'function') {
                updateNotificationCount();
            }
        }
        
        if (actionUrl) {
            window.location.href = actionUrl;
        }
    } catch (error) {
        console.error('Failed to handle notification click:', error);
        if (actionUrl) {
            window.location.href = actionUrl;
        }
    }
}

async function markAsRead(notificationId) {
    try {
        const response = await customerAPI.notifications.markAsRead(notificationId);
        if (response.success) {
            loadNotifications();
            updateFilterCounts();
            if (typeof updateNotificationCount === 'function') {
                updateNotificationCount();
            }
        }
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
}

async function markAllAsRead() {
    try {
        let types = null;
        if (currentFilter !== 'all') {
            types = [currentFilter];
        }
        
        const response = await customerAPI.notifications.markAllAsRead(types);
        if (response.success) {
            showToast('All notifications marked as read', 'success');
            loadNotifications();
            updateFilterCounts();
            if (typeof updateNotificationCount === 'function') {
                updateNotificationCount();
            }
        }
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
}

async function deleteNotification(notificationId) {
    if (!confirm('Are you sure you want to delete this notification?')) return;
    
    try {
        const response = await customerAPI.notifications.delete(notificationId);
        if (response.success) {
            showToast('Notification deleted', 'success');
            loadNotifications();
            updateFilterCounts();
            if (typeof updateNotificationCount === 'function') {
                updateNotificationCount();
            }
        }
    } catch (error) {
        console.error('Failed to delete notification:', error);
    }
}

// Helper functions (same as in navbar)
function getNotificationTypeClass(type) {
    const typeClasses = {
        'order': 'bg-blue-500',
        'support': 'bg-purple-500', 
        'product': 'bg-green-500',
        'promotion': 'bg-yellow-500',
        'payment': 'bg-indigo-500',
        'shipping': 'bg-orange-500',
        'system': 'bg-gray-500',
        'account': 'bg-pink-500'
    };
    return typeClasses[type] || 'bg-gray-500';
}

function getNotificationIcon(type) {
    const typeIcons = {
        'order': 'fas fa-shopping-bag',
        'support': 'fas fa-headset',
        'product': 'fas fa-box',
        'promotion': 'fas fa-tag',
        'payment': 'fas fa-credit-card',
        'shipping': 'fas fa-truck',
        'system': 'fas fa-cog',
        'account': 'fas fa-user'
    };
    return typeIcons[type] || 'fas fa-bell';
}

function getNotificationPriorityClass(priority) {
    const priorityClasses = {
        'urgent': 'bg-red-100 text-red-800',
        'high': 'bg-orange-100 text-orange-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'low': 'bg-green-100 text-green-800'
    };
    return priorityClasses[priority] || 'bg-gray-100 text-gray-800';
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
    return date.toLocaleDateString();
}

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