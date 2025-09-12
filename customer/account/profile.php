<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Core1 E-commerce</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gray-50">

<?php 
// Require authentication
require_once '../auth/functions.php';
requireLogin();
?>

<?php include '../components/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <a href="../index.php" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-home"></i>
                            <span class="sr-only">Home</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-4"></i>
                            <a href="../account/orders.php" class="text-gray-500 hover:text-gray-700">Account</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-4"></i>
                            <span class="text-gray-700 font-medium">Profile</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Account Navigation -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-wrap gap-4">
                <a href="orders.php" class="flex items-center px-4 py-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                    <i class="fas fa-box mr-2"></i>
                    My Orders
                </a>
                <a href="addresses.php" class="flex items-center px-4 py-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Addresses
                </a>
                <a href="profile.php" class="flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg">
                    <i class="fas fa-user-edit mr-2"></i>
                    Profile
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-user text-amber-600 mr-2"></i>
                            Personal Information
                        </h2>
                        <button onclick="toggleEditMode('personal')" id="editPersonalBtn"
                                class="text-amber-600 hover:text-amber-700 font-medium">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </button>
                    </div>
                    
                    <form id="personalInfoForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" id="firstName" name="firstName" disabled
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500">
                            </div>
                            <div>
                                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" id="lastName" name="lastName" disabled
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500">
                            </div>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" id="phone" name="phone" disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500">
                        </div>
                        
                        <div id="personalFormActions" class="flex gap-3 pt-4" style="display: none;">
                            <button type="submit" id="savePersonalBtn"
                                    class="bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                            <button type="button" onclick="cancelEditMode('personal')"
                                    class="border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-lock text-amber-600 mr-2"></i>
                            Change Password
                        </h2>
                        <button onclick="toggleEditMode('password')" id="editPasswordBtn"
                                class="text-amber-600 hover:text-amber-700 font-medium">
                            <i class="fas fa-edit mr-1"></i>
                            Change
                        </button>
                    </div>
                    
                    <div id="passwordInfo" class="text-gray-600">
                        <p>Keep your account secure with a strong password.</p>
                        <p class="text-sm mt-2">Last password change: <span id="lastPasswordChange">Not available</span></p>
                    </div>
                    
                    <form id="passwordForm" style="display: none;" class="space-y-4">
                        <div>
                            <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" id="newPassword" name="newPassword" required minlength="6"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Password must be at least 6 characters long</p>
                        </div>
                        
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="submit" id="savePasswordBtn"
                                    class="bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Update Password
                            </button>
                            <button type="button" onclick="cancelEditMode('password')"
                                    class="border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Email Preferences -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-envelope text-amber-600 mr-2"></i>
                        Email Preferences
                    </h2>
                    
                    <form id="preferencesForm" class="space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="orderUpdates" name="orderUpdates" checked
                                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded mr-3">
                                <label for="orderUpdates" class="text-sm text-gray-700">Order updates and shipping notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="promotions" name="promotions" checked
                                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded mr-3">
                                <label for="promotions" class="text-sm text-gray-700">Promotions and special offers</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="newsletter" name="newsletter"
                                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded mr-3">
                                <label for="newsletter" class="text-sm text-gray-700">Weekly newsletter with new products</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="recommendations" name="recommendations"
                                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded mr-3">
                                <label for="recommendations" class="text-sm text-gray-700">Product recommendations based on my purchases</label>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" id="savePreferencesBtn"
                                    class="bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Profile Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-center">
                        <div class="relative inline-block mb-4">
                            <div id="profileImageContainer" class="w-20 h-20 rounded-full overflow-hidden mx-auto relative">
                                <img id="profileImage" src="" alt="Profile" class="w-full h-full object-cover" style="display: none;">
                                <div id="profileImagePlaceholder" class="w-full h-full bg-amber-600 text-white flex items-center justify-center text-2xl">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="absolute -bottom-1 -right-1">
                                <button onclick="openImageUploadModal()" class="w-7 h-7 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:text-amber-600 hover:border-amber-600 transition-colors shadow-sm">
                                    <i class="fas fa-camera text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <h3 class="font-semibold text-gray-900" id="profileName">Loading...</h3>
                        <p class="text-gray-600 text-sm" id="profileEmail">Loading...</p>
                        <div class="mt-4 flex items-center justify-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span id="accountStatus">Active</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Account Statistics</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Total Orders</span>
                            <span class="font-medium" id="totalOrders">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Total Spent</span>
                            <span class="font-medium" id="totalSpent">₱0.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Saved Addresses</span>
                            <span class="font-medium" id="totalAddresses">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Member Since</span>
                            <span class="font-medium" id="memberSince">-</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="orders.php" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-box text-amber-600 mr-3"></i>
                                <span class="text-sm font-medium">View Orders</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        <a href="addresses.php" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-amber-600 mr-3"></i>
                                <span class="text-sm font-medium">Manage Addresses</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        <a href="../products.php" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-shopping-bag text-amber-600 mr-3"></i>
                                <span class="text-sm font-medium">Continue Shopping</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Account Actions</h3>
                    <div class="space-y-3">
                        <button onclick="downloadData()" class="w-full flex items-center justify-center p-3 border border-gray-200 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            <span class="text-sm font-medium">Download My Data</span>
                        </button>
                        <button onclick="confirmDeleteAccount()" class="w-full flex items-center justify-center p-3 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            <span class="text-sm font-medium">Delete Account</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md w-full">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">Delete Account</h3>
        </div>
        <p class="text-gray-600 mb-4">Are you sure you want to delete your account? This action cannot be undone and will permanently remove:</p>
        <ul class="text-gray-600 text-sm mb-6 list-disc list-inside space-y-1">
            <li>Your profile information</li>
            <li>Order history</li>
            <li>Saved addresses</li>
            <li>Preferences and settings</li>
        </ul>
        <div class="flex gap-3">
            <button onclick="deleteAccount()" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                Yes, Delete My Account
            </button>
            <button onclick="hideDeleteAccountModal()" class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Profile Image Upload Modal -->
<div id="imageUploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md w-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Update Profile Picture</h3>
            <button onclick="closeImageUploadModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Current Image Preview -->
        <div class="text-center mb-6">
            <div class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4 border-2 border-gray-200">
                <img id="modalCurrentImage" src="" alt="Current Profile" class="w-full h-full object-cover" style="display: none;">
                <div id="modalCurrentPlaceholder" class="w-full h-full bg-amber-600 text-white flex items-center justify-center text-xl">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
        
        <!-- File Upload -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Choose New Image</label>
            <div class="flex items-center justify-center w-full">
                <label for="imageUpload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadPlaceholder">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                        <p class="mb-2 text-sm text-gray-500">
                            <span class="font-semibold">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                    </div>
                    <div id="selectedImagePreview" class="w-full h-full rounded-lg overflow-hidden" style="display: none;">
                        <img id="previewImage" class="w-full h-full object-cover" src="" alt="Preview">
                    </div>
                    <input id="imageUpload" type="file" class="hidden" accept="image/*" onchange="previewSelectedImage(this)">
                </label>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex gap-3">
            <button id="uploadButton" onclick="uploadProfileImage()" disabled
                    class="flex-1 bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                <i class="fas fa-upload mr-2"></i>Upload Image
            </button>
            <button id="deleteImageButton" onclick="deleteProfileImage()" 
                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                    style="display: none;">
                <i class="fas fa-trash mr-2"></i>Remove
            </button>
        </div>
    </div>
</div>

<script src="../assets/js/customer-api.js"></script>
<script>
let originalPersonalData = {};

document.addEventListener('DOMContentLoaded', function() {
    loadProfile();
    loadAccountStats();
});

async function loadProfile() {
    try {
        const response = await customerAPI.auth.getProfile();
        if (response.success && response.customer) {
            const customer = response.customer;
            
            // Update form fields
            document.getElementById('firstName').value = customer.first_name || '';
            document.getElementById('lastName').value = customer.last_name || '';
            document.getElementById('email').value = customer.email || '';
            document.getElementById('phone').value = customer.phone || '';
            
            // Update sidebar
            document.getElementById('profileName').textContent = `${customer.first_name || ''} ${customer.last_name || ''}`.trim() || 'No Name';
            document.getElementById('profileEmail').textContent = customer.email || '';
            
            // Update profile image
            updateProfileImageDisplay(customer.profile_image);
            
            // Store original data
            originalPersonalData = {
                first_name: customer.first_name || '',
                last_name: customer.last_name || '',
                email: customer.email || '',
                phone: customer.phone || '',
                profile_image: customer.profile_image || null
            };
            
            // Update member since date
            if (customer.created_at) {
                document.getElementById('memberSince').textContent = new Date(customer.created_at).toLocaleDateString();
            }
        }
    } catch (error) {
        console.error('Failed to load profile:', error);
        showToast('Failed to load profile information', 'error');
    }
}

async function loadAccountStats() {
    try {
        // Load orders count and total spent
        const ordersResponse = await customerAPI.orders.getAll();
        if (ordersResponse.success) {
            const orders = ordersResponse.data;
            document.getElementById('totalOrders').textContent = orders.length;
            
            const totalSpent = orders.reduce((sum, order) => sum + parseFloat(order.total_amount || 0), 0);
            document.getElementById('totalSpent').textContent = `₱${totalSpent.toFixed(2)}`;
        }
        
        // Load addresses count
        const addressesResponse = await customerAPI.addresses.getAll();
        if (addressesResponse.success) {
            document.getElementById('totalAddresses').textContent = addressesResponse.data.length;
        }
    } catch (error) {
        console.error('Failed to load account stats:', error);
    }
}

function toggleEditMode(section) {
    if (section === 'personal') {
        const fields = ['firstName', 'lastName', 'email', 'phone'];
        const isEditing = !document.getElementById('firstName').disabled;
        
        if (isEditing) {
            // Cancel edit mode
            cancelEditMode('personal');
        } else {
            // Enable edit mode
            fields.forEach(field => {
                document.getElementById(field).disabled = false;
                document.getElementById(field).classList.remove('disabled:bg-gray-50', 'disabled:text-gray-500');
            });
            document.getElementById('personalFormActions').style.display = 'flex';
            document.getElementById('editPersonalBtn').innerHTML = '<i class="fas fa-times mr-1"></i>Cancel';
        }
    } else if (section === 'password') {
        const passwordForm = document.getElementById('passwordForm');
        const passwordInfo = document.getElementById('passwordInfo');
        const isVisible = passwordForm.style.display !== 'none';
        
        if (isVisible) {
            // Hide form
            passwordForm.style.display = 'none';
            passwordInfo.style.display = 'block';
            document.getElementById('editPasswordBtn').innerHTML = '<i class="fas fa-edit mr-1"></i>Change';
        } else {
            // Show form
            passwordForm.style.display = 'block';
            passwordInfo.style.display = 'none';
            document.getElementById('editPasswordBtn').innerHTML = '<i class="fas fa-times mr-1"></i>Cancel';
        }
    }
}

function cancelEditMode(section) {
    if (section === 'personal') {
        const fields = ['firstName', 'lastName', 'email', 'phone'];
        
        // Restore original values
        document.getElementById('firstName').value = originalPersonalData.first_name;
        document.getElementById('lastName').value = originalPersonalData.last_name;
        document.getElementById('email').value = originalPersonalData.email;
        document.getElementById('phone').value = originalPersonalData.phone;
        
        // Disable fields
        fields.forEach(field => {
            document.getElementById(field).disabled = true;
            document.getElementById(field).classList.add('disabled:bg-gray-50', 'disabled:text-gray-500');
        });
        
        document.getElementById('personalFormActions').style.display = 'none';
        document.getElementById('editPersonalBtn').innerHTML = '<i class="fas fa-edit mr-1"></i>Edit';
    } else if (section === 'password') {
        document.getElementById('passwordForm').style.display = 'none';
        document.getElementById('passwordInfo').style.display = 'block';
        document.getElementById('editPasswordBtn').innerHTML = '<i class="fas fa-edit mr-1"></i>Change';
        document.getElementById('passwordForm').reset();
    }
}

document.getElementById('personalInfoForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const updateData = {
        first_name: formData.get('firstName'),
        last_name: formData.get('lastName'),
        email: formData.get('email'),
        phone: formData.get('phone')
    };
    
    const saveButton = document.getElementById('savePersonalBtn');
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    
    try {
        // Note: We'll need to create an update profile endpoint in the API
        // For now, we'll simulate the call
        const response = await fetch('/Core1_ecommerce/customer/api/auth/update-profile', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updateData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Profile updated successfully', 'success');
            originalPersonalData = { ...updateData };
            cancelEditMode('personal');
            
            // Update sidebar
            document.getElementById('profileName').textContent = `${updateData.first_name} ${updateData.last_name}`.trim() || 'No Name';
            document.getElementById('profileEmail').textContent = updateData.email;
        } else {
            showToast(result.message || 'Failed to update profile', 'error');
        }
    } catch (error) {
        console.error('Failed to update profile:', error);
        showToast('Failed to update profile', 'error');
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Save Changes';
    }
});

document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const currentPassword = formData.get('currentPassword');
    const newPassword = formData.get('newPassword');
    const confirmPassword = formData.get('confirmPassword');
    
    if (newPassword !== confirmPassword) {
        showToast('New passwords do not match', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showToast('Password must be at least 6 characters long', 'error');
        return;
    }
    
    const saveButton = document.getElementById('savePasswordBtn');
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
    
    try {
        const response = await fetch('/Core1_ecommerce/customer/api/auth/change-password', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Password updated successfully', 'success');
            cancelEditMode('password');
        } else {
            showToast(result.message || 'Failed to update password', 'error');
        }
    } catch (error) {
        console.error('Failed to update password:', error);
        showToast('Failed to update password', 'error');
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Update Password';
    }
});

document.getElementById('preferencesForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const preferences = {
        order_updates: formData.get('orderUpdates') ? true : false,
        promotions: formData.get('promotions') ? true : false,
        newsletter: formData.get('newsletter') ? true : false,
        recommendations: formData.get('recommendations') ? true : false
    };
    
    const saveButton = document.getElementById('savePreferencesBtn');
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    
    try {
        // For now, we'll simulate saving preferences
        setTimeout(() => {
            showToast('Preferences saved successfully', 'success');
            saveButton.disabled = false;
            saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Save Preferences';
        }, 1000);
    } catch (error) {
        console.error('Failed to save preferences:', error);
        showToast('Failed to save preferences', 'error');
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Save Preferences';
    }
});

function confirmDeleteAccount() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
    document.getElementById('deleteAccountModal').classList.add('flex');
}

function hideDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
    document.getElementById('deleteAccountModal').classList.remove('flex');
}

async function deleteAccount() {
    try {
        const response = await fetch('/Core1_ecommerce/customer/api/auth/delete-account', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Account deleted successfully', 'success');
            setTimeout(() => {
                window.location.href = '../index.php';
            }, 2000);
        } else {
            showToast(result.message || 'Failed to delete account', 'error');
        }
    } catch (error) {
        console.error('Failed to delete account:', error);
        showToast('Failed to delete account', 'error');
    }
    
    hideDeleteAccountModal();
}

async function downloadData() {
    try {
        showToast('Preparing your data download...', 'info');
        
        // Simulate data preparation
        setTimeout(() => {
            const data = {
                profile: originalPersonalData,
                export_date: new Date().toISOString(),
                note: 'This is your personal data export from Core1 E-commerce'
            };
            
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `core1-ecommerce-data-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            showToast('Data download started', 'success');
        }, 2000);
    } catch (error) {
        console.error('Failed to download data:', error);
        showToast('Failed to prepare data download', 'error');
    }
}

function showToast(message, type = 'success') {
    customerAPI.utils.showNotification(message, type);
}

// Profile Image Functions
function updateProfileImageDisplay(imageUrl) {
    const profileImage = document.getElementById('profileImage');
    const profileImagePlaceholder = document.getElementById('profileImagePlaceholder');
    const modalCurrentImage = document.getElementById('modalCurrentImage');
    const modalCurrentPlaceholder = document.getElementById('modalCurrentPlaceholder');
    const deleteButton = document.getElementById('deleteImageButton');
    
    if (imageUrl) {
        profileImage.src = imageUrl;
        profileImage.style.display = 'block';
        profileImagePlaceholder.style.display = 'none';
        
        modalCurrentImage.src = imageUrl;
        modalCurrentImage.style.display = 'block';
        modalCurrentPlaceholder.style.display = 'none';
        
        deleteButton.style.display = 'block';
    } else {
        profileImage.style.display = 'none';
        profileImagePlaceholder.style.display = 'flex';
        
        modalCurrentImage.style.display = 'none';
        modalCurrentPlaceholder.style.display = 'flex';
        
        deleteButton.style.display = 'none';
    }
}

function openImageUploadModal() {
    document.getElementById('imageUploadModal').classList.remove('hidden');
    document.getElementById('imageUploadModal').classList.add('flex');
    
    // Reset upload form
    document.getElementById('imageUpload').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'flex';
    document.getElementById('selectedImagePreview').style.display = 'none';
    document.getElementById('uploadButton').disabled = true;
    
    // Update modal with current image
    updateProfileImageDisplay(originalPersonalData.profile_image);
}

function closeImageUploadModal() {
    document.getElementById('imageUploadModal').classList.add('hidden');
    document.getElementById('imageUploadModal').classList.remove('flex');
}

function previewSelectedImage(input) {
    const file = input.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showToast('Invalid file type. Please select a JPG, PNG, GIF, or WebP image.', 'error');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showToast('File too large. Maximum size is 5MB.', 'error');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('selectedImagePreview').style.display = 'block';
            document.getElementById('uploadButton').disabled = false;
        };
        reader.readAsDataURL(file);
    }
}

async function uploadProfileImage() {
    const fileInput = document.getElementById('imageUpload');
    const file = fileInput.files[0];
    
    if (!file) {
        showToast('Please select an image to upload', 'error');
        return;
    }
    
    const uploadButton = document.getElementById('uploadButton');
    const originalText = uploadButton.innerHTML;
    uploadButton.disabled = true;
    uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    
    try {
        const formData = new FormData();
        formData.append('profile_image', file);
        
        const response = await fetch('/Core1_ecommerce/customer/api/auth/upload-profile-image', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Profile image updated successfully', 'success');
            originalPersonalData.profile_image = result.data.image_url;
            
            // Update all profile images globally (navbar + profile page)
            updateProfileImageDisplay(result.data.image_url);
            
            // Also update navbar if global function is available
            if (typeof window.updateGlobalProfileImage === 'function') {
                window.updateGlobalProfileImage(result.data.image_url);
            }
            
            // Fallback: trigger custom event for navbar
            window.dispatchEvent(new CustomEvent('profileImageUpdated', { 
                detail: { imageUrl: result.data.image_url }
            }));
            
            closeImageUploadModal();
        } else {
            showToast(result.message || 'Failed to upload image', 'error');
        }
    } catch (error) {
        console.error('Failed to upload image:', error);
        showToast('Failed to upload image', 'error');
    } finally {
        uploadButton.disabled = false;
        uploadButton.innerHTML = originalText;
    }
}

async function deleteProfileImage() {
    if (!confirm('Are you sure you want to remove your profile picture?')) {
        return;
    }
    
    try {
        const response = await fetch('/Core1_ecommerce/customer/api/auth/delete-profile-image', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Profile image removed successfully', 'success');
            originalPersonalData.profile_image = null;
            
            // Update all profile images globally (navbar + profile page)
            updateProfileImageDisplay(null);
            
            // Also update navbar if global function is available
            if (typeof window.updateGlobalProfileImage === 'function') {
                window.updateGlobalProfileImage(null);
            }
            
            // Fallback: trigger custom event for navbar
            window.dispatchEvent(new CustomEvent('profileImageUpdated', { 
                detail: { imageUrl: null }
            }));
            
            closeImageUploadModal();
        } else {
            showToast(result.message || 'Failed to remove image', 'error');
        }
    } catch (error) {
        console.error('Failed to delete image:', error);
        showToast('Failed to remove image', 'error');
    }
}
</script>

</body>
</html>