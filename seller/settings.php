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
                        <form class="space-y-6">
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
                                    <input type="text" value="John" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" value="Seller" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" value="john.seller@email.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="tel" value="+1 (555) 123-4567" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Business Address</label>
                                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">123 Business Street, Suite 100
New York, NY 10001
United States</textarea>
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
                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
                                    <input type="text" value="John's Premium Store" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store URL</label>
                                    <input type="text" value="johns-premium-store" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Description</label>
                                    <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">Welcome to John's Premium Store! We offer high-quality products with exceptional customer service. Browse our carefully curated selection of premium items.</textarea>
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
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                        <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                        <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                        <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                    </div>
                                    <button type="button" class="btn-beige">Update Password</button>
                                </div>
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

    <script>
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
    </script>
</body>
</html>