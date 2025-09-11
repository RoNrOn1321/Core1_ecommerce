<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Seller Dashboard - Lumino Ecommerce</title>
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
    <!-- Auth Check & Redirect -->
    <div id="authCheck" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-lock text-4xl text-red-500 mb-4"></i>
                <h2 class="text-xl font-bold text-gray-800 mb-4">Authentication Required</h2>
                <p class="text-gray-600 mb-6">Please log in to access your seller dashboard.</p>
                <button onclick="redirectToLogin()" class="btn-beige w-full">
                    Go to Login
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-40">
        <div class="bg-white p-8 rounded-lg shadow-xl">
            <div class="text-center">
                <div class="spinner mb-4"></div>
                <p class="text-gray-600">Loading<?php echo isset($page_title) ? ' ' . strtolower($page_title) : ''; ?>...</p>
            </div>
        </div>
    </div>

    <!-- API Status Indicator -->
    <div id="apiStatus" class="fixed top-4 right-4 z-30">
        <div class="api-status offline">
            <div class="status-dot"></div>
            <span>API Offline</span>
        </div>
    </div>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-md z-50 h-20">
        <div class="flex items-center justify-between px-6 h-full">
            <!-- Logo -->
            <div class="flex items-center">
                <button id="sidebarToggle" class="mr-4 lg:hidden">
                    <i class="fas fa-bars text-2xl text-gray-700 hover:text-beige transition-colors"></i>
                </button>
                <div class="logo text-3xl font-bold text-gray-800">
                    Lumino<span class="text-beige">Shop</span>
                </div>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="text-gray-700 hover:text-beige transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notificationCount" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </button>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileDropdown" class="flex items-center space-x-2 text-gray-700 hover:text-beige transition-colors">
                        <img id="profileImage" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
                             alt="Profile" class="w-8 h-8 rounded-full">
                        <span id="profileName" class="hidden md:block">Loading...</span>
                        <i class="fas fa-chevron-down text-sm"></i>
                    </button>
                    <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                        <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <button onclick="logout()" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>