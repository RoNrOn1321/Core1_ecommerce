<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Seller Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'beige': '#b48d6b',
                        'beige-light': '#c8a382',
                        'beige-dark': '#9d7a5a',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-md z-50 h-20">
        <div class="flex items-center justify-between px-6 h-full">
            <div class="flex items-center">
                <button id="sidebarToggle" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-2xl text-gray-700 hover:text-beige transition-colors"></i>
                </button>
                <div class="logo text-3xl font-bold text-gray-800">
                    Lumino<span class="text-beige">Shop</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button class="text-gray-700 hover:text-beige transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </button>
                </div>
                <div class="relative">
                    <button class="text-gray-700 hover:text-beige transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-beige text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">5</span>
                    </button>
                </div>
                <div class="relative">
                    <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-beige transition-colors">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
                             alt="Profile" class="w-8 h-8 rounded-full">
                        <span class="hidden md:block">John Seller</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 w-64 h-screen bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 pt-20">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Seller Portal</h3>
            <p class="text-sm text-gray-600 mt-1">Manage your store</p>
        </div>
        <nav class="mt-6">
            <ul class="space-y-2 px-4">
                <li><a href="dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt w-5"></i><span class="ml-3">Dashboard</span></a></li>
                <li><a href="products.php" class="sidebar-link"><i class="fas fa-box w-5"></i><span class="ml-3">Products</span><span class="ml-auto bg-beige text-white text-xs px-2 py-1 rounded-full">24</span></a></li>
                <li><a href="orders.php" class="sidebar-link"><i class="fas fa-shopping-cart w-5"></i><span class="ml-3">Orders</span><span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">8</span></a></li>
                <li><a href="customers.php" class="sidebar-link"><i class="fas fa-users w-5"></i><span class="ml-3">Customers</span></a></li>
                <li><a href="analytics.php" class="sidebar-link"><i class="fas fa-chart-line w-5"></i><span class="ml-3">Analytics</span></a></li>
                <li><a href="reviews.php" class="sidebar-link"><i class="fas fa-star w-5"></i><span class="ml-3">Reviews</span></a></li>
                <li><a href="promotions.php" class="sidebar-link"><i class="fas fa-percent w-5"></i><span class="ml-3">Promotions</span></a></li>
                <li><a href="finances.php" class="sidebar-link"><i class="fas fa-wallet w-5"></i><span class="ml-3">Finances</span></a></li>
                <li><a href="settings.php" class="sidebar-link active"><i class="fas fa-cog w-5"></i><span class="ml-3">Settings</span></a></li>
            </ul>
        </nav>
        <div class="absolute bottom-6 left-4 right-4">
            <button class="w-full flex items-center justify-center px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Settings</h1>
                <p class="text-gray-600">Manage your store preferences and account settings</p>
            </div>

            <!-- Settings Navigation -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6">
                        <button class="settings-tab active border-b-2 border-beige text-beige py-4 px-1 font-medium text-sm" data-tab="profile">
                            <i class="fas fa-user mr-2"></i>Profile
                        </button>
                        <button class="settings-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-1 font-medium text-sm" data-tab="store">
                            <i class="fas fa-store mr-2"></i>Store Settings
                        </button>
                        <button class="settings-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-1 font-medium text-sm" data-tab="payments">
                            <i class="fas fa-credit-card mr-2"></i>Payments
                        </button>
                        <button class="settings-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-1 font-medium text-sm" data-tab="notifications">
                            <i class="fas fa-bell mr-2"></i>Notifications
                        </button>
                        <button class="settings-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-4 px-1 font-medium text-sm" data-tab="security">
                            <i class="fas fa-shield-alt mr-2"></i>Security
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Profile Settings -->
            <div id="profile-tab" class="settings-content">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Profile Information</h3>
                        <p class="text-sm text-gray-600 mt-1">Update your personal and business information</p>
                    </div>
                    <div class="p-6">
                        <form id="profileForm" class="space-y-6">
                            <!-- Profile Picture -->
                            <div class="flex items-center space-x-6">
                                <div class="shrink-0">
                                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face" 
                                         alt="Profile" class="w-20 h-20 rounded-full object-cover">
                                </div>
                                <div>
                                    <button type="button" class="btn-beige text-sm">
                                        <i class="fas fa-camera mr-2"></i>Change Photo
                                    </button>
                                    <p class="text-sm text-gray-600 mt-2">JPG, GIF or PNG. Max size 2MB.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input name="first_name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input name="last_name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input name="email" type="email" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 mt-1">Email cannot be changed. Contact support if needed.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input name="phone" type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="btn-beige">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Store Settings -->
            <div id="store-tab" class="settings-content hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Store Configuration</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage your store appearance and policies</p>
                    </div>
                    <div class="p-6">
                        <form id="storeForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
                                    <input name="store_name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store URL</label>
                                    <input name="store_slug" type="text" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 mt-1">Store URL is auto-generated from store name.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Description</label>
                                    <textarea name="store_description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                        <option>USD - US Dollar</option>
                                        <option>EUR - Euro</option>
                                        <option>GBP - British Pound</option>
                                        <option>CAD - Canadian Dollar</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                        <option>Eastern Time (UTC-5)</option>
                                        <option>Central Time (UTC-6)</option>
                                        <option>Mountain Time (UTC-7)</option>
                                        <option>Pacific Time (UTC-8)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Store Policies</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Return Policy</label>
                                        <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">We accept returns within 30 days of purchase. Items must be in original condition with tags attached.</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Policy</label>
                                        <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">Free shipping on orders over $50. Standard shipping takes 3-5 business days. Express shipping available.</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="btn-beige">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payment Settings -->
            <div id="payments-tab" class="settings-content hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Payment Methods</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage how you receive payments</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Bank Account -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-university text-white"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="font-medium text-gray-900">Bank Account</h4>
                                            <p class="text-sm text-gray-600">**** **** **** 1234</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Primary</span>
                                        <button class="text-beige hover:text-beige-dark"><i class="fas fa-edit"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- PayPal -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                            <i class="fab fa-paypal text-white"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="font-medium text-gray-900">PayPal</h4>
                                            <p class="text-sm text-gray-600">john.seller@email.com</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Connected</span>
                                        <button class="text-beige hover:text-beige-dark"><i class="fas fa-edit"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Payment Method -->
                            <button class="w-full border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-beige transition-colors">
                                <i class="fas fa-plus text-2xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Add New Payment Method</p>
                            </button>

                            <!-- Tax Settings -->
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Tax Settings</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax ID Number</label>
                                        <input type="text" value="123-45-6789" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Tax Rate (%)</label>
                                        <input type="number" step="0.01" value="8.25" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div id="notifications-tab" class="settings-content hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Notification Preferences</h3>
                        <p class="text-sm text-gray-600 mt-1">Choose how you want to be notified</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Email Notifications -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Email Notifications</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">New Orders</p>
                                            <p class="text-sm text-gray-600">Get notified when you receive new orders</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" checked class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-beige"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">Order Updates</p>
                                            <p class="text-sm text-gray-600">Updates on order status changes</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" checked class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-beige"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">Customer Messages</p>
                                            <p class="text-sm text-gray-600">Messages from customers</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-beige"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">Marketing Updates</p>
                                            <p class="text-sm text-gray-600">Platform updates and marketing tips</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" checked class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-beige"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- SMS Notifications -->
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">SMS Notifications</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">Urgent Orders</p>
                                            <p class="text-sm text-gray-600">High-value or priority orders</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" checked class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-beige"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">Payment Issues</p>
                                            <p class="text-sm text-gray-600">Failed payments or disputes</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-beige"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div id="security-tab" class="settings-content hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Security Settings</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage your account security</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Change Password -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Change Password</h4>
                                <form id="passwordForm" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                        <input name="current_password" type="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                        <input name="new_password" type="password" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                        <input name="confirm_password" type="password" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                    <button type="submit" class="btn-beige">Update Password</button>
                                </form>
                            </div>

                            <!-- Two-Factor Authentication -->
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Two-Factor Authentication</h4>
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">SMS Authentication</p>
                                        <p class="text-sm text-gray-600">Receive verification codes via SMS</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm text-red-600">Disabled</span>
                                        <button class="btn-beige-outline text-sm px-4 py-2">Enable</button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg mt-3">
                                    <div>
                                        <p class="font-medium text-gray-900">Authenticator App</p>
                                        <p class="text-sm text-gray-600">Use Google Authenticator or similar app</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm text-red-600">Disabled</span>
                                        <button class="btn-beige-outline text-sm px-4 py-2">Setup</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Session Management -->
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Active Sessions</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-desktop text-white"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium text-gray-900">Windows Desktop</p>
                                                <p class="text-sm text-gray-600">Chrome • New York, NY • Current session</p>
                                            </div>
                                        </div>
                                        <span class="text-sm text-green-600">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-mobile-alt text-white"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium text-gray-900">iPhone</p>
                                                <p class="text-sm text-gray-600">Safari • Last seen 2 hours ago</p>
                                            </div>
                                        </div>
                                        <button class="text-red-600 hover:text-red-800 text-sm">Revoke</button>
                                    </div>
                                </div>
                                <button type="button" class="mt-4 text-red-600 hover:text-red-800 text-sm font-medium">
                                    Sign out of all other sessions
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <div class="text-center">
                <div class="spinner mb-4"></div>
                <p class="text-gray-600">Loading settings...</p>
            </div>
        </div>
    </div>

    <!-- Notifications Container -->
    <div id="notificationsContainer" class="fixed top-20 right-4 z-40"></div>

    <script src="js/seller-api.js"></script>
    <script>
        // Initialize API client
        const api = new SellerAPI();
        let currentSeller = null;
        let currentPreferences = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', async () => {
            await initializeSettings();
        });

        async function initializeSettings() {
            try {
                showLoading(true);
                
                // Check authentication
                try {
                    const response = await api.getCurrentSeller();
                    currentSeller = response.data;
                    
                    // Load all settings data
                    await loadAllSettings();
                    
                } catch (authError) {
                    console.log('Not authenticated:', authError);
                    window.location.href = 'login.php';
                    return;
                }
                
            } catch (error) {
                console.error('Settings initialization error:', error);
                showNotification('Failed to load settings data', 'error');
            } finally {
                showLoading(false);
            }
        }

        async function loadAllSettings() {
            try {
                // Load profile and preferences in parallel
                const [profileResponse, preferencesResponse] = await Promise.all([
                    api.getProfile(),
                    api.getPreferences()
                ]);

                // Update profile form
                if (profileResponse.success) {
                    console.log('Profile data received:', profileResponse.data);
                    updateProfileForm(profileResponse.data);
                }

                // Update preferences
                if (preferencesResponse.success) {
                    currentPreferences = preferencesResponse.data;
                    updatePreferencesForm(preferencesResponse.data);
                }

                // Update store settings (using existing profile data)
                updateStoreForm(profileResponse.data);

            } catch (error) {
                console.error('Error loading settings:', error);
                showNotification('Some settings could not be loaded', 'warning');
            }
        }

        function updateProfileForm(seller) {
            console.log('updateProfileForm called with seller:', seller);
            
            // Update profile form fields using name attributes for reliability
            const firstNameInput = document.querySelector('input[name="first_name"]');
            const lastNameInput = document.querySelector('input[name="last_name"]');
            const emailInput = document.querySelector('input[name="email"]');
            const phoneInput = document.querySelector('input[name="phone"]');
            
            console.log('Form inputs found:', {
                firstNameInput: !!firstNameInput,
                lastNameInput: !!lastNameInput,
                emailInput: !!emailInput,
                phoneInput: !!phoneInput
            });
            
            if (firstNameInput) {
                firstNameInput.value = seller.first_name || '';
                console.log('First name set to:', seller.first_name);
            } else {
                console.error('First name input not found');
            }
            
            if (lastNameInput) {
                lastNameInput.value = seller.last_name || '';
                console.log('Last name set to:', seller.last_name);
            } else {
                console.error('Last name input not found');
            }
            
            if (emailInput) {
                emailInput.value = seller.email || '';
                console.log('Email set to:', seller.email);
            } else {
                console.error('Email input not found');
            }
            
            if (phoneInput) {
                phoneInput.value = seller.phone || '';
                console.log('Phone set to:', seller.phone);
            } else {
                console.error('Phone input not found');
            }

            // Update header profile name
            const profileName = document.querySelector('#profileDropdown span');
            if (profileName) {
                profileName.textContent = `${seller.first_name || ''} ${seller.last_name || ''}`;
                console.log('Header profile name updated to:', profileName.textContent);
            }
        }

        function updateStoreForm(seller) {
            // Update store form fields using name attributes for reliability
            const storeNameInput = document.querySelector('input[name="store_name"]');
            const storeUrlInput = document.querySelector('input[name="store_slug"]');
            const descriptionTextarea = document.querySelector('textarea[name="store_description"]');
            
            if (storeNameInput) {
                storeNameInput.value = seller.store_name || '';
                console.log('Store name set to:', seller.store_name);
            }
            if (storeUrlInput) {
                storeUrlInput.value = seller.store_slug || '';
                console.log('Store slug set to:', seller.store_slug);
            }
            if (descriptionTextarea) {
                descriptionTextarea.value = seller.store_description || '';
                console.log('Store description set to:', seller.store_description);
            }
        }

        function updatePreferencesForm(preferences) {
            // Update email notification toggles
            const emailToggles = {
                email_new_orders: document.querySelector('#notifications-tab input:nth-of-type(1)'),
                email_order_updates: document.querySelector('#notifications-tab input:nth-of-type(2)'),
                email_customer_messages: document.querySelector('#notifications-tab input:nth-of-type(3)'),
                email_marketing_updates: document.querySelector('#notifications-tab input:nth-of-type(4)')
            };

            const smsToggles = {
                sms_urgent_orders: document.querySelector('#notifications-tab .border-t input:nth-of-type(1)'),
                sms_payment_issues: document.querySelector('#notifications-tab .border-t input:nth-of-type(2)')
            };

            // Set email preferences
            Object.keys(emailToggles).forEach(key => {
                if (emailToggles[key] && preferences[key] !== undefined) {
                    emailToggles[key].checked = Boolean(parseInt(preferences[key]));
                }
            });

            // Set SMS preferences
            Object.keys(smsToggles).forEach(key => {
                if (smsToggles[key] && preferences[key] !== undefined) {
                    smsToggles[key].checked = Boolean(parseInt(preferences[key]));
                }
            });
        }

        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (show) {
                overlay.classList.remove('hidden');
            } else {
                overlay.classList.add('hidden');
            }
        }

        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationsContainer');
            const notification = document.createElement('div');
            notification.className = `mb-4 p-4 rounded-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            
            container.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('opacity-100');
            }, 100);
            
            // Hide after 5 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
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

        // Settings tabs functionality
        const settingsTabs = document.querySelectorAll('.settings-tab');
        const settingsContents = document.querySelectorAll('.settings-content');

        settingsTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetTab = tab.getAttribute('data-tab');
                
                // Remove active class from all tabs
                settingsTabs.forEach(t => {
                    t.classList.remove('active', 'border-beige', 'text-beige');
                    t.classList.add('border-transparent', 'text-gray-500');
                });
                
                // Add active class to clicked tab
                tab.classList.add('active', 'border-beige', 'text-beige');
                tab.classList.remove('border-transparent', 'text-gray-500');
                
                // Hide all content sections
                settingsContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show target content section
                document.getElementById(targetTab + '-tab').classList.remove('hidden');
            });
        });

        // Form submission handlers
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleProfileUpdate(e.target);
        });

        document.getElementById('storeForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleStoreUpdate(e.target);
        });

        // Password change form handler
        document.getElementById('passwordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await handlePasswordChange(e.target);
        });

        // Profile update handler
        async function handleProfileUpdate(form) {
            try {
                showLoading(true);
                
                const formData = new FormData(form);
                const profileData = {
                    first_name: formData.get('first_name'),
                    last_name: formData.get('last_name'),
                    phone: formData.get('phone')
                };

                const response = await api.updateProfile(profileData);
                
                if (response.success) {
                    showNotification('Profile updated successfully', 'success');
                    
                    // Update current seller data
                    currentSeller.first_name = profileData.first_name;
                    currentSeller.last_name = profileData.last_name;
                    currentSeller.phone = profileData.phone;
                    
                    // Update profile name display
                    const profileName = document.querySelector('#profileDropdown span');
                    if (profileName) {
                        profileName.textContent = `${profileData.first_name} ${profileData.last_name}`;
                    }
                } else {
                    showNotification(response.message || 'Profile update failed', 'error');
                }
            } catch (error) {
                console.error('Profile update error:', error);
                showNotification('Failed to update profile', 'error');
            } finally {
                showLoading(false);
            }
        }

        // Store update handler
        async function handleStoreUpdate(form) {
            try {
                showLoading(true);
                
                const formData = new FormData(form);
                const storeData = {
                    store_name: formData.get('store_name'),
                    store_description: formData.get('store_description')
                };

                const response = await api.updateStoreProfile(storeData);
                
                if (response.success) {
                    showNotification('Store settings updated successfully', 'success');
                } else {
                    showNotification(response.message || 'Store update failed', 'error');
                }
            } catch (error) {
                console.error('Store update error:', error);
                showNotification('Failed to update store settings', 'error');
            } finally {
                showLoading(false);
            }
        }

        // Password change handler
        async function handlePasswordChange(form) {
            try {
                const formData = new FormData(form);
                const currentPassword = formData.get('current_password');
                const newPassword = formData.get('new_password');
                const confirmPassword = formData.get('confirm_password');

                if (!currentPassword || !newPassword || !confirmPassword) {
                    showNotification('All password fields are required', 'error');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    showNotification('New passwords do not match', 'error');
                    return;
                }

                if (newPassword.length < 6) {
                    showNotification('New password must be at least 6 characters', 'error');
                    return;
                }

                showLoading(true);

                const response = await api.changePassword(currentPassword, newPassword, confirmPassword);
                
                if (response.success) {
                    showNotification('Password changed successfully', 'success');
                    form.reset(); // Clear the form
                } else {
                    showNotification(response.message || 'Password change failed', 'error');
                }
            } catch (error) {
                console.error('Password change error:', error);
                showNotification('Failed to change password', 'error');
            } finally {
                showLoading(false);
            }
        }

        // Notification preferences handler
        document.addEventListener('change', async (e) => {
            if (e.target.type === 'checkbox' && e.target.closest('#notifications-tab')) {
                await handlePreferencesUpdate();
            }
        });

        async function handlePreferencesUpdate() {
            try {
                // Collect all preference checkboxes
                const preferences = {};
                
                // Email preferences
                const emailToggles = document.querySelectorAll('#notifications-tab input[type="checkbox"]');
                emailToggles.forEach((toggle, index) => {
                    const fieldNames = [
                        'email_new_orders',
                        'email_order_updates', 
                        'email_customer_messages', 
                        'email_marketing_updates',
                        'sms_urgent_orders',
                        'sms_payment_issues'
                    ];
                    
                    if (fieldNames[index]) {
                        preferences[fieldNames[index]] = toggle.checked;
                    }
                });

                const response = await api.updatePreferences(preferences);
                
                if (response.success) {
                    showNotification('Preferences updated', 'success');
                } else {
                    showNotification('Failed to update preferences', 'error');
                }
            } catch (error) {
                console.error('Preferences update error:', error);
                showNotification('Failed to update preferences', 'error');
            }
        }

        // Logout functionality
        async function logout() {
            try {
                await api.logout();
                window.location.href = 'login.php';
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = 'login.php';
            }
        }

        // Add logout handler to logout button
        const logoutBtn = document.querySelector('.bg-red-500');
        if (logoutBtn) {
            logoutBtn.onclick = logout;
        }
    </script>
</body>
</html>