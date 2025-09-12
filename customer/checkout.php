<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Core1 E-commerce</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50">

<?php 
// Require authentication
require_once 'auth/functions.php';
requireLogin();
?>

<?php include 'components/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <a href="index.php" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-home"></i>
                            <span class="sr-only">Home</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-4"></i>
                            <span class="text-gray-700 font-medium">Checkout</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
            <!-- Order Form -->
            <div class="lg:col-span-7">
                <form id="checkoutForm" class="space-y-6">
                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-map-marker-alt text-amber-600 mr-2"></i>
                                Shipping Address
                            </h2>
                            <button type="button" onclick="toggleAddressMode()" id="addressModeBtn"
                                    class="text-sm text-amber-600 hover:text-amber-700 font-medium">
                                <i class="fas fa-plus mr-1"></i>
                                Add New Address
                            </button>
                        </div>
                        
                        <!-- Saved Addresses -->
                        <div id="savedAddresses" class="space-y-3 mb-4">
                            <!-- Saved addresses will be loaded here -->
                        </div>
                        
                        <!-- Address Form -->
                        <div id="addressForm" style="display: none;">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="fullName" name="fullName" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                                    <input type="text" id="address" name="address" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <input type="text" id="city" name="city" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                    <input type="text" id="postalCode" name="postalCode" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                    <input type="text" id="province" name="province" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="saveAddress" name="saveAddress" 
                                               class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded mr-2">
                                        <span class="text-sm text-gray-700">Save this address for future orders</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-credit-card text-amber-600 mr-2"></i>
                            Payment Method
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="cod" name="paymentMethod" type="radio" value="cod" checked
                                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                <label for="cod" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                    Cash on Delivery
                                </label>
                            </div>
                            <div class="flex items-center opacity-50">
                                <input id="card" name="paymentMethod" type="radio" value="card" disabled
                                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                <label for="card" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                                    Credit/Debit Card (Coming Soon)
                                </label>
                            </div>
                            <div class="flex items-center opacity-50">
                                <input id="gcash" name="paymentMethod" type="radio" value="gcash" disabled
                                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                <label for="gcash" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-mobile-alt text-blue-500 mr-2"></i>
                                    GCash (Coming Soon)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-sticky-note text-amber-600 mr-2"></i>
                            Order Notes (Optional)
                        </h2>
                        <textarea id="orderNotes" name="orderNotes" rows="3" 
                                  placeholder="Any special instructions for your order..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"></textarea>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-5 mt-8 lg:mt-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-shopping-cart text-amber-600 mr-2"></i>
                        Order Summary
                    </h2>
                    
                    <!-- Cart Items -->
                    <div id="checkoutItems" class="space-y-4 mb-6">
                        <!-- Items will be loaded here -->
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">₱<span id="subtotal">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-gray-900">₱<span id="shipping">50.00</span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="text-gray-900">₱<span id="tax">0.00</span></span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between text-base font-semibold">
                                <span class="text-gray-900">Order Total</span>
                                <span class="text-amber-600">₱<span id="total">0.00</span></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Place Order Button -->
                    <button type="button" onclick="placeOrder()" id="placeOrderBtn"
                            class="w-full bg-amber-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors mt-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        Place Order
                    </button>
                    
                    <p class="text-xs text-gray-500 text-center mt-4">
                        By placing your order, you agree to our Terms of Service and Privacy Policy.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/customer-api.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCheckoutData();
});

let selectedAddressId = null;
let savedAddresses = [];
let isNewAddressMode = false;

async function loadCheckoutData() {
    try {
        // Load cart items
        const response = await customerAPI.cart.getItems();
        if (response.success && response.data) {
            renderCheckoutItems(response.data);
            updateOrderTotals(response.summary);
        } else {
            showToast('Your cart is empty. Redirecting to products...', 'warning');
            setTimeout(() => {
                window.location.href = 'products.php';
            }, 2000);
        }
        
        // Load user information
        const profileResponse = await customerAPI.auth.getProfile();
        if (profileResponse.success && profileResponse.customer) {
            const customer = profileResponse.customer;
            document.getElementById('fullName').value = `${customer.first_name} ${customer.last_name}`;
            document.getElementById('phone').value = customer.phone || '';
        }
        
        // Load saved addresses
        await loadSavedAddresses();
        
    } catch (error) {
        console.error('Failed to load checkout data:', error);
        showToast('Failed to load checkout data', 'error');
    }
}

function renderCheckoutItems(items) {
    const container = document.getElementById('checkoutItems');
    
    if (items.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-4">No items in cart</p>';
        return;
    }

    const itemsHTML = items.map(item => `
        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
            <div class="flex items-center space-x-3">
                <img src="${getItemImage(item.image)}" 
                     alt="${item.name}" class="w-12 h-12 object-cover rounded">
                <div>
                    <p class="font-medium text-gray-900 text-sm">${item.name}</p>
                    <p class="text-xs text-gray-500">₱${item.price.toFixed(2)} × ${item.quantity}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="font-semibold text-gray-900">₱${item.subtotal.toFixed(2)}</span>
            </div>
        </div>
    `).join('');

    container.innerHTML = itemsHTML;
}

function updateOrderTotals(summary) {
    const subtotal = summary.total_amount || 0;
    const shipping = 50.00;
    const tax = 0.00;
    const total = subtotal + shipping + tax;
    
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('shipping').textContent = shipping.toFixed(2);
    document.getElementById('tax').textContent = tax.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
}

function getItemImage(imageUrl) {
    if (!imageUrl) return 'images/no-image.png';
    
    if (imageUrl.startsWith('http') || imageUrl.startsWith('/')) {
        return imageUrl;
    }
    
    return `/Core1_ecommerce/uploads/${imageUrl}`;
}

async function loadSavedAddresses() {
    try {
        const response = await customerAPI.addresses.getAll();
        if (response.success && response.data) {
            savedAddresses = response.data;
            renderSavedAddresses();
        }
    } catch (error) {
        console.error('Failed to load addresses:', error);
    }
}

function renderSavedAddresses() {
    const container = document.getElementById('savedAddresses');
    
    if (savedAddresses.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500">No saved addresses. Add a new address below.</p>';
        showAddressForm();
        return;
    }

    const addressesHTML = savedAddresses.map(address => `
        <div class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors ${selectedAddressId === address.id ? 'border-amber-500 bg-amber-50' : ''}"
             onclick="selectAddress(${address.id})">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <input type="radio" name="selectedAddress" value="${address.id}" 
                               ${selectedAddressId === address.id ? 'checked' : ''}
                               class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 mr-3">
                        <span class="font-medium text-gray-900">${address.full_name || 'No Name'}</span>
                        ${address.is_default ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Default</span>' : ''}
                    </div>
                    <p class="text-sm text-gray-600 ml-7">${address.full_address}</p>
                    <p class="text-sm text-gray-600 ml-7">${address.phone || 'No phone'}</p>
                </div>
                <button type="button" onclick="event.stopPropagation(); editAddress(${address.id})" 
                        class="text-sm text-gray-400 hover:text-gray-600">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = addressesHTML;
    
    // Auto-select default address or first address
    if (!selectedAddressId && savedAddresses.length > 0) {
        const defaultAddress = savedAddresses.find(addr => addr.is_default);
        const addressToSelect = defaultAddress || savedAddresses[0];
        selectAddress(addressToSelect.id);
    }
}

function selectAddress(addressId) {
    selectedAddressId = addressId;
    const address = savedAddresses.find(addr => addr.id === addressId);
    
    if (address) {
        // Update form fields (hidden when using saved address)
        document.getElementById('fullName').value = address.full_name || '';
        document.getElementById('address').value = address.address_line_1 || '';
        document.getElementById('city').value = address.city || '';
        document.getElementById('province').value = address.state || '';
        document.getElementById('postalCode').value = address.postal_code || '';
        document.getElementById('phone').value = address.phone || '';
        
        // Hide address form when address is selected
        hideAddressForm();
        
        // Re-render to update selection
        renderSavedAddresses();
    }
}

function toggleAddressMode() {
    if (isNewAddressMode) {
        hideAddressForm();
    } else {
        showAddressForm();
    }
}

function showAddressForm() {
    isNewAddressMode = true;
    selectedAddressId = null;
    document.getElementById('addressForm').style.display = 'block';
    document.getElementById('addressModeBtn').innerHTML = '<i class="fas fa-times mr-1"></i>Cancel';
    
    // Clear form
    document.getElementById('fullName').value = '';
    document.getElementById('address').value = '';
    document.getElementById('city').value = '';
    document.getElementById('province').value = '';
    document.getElementById('postalCode').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('saveAddress').checked = false;
    
    // Re-render addresses to clear selection
    renderSavedAddresses();
}

function hideAddressForm() {
    isNewAddressMode = false;
    document.getElementById('addressForm').style.display = 'none';
    document.getElementById('addressModeBtn').innerHTML = '<i class="fas fa-plus mr-1"></i>Add New Address';
}

async function editAddress(addressId) {
    const address = savedAddresses.find(addr => addr.id === addressId);
    if (!address) return;
    
    // Show form with address data
    showAddressForm();
    
    // Populate form with address data
    const nameParts = (address.full_name || '').split(' ');
    document.getElementById('fullName').value = address.full_name || '';
    document.getElementById('address').value = address.address_line_1 || '';
    document.getElementById('city').value = address.city || '';
    document.getElementById('province').value = address.state || '';
    document.getElementById('postalCode').value = address.postal_code || '';
    document.getElementById('phone').value = address.phone || '';
    
    // Set editing mode
    selectedAddressId = addressId;
}

async function placeOrder() {
    const form = document.getElementById('checkoutForm');
    const button = document.getElementById('placeOrderBtn');
    
    // Validate address selection
    if (!selectedAddressId && !isNewAddressMode) {
        showToast('Please select an address or add a new one', 'error');
        return;
    }
    
    // If using new address, validate form
    if (isNewAddressMode) {
        const requiredFields = ['fullName', 'address', 'city', 'province', 'phone'];
        for (const field of requiredFields) {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                element.focus();
                showToast(`${field.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())} is required`, 'error');
                return;
            }
        }
        
        // Save new address if requested
        const saveAddress = document.getElementById('saveAddress').checked;
        if (saveAddress) {
            try {
                const addressData = {
                    first_name: document.getElementById('fullName').value.split(' ')[0],
                    last_name: document.getElementById('fullName').value.split(' ').slice(1).join(' '),
                    address_line_1: document.getElementById('address').value,
                    city: document.getElementById('city').value,
                    state: document.getElementById('province').value,
                    postal_code: document.getElementById('postalCode').value,
                    phone: document.getElementById('phone').value,
                    is_default: savedAddresses.length === 0 // Set as default if it's the first address
                };
                
                await customerAPI.addresses.create(addressData);
                showToast('Address saved successfully', 'success');
            } catch (error) {
                console.error('Failed to save address:', error);
                // Continue with order even if address saving fails
            }
        }
    }
    
    // Get form data
    const formData = new FormData(form);
    const orderData = {
        shipping_address: {
            full_name: document.getElementById('fullName').value,
            address: document.getElementById('address').value,
            city: document.getElementById('city').value,
            postal_code: document.getElementById('postalCode').value,
            province: document.getElementById('province').value,
            phone: document.getElementById('phone').value
        },
        payment_method: formData.get('paymentMethod'),
        order_notes: formData.get('orderNotes') || null
    };
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Placing Order...';
    
    try {
        const response = await customerAPI.orders.create(orderData);
        
        if (response.success) {
            showToast('Order placed successfully! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = `account/orders.php?order=${response.data.order_id}`;
            }, 2000);
        } else {
            showToast(response.message || 'Failed to place order', 'error');
        }
        
    } catch (error) {
        console.error('Order placement failed:', error);
        showToast('Failed to place order. Please try again.', 'error');
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Place Order';
    }
}

// Toast notification function
function showToast(message, type = 'success') {
    // Create toast if it doesn't exist
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'fixed bottom-6 right-6 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-full opacity-0';
        document.body.appendChild(toast);
    }
    
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
        case 'warning':
            toast.classList.add('bg-yellow-500', 'text-white');
            break;
        default:
            toast.classList.add('bg-green-500', 'text-white');
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
}
</script>

</body>
</html>