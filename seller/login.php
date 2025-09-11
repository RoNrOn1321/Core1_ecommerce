<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Login - Lumino Ecommerce</title>
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
<body class="bg-gray-50 font-sans min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-beige rounded-full mb-4">
                <i class="fas fa-store text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">
                Lumino<span class="text-beige">Shop</span>
            </h1>
            <p class="text-gray-600 mt-2">Seller Portal</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 text-center">Welcome Back</h2>
                <p class="text-gray-600 text-center mt-2">Sign in to your seller account</p>
            </div>

            <form id="loginForm" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" required 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                               placeholder="Enter your email">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                               class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                               placeholder="Enter your password">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-beige border-gray-300 rounded focus:ring-beige">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>
                    <a href="#" class="text-sm text-beige hover:text-beige-dark">Forgot password?</a>
                </div>

                <button type="submit" class="w-full btn-beige py-3 flex items-center justify-center">
                    <span id="loginText">Sign In</span>
                    <div id="loginSpinner" class="hidden ml-2">
                        <div class="spinner w-5 h-5 border-2 border-white border-t-transparent"></div>
                    </div>
                </button>
            </form>

            <!-- Social Login -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-gray-500">Or continue with</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fab fa-google text-red-500 text-lg"></i>
                        <span class="ml-2">Google</span>
                    </button>
                    <button type="button" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fab fa-facebook text-blue-600 text-lg"></i>
                        <span class="ml-2">Facebook</span>
                    </button>
                </div>
            </div>

            <!-- Sign Up Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="font-medium text-beige hover:text-beige-dark">Start selling today</a>
                </p>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="mt-8 text-center">
            <div class="flex justify-center space-x-6 text-sm text-gray-600">
                <a href="#" class="hover:text-beige">Help Center</a>
                <a href="#" class="hover:text-beige">Privacy Policy</a>
                <a href="#" class="hover:text-beige">Terms of Service</a>
            </div>
            <p class="mt-4 text-xs text-gray-500">
                © 2024 lumino. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Success Message (Hidden by default) -->
    <div id="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Login successful! Redirecting...</span>
        </div>
    </div>

    <!-- Error Message (Hidden by default) -->
    <div id="errorMessage" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="errorText">Invalid email or password</span>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = togglePassword.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Form submission
        const loginForm = document.getElementById('loginForm');
        const loginText = document.getElementById('loginText');
        const loginSpinner = document.getElementById('loginSpinner');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        function showMessage(element, duration = 3000) {
            element.classList.remove('translate-x-full');
            setTimeout(() => {
                element.classList.add('translate-x-full');
            }, duration);
        }

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Show loading state
            loginText.textContent = 'Signing in...';
            loginSpinner.classList.remove('hidden');
            
            // Get form data
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simulate login process
            try {
                // This would normally be an API call
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // For demo purposes, check for demo credentials
                if (email === 'seller@demo.com' && password === 'password') {
                    // Success
                    showMessage(successMessage);
                    
                    // Redirect to dashboard after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 2000);
                } else {
                    // Error
                    showMessage(errorMessage);
                }
            } catch (error) {
                // Handle error
                document.getElementById('errorText').textContent = 'An error occurred. Please try again.';
                showMessage(errorMessage);
            } finally {
                // Reset button state
                loginText.textContent = 'Sign In';
                loginSpinner.classList.add('hidden');
            }
        });

        // Demo credentials helper
        document.addEventListener('DOMContentLoaded', () => {
            // Add demo credentials info
            const demoInfo = document.createElement('div');
            demoInfo.className = 'mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg';
            demoInfo.innerHTML = `
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                    <div class="text-sm">
                        <p class="font-medium text-blue-800">Demo Credentials:</p>
                        <p class="text-blue-700">Email: seller@demo.com</p>
                        <p class="text-blue-700">Password: password</p>
                    </div>
                </div>
            `;
            
            const form = document.querySelector('form');
            form.parentNode.insertBefore(demoInfo, form.nextSibling);
        });
    </script>
</body>
</html>