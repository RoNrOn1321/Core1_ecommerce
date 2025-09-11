    <!-- Notifications Container -->
    <div id="notificationsContainer" class="fixed top-20 right-4 z-40"></div>

    <!-- Scripts -->
    <script src="js/seller-api.js"></script>
    <script>
        // Initialize API client
        const api = new SellerAPI();
        let currentSeller = null;

        // Common functionality
        function updateApiStatus(online) {
            const statusElement = document.getElementById('apiStatus');
            const statusClass = online ? 'api-status online' : 'api-status offline';
            const statusText = online ? 'API Online' : 'API Offline';
            
            statusElement.innerHTML = `
                <div class="${statusClass}">
                    <div class="status-dot"></div>
                    <span>${statusText}</span>
                </div>
            `;
        }

        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (show) {
                overlay.classList.remove('hidden');
            } else {
                overlay.classList.add('hidden');
            }
        }

        function showAuthRequired() {
            document.getElementById('authCheck').classList.remove('hidden');
        }

        function redirectToLogin() {
            window.location.href = 'login.php';
        }

        async function logout() {
            try {
                await api.logout();
                showNotification('Logged out successfully', 'success');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1000);
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = 'login.php';
            }
        }

        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationsContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            container.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Hide after 5 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        function updateProfileInfo(seller) {
            document.getElementById('profileName').textContent = 
                `${seller.first_name} ${seller.last_name}`;
        }

        // Sidebar functionality
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        // Profile dropdown
        document.getElementById('profileDropdown').addEventListener('click', () => {
            document.getElementById('profileMenu').classList.toggle('hidden');
        });

        // Close profile dropdown when clicking outside
        document.addEventListener('click', (event) => {
            const profileDropdown = document.getElementById('profileDropdown');
            const profileMenu = document.getElementById('profileMenu');
            
            if (!profileDropdown.contains(event.target)) {
                profileMenu.classList.add('hidden');
            }
        });

        // Initialize basic auth check
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                showLoading(true);
                const response = await api.getCurrentSeller();
                currentSeller = response.data;
                updateApiStatus(true);
                updateProfileInfo(currentSeller);
                
                // If page has initializePageData function, call it
                if (typeof initializePageData === 'function') {
                    await initializePageData();
                }
                
            } catch (error) {
                console.log('Not authenticated:', error);
                updateApiStatus(false);
                showAuthRequired();
            } finally {
                showLoading(false);
            }
        });
    </script>
</body>
</html>