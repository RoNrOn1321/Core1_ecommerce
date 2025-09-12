<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Addresses - Core1 E-commerce</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Addresses</h1>
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
                            <span class="text-gray-700 font-medium">Addresses</span>
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
                <a href="addresses.php" class="flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Addresses
                </a>
                <a href="profile.php" class="flex items-center px-4 py-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                    <i class="fas fa-user-edit mr-2"></i>
                    Profile
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Addresses List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-map-marker-alt text-amber-600 mr-2"></i>
                            Saved Addresses
                        </h2>
                        <button onclick="showAddAddressForm()" 
                                class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add New Address
                        </button>
                    </div>
                    
                    <!-- Addresses Container -->
                    <div id="addressesList" class="space-y-4">
                        <!-- Addresses will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Add/Edit Address Form -->
            <div class="lg:col-span-1">
                <div id="addressFormCard" class="bg-white rounded-lg shadow-sm p-6" style="display: none;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="formTitle">Add New Address</h3>
                        <button onclick="hideAddressForm()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="addressForm" class="space-y-4">
                        <input type="hidden" id="addressId" name="addressId">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" id="firstName" name="firstName" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" id="lastName" name="lastName" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label for="addressLine1" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                            <input type="text" id="addressLine1" name="addressLine1" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="addressLine2" class="block text-sm font-medium text-gray-700 mb-1">Apartment, suite, etc. (optional)</label>
                            <input type="text" id="addressLine2" name="addressLine2"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" id="city" name="city" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                <input type="text" id="state" name="state" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" id="postalCode" name="postalCode"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="tel" id="phone" name="phone"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label for="addressType" class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                            <select id="addressType" name="addressType" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                <option value="home">Home</option>
                                <option value="office">Office</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="label" class="block text-sm font-medium text-gray-700 mb-1">Label (optional)</label>
                            <input type="text" id="label" name="label" placeholder="e.g., Main Office, Mom's House"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        </div>
                        
                        <!-- Map Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Location on Map (optional)
                            </label>
                            <div class="mb-2">
                                <input type="text" id="mapSearch" placeholder="Search for location..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <div id="addressMap" class="h-48 border border-gray-300 rounded-lg mb-2"></div>
                            <button type="button" id="getCurrentLocation" 
                                    class="text-sm text-amber-600 hover:text-amber-700">
                                <i class="fas fa-crosshairs mr-1"></i>Use my current location
                            </button>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="isDefault" name="isDefault" 
                                   class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded mr-2">
                            <label for="isDefault" class="text-sm text-gray-700">Set as default address</label>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="submit" id="saveAddressBtn"
                                    class="flex-1 bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Address
                            </button>
                            <button type="button" onclick="hideAddressForm()"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-sm w-full">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">Delete Address</h3>
        </div>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this address? This action cannot be undone.</p>
        <div class="flex gap-3">
            <button id="confirmDeleteBtn" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                Delete
            </button>
            <button onclick="hideDeleteModal()" class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="../assets/js/customer-api.js"></script>
<script>
let addresses = [];
let editingAddressId = null;
let map = null;
let currentMarker = null;
const LOCATIONIQ_API_KEY = 'pk.d94f7afe5f777d19a9a33c823b40550e';
const DEFAULT_LAT = 8.6220221;
const DEFAULT_LNG = 123.68469;

document.addEventListener('DOMContentLoaded', function() {
    loadAddresses();
});

async function loadAddresses() {
    try {
        const response = await customerAPI.addresses.getAll();
        if (response.success) {
            addresses = response.data;
            renderAddresses();
        } else {
            showToast(response.message || 'Failed to load addresses', 'error');
        }
    } catch (error) {
        console.error('Failed to load addresses:', error);
        showToast('Failed to load addresses', 'error');
    }
}

function renderAddresses() {
    const container = document.getElementById('addressesList');
    
    if (addresses.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No addresses saved</h3>
                <p class="text-gray-500 mb-4">Add your first address to get started with faster checkout.</p>
                <button onclick="showAddAddressForm()" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Address
                </button>
            </div>
        `;
        return;
    }

    const addressesHTML = addresses.map(address => `
        <div class="border border-gray-200 rounded-lg p-4 ${address.is_default ? 'bg-amber-50 border-amber-200' : ''}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <h4 class="font-medium text-gray-900">${address.full_name || 'No Name'}</h4>
                        ${address.is_default ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Default</span>' : ''}
                        ${address.label ? `<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">${address.label}</span>` : ''}
                    </div>
                    <p class="text-gray-600 text-sm">${address.full_address}</p>
                    ${address.phone ? `<p class="text-gray-600 text-sm mt-1"><i class="fas fa-phone mr-1"></i>${address.phone}</p>` : ''}
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    ${!address.is_default ? `<button onclick="setDefaultAddress(${address.id})" class="text-sm text-amber-600 hover:text-amber-700 px-2 py-1 rounded" title="Set as default">
                        <i class="fas fa-star"></i>
                    </button>` : ''}
                    <button onclick="editAddress(${address.id})" class="text-sm text-blue-600 hover:text-blue-700 px-2 py-1 rounded" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteAddress(${address.id})" class="text-sm text-red-600 hover:text-red-700 px-2 py-1 rounded" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = addressesHTML;
}

function showAddAddressForm() {
    editingAddressId = null;
    document.getElementById('formTitle').textContent = 'Add New Address';
    document.getElementById('addressForm').reset();
    document.getElementById('addressId').value = '';
    document.getElementById('addressFormCard').style.display = 'block';
    
    // Initialize map
    setTimeout(initializeMap, 100);
}

function hideAddressForm() {
    document.getElementById('addressFormCard').style.display = 'none';
    editingAddressId = null;
    if (map) {
        map.remove();
        map = null;
    }
}

async function editAddress(addressId) {
    try {
        const response = await customerAPI.addresses.getById(addressId);
        if (response.success) {
            const address = response.data;
            editingAddressId = addressId;
            
            document.getElementById('formTitle').textContent = 'Edit Address';
            document.getElementById('addressId').value = addressId;
            document.getElementById('firstName').value = address.first_name || '';
            document.getElementById('lastName').value = address.last_name || '';
            document.getElementById('addressLine1').value = address.address_line_1 || '';
            document.getElementById('addressLine2').value = address.address_line_2 || '';
            document.getElementById('city').value = address.city || '';
            document.getElementById('state').value = address.state || '';
            document.getElementById('postalCode').value = address.postal_code || '';
            document.getElementById('phone').value = address.phone || '';
            document.getElementById('addressType').value = address.type || 'home';
            document.getElementById('label').value = address.label || '';
            document.getElementById('isDefault').checked = address.is_default;
            
            document.getElementById('addressFormCard').style.display = 'block';
            
            // Initialize map with address location
            setTimeout(() => {
                initializeMap();
                if (address.latitude && address.longitude) {
                    const lat = parseFloat(address.latitude);
                    const lng = parseFloat(address.longitude);
                    map.setView([lat, lng], 16);
                    currentMarker = L.marker([lat, lng]).addTo(map);
                }
            }, 100);
        }
    } catch (error) {
        console.error('Failed to load address:', error);
        showToast('Failed to load address details', 'error');
    }
}

async function setDefaultAddress(addressId) {
    try {
        const response = await customerAPI.addresses.setDefault(addressId);
        if (response.success) {
            showToast('Default address updated', 'success');
            await loadAddresses();
        } else {
            showToast(response.message || 'Failed to set default address', 'error');
        }
    } catch (error) {
        console.error('Failed to set default address:', error);
        showToast('Failed to set default address', 'error');
    }
}

let addressToDelete = null;

function deleteAddress(addressId) {
    addressToDelete = addressId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    addressToDelete = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!addressToDelete) return;
    
    try {
        const response = await customerAPI.addresses.delete(addressToDelete);
        if (response.success) {
            showToast('Address deleted successfully', 'success');
            await loadAddresses();
        } else {
            showToast(response.message || 'Failed to delete address', 'error');
        }
    } catch (error) {
        console.error('Failed to delete address:', error);
        showToast('Failed to delete address', 'error');
    } finally {
        hideDeleteModal();
    }
});

document.getElementById('addressForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const addressData = {
        first_name: formData.get('firstName'),
        last_name: formData.get('lastName'),
        address_line_1: formData.get('addressLine1'),
        address_line_2: formData.get('addressLine2') || null,
        city: formData.get('city'),
        state: formData.get('state'),
        postal_code: formData.get('postalCode') || null,
        phone: formData.get('phone') || null,
        type: formData.get('addressType'),
        label: formData.get('label') || null,
        is_default: formData.get('isDefault') ? true : false
    };
    
    // Add coordinates if marker is present
    if (currentMarker) {
        const latlng = currentMarker.getLatLng();
        addressData.latitude = latlng.lat;
        addressData.longitude = latlng.lng;
    }
    
    const saveButton = document.getElementById('saveAddressBtn');
    saveButton.disabled = true;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    
    try {
        let response;
        if (editingAddressId) {
            response = await customerAPI.addresses.update(editingAddressId, addressData);
        } else {
            response = await customerAPI.addresses.create(addressData);
        }
        
        if (response.success) {
            showToast(editingAddressId ? 'Address updated successfully' : 'Address added successfully', 'success');
            hideAddressForm();
            await loadAddresses();
        } else {
            showToast(response.message || 'Failed to save address', 'error');
        }
    } catch (error) {
        console.error('Failed to save address:', error);
        showToast('Failed to save address', 'error');
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Save Address';
    }
});

function initializeMap() {
    if (map) {
        map.remove();
    }
    
    map = L.map('addressMap').setView([DEFAULT_LAT, DEFAULT_LNG], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    map.on('click', onMapClick);
    
    document.getElementById('mapSearch').addEventListener('input', debounce(searchLocation, 500));
    document.getElementById('getCurrentLocation').addEventListener('click', getCurrentLocation);
}

function onMapClick(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
    
    if (currentMarker) {
        map.removeLayer(currentMarker);
    }
    
    currentMarker = L.marker([lat, lng]).addTo(map);
    reverseGeocode(lat, lng);
}

async function reverseGeocode(lat, lng) {
    try {
        const response = await fetch(`https://us1.locationiq.com/v1/reverse.php?key=${LOCATIONIQ_API_KEY}&lat=${lat}&lon=${lng}&format=json&addressdetails=1`);
        const data = await response.json();
        
        if (data && data.address) {
            const address = data.address;
            
            document.getElementById('addressLine1').value = 
                `${address.house_number || ''} ${address.road || address.street || ''} ${address.neighbourhood || ''} ${address.suburb || ''}`.trim();
            document.getElementById('city').value = address.city || address.town || address.municipality || address.village || '';
            document.getElementById('state').value = address.state || address.province || '';
            document.getElementById('postalCode').value = address.postcode || '';
            document.getElementById('mapSearch').value = data.display_name || '';
            
            showToast('Address updated from map location', 'success');
        }
    } catch (error) {
        console.error('Reverse geocoding failed:', error);
        showToast('Could not get address details. Please fill manually.', 'warning');
    }
}

async function searchLocation(query) {
    if (query.length < 3) return;
    
    try {
        const response = await fetch(`https://us1.locationiq.com/v1/search.php?key=${LOCATIONIQ_API_KEY}&q=${encodeURIComponent(query)}&format=json&limit=1&countrycodes=ph`);
        const data = await response.json();
        
        if (data && data.length > 0) {
            const result = data[0];
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);
            
            map.setView([lat, lng], 16);
            
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }
            
            currentMarker = L.marker([lat, lng]).addTo(map);
            
            const addressParts = result.display_name.split(', ');
            if (addressParts.length >= 3) {
                document.getElementById('addressLine1').value = addressParts[0] || '';
                document.getElementById('city').value = addressParts[1] || '';
                document.getElementById('state').value = addressParts[2] || '';
            }
        }
    } catch (error) {
        console.error('Location search failed:', error);
    }
}

function getCurrentLocation() {
    const button = document.getElementById('getCurrentLocation');
    
    if (!navigator.geolocation) {
        showToast('Geolocation is not supported by this browser', 'error');
        return;
    }
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Getting location...';
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            map.setView([lat, lng], 16);
            
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }
            
            currentMarker = L.marker([lat, lng]).addTo(map);
            reverseGeocode(lat, lng);
            
            button.innerHTML = '<i class="fas fa-crosshairs mr-1"></i>Use my current location';
            showToast('Location detected successfully', 'success');
        },
        (error) => {
            console.error('Geolocation error:', error);
            button.innerHTML = '<i class="fas fa-crosshairs mr-1"></i>Use my current location';
            showToast('Could not get your location', 'error');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
        }
    );
}

function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

function showToast(message, type = 'success') {
    customerAPI.utils.showNotification(message, type);
}
</script>

</body>
</html>