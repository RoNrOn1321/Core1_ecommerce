/**
 * Real-time notification badge updates for admin sidebar
 */

class NotificationManager {
    constructor() {
        this.updateInterval = 30000; // Update every 30 seconds
        this.intervalId = null;
        this.apiUrl = 'api/notifications.php';
        this.isActive = true;
        
        this.init();
    }
    
    init() {
        // Start periodic updates
        this.startPeriodicUpdates();
        
        // Handle page visibility changes to pause/resume updates
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });
        
        // Handle window focus/blur
        window.addEventListener('focus', () => this.resumeUpdates());
        window.addEventListener('blur', () => this.pauseUpdates());
        
        console.log('NotificationManager initialized');
    }
    
    startPeriodicUpdates() {
        // Initial update
        this.updateNotifications();
        
        // Set up periodic updates
        this.intervalId = setInterval(() => {
            if (this.isActive) {
                this.updateNotifications();
            }
        }, this.updateInterval);
    }
    
    pauseUpdates() {
        this.isActive = false;
        console.log('Notification updates paused');
    }
    
    resumeUpdates() {
        this.isActive = true;
        // Trigger immediate update when resuming
        this.updateNotifications();
        console.log('Notification updates resumed');
    }
    
    async updateNotifications() {
        try {
            // Add loading state to badges
            this.setLoadingState(true);
            
            const response = await fetch(this.apiUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.updateBadges(data.data);
            } else {
                console.error('API returned error:', data.error);
            }
            
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
            // On error, keep existing values but remove loading state
            this.setLoadingState(false);
        }
    }
    
    setLoadingState(isLoading) {
        const badges = document.querySelectorAll('[data-notification]');
        badges.forEach(badge => {
            if (isLoading) {
                badge.classList.add('loading');
            } else {
                badge.classList.remove('loading');
            }
        });
    }
    
    updateBadges(notifications) {
        // Update each notification badge
        Object.keys(notifications).forEach(type => {
            const badge = document.querySelector(`[data-notification="${type}"]`);
            if (badge) {
                const count = notifications[type];
                const previousCount = parseInt(badge.textContent) || 0;
                const isActionable = type === 'sellers' || type === 'orders' || type === 'support';
                
                // Update badge text
                badge.textContent = count;
                
                // Remove loading state
                badge.classList.remove('loading');
                
                // Show/hide badge based on count and type
                if (isActionable) {
                    // For actionable items, only show if count > 0
                    badge.style.display = count > 0 ? '' : 'none';
                } else {
                    // For informational items, always show
                    badge.style.display = '';
                }
                
                // Add visual feedback for updates if count changed
                if (count !== previousCount) {
                    this.addUpdateAnimation(badge);
                    
                    // Log significant changes
                    if (isActionable && count > previousCount) {
                        console.log(`New ${type} notification: ${count} (was ${previousCount})`);
                    }
                }
            }
        });
    }
    
    addUpdateAnimation(element) {
        // Add a subtle pulse animation to indicate update
        element.style.transition = 'transform 0.3s ease';
        element.style.transform = 'scale(1.1)';
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 300);
    }
    
    destroy() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        this.isActive = false;
        console.log('NotificationManager destroyed');
    }
}

// Initialize notification manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're in the admin area and have notification badges
    if (document.querySelector('[data-notification]')) {
        window.notificationManager = new NotificationManager();
    }
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (window.notificationManager) {
        window.notificationManager.destroy();
    }
});