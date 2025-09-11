<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Registration - Lumino Ecommerce</title>
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
<body class="bg-gray-50 font-sans min-h-screen py-8">
    <div class="max-w-2xl w-full mx-auto px-4">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-beige rounded-full mb-4">
                <i class="fas fa-store text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">
                Lumino<span class="text-beige">Shop</span>
            </h1>
            <p class="text-gray-600 mt-2">Start Your Selling Journey</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 text-center">Create Your Seller Account</h2>
                <p class="text-gray-600 text-center mt-2">Join thousands of successful sellers</p>
            </div>

            <form id="registerForm" class="space-y-6">
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" id="firstName" name="firstName" required 
                                   class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                   placeholder="Enter your first name">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="lastName" name="lastName" required 
                                   class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                   placeholder="Enter your last name">
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                    <div class="space-y-4">
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
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <div class="relative">
                                <input type="tel" id="phone" name="phone" required 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                       placeholder="Enter your phone number">
                                <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Business Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="businessName" class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                            <input type="text" id="businessName" name="businessName" required 
                                   class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                   placeholder="Enter your business name">
                        </div>
                        <div>
                            <label for="businessType" class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
                            <select id="businessType" name="businessType" required 
                                    class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20">
                                <option value="">Select business type</option>
                                <option value="individual">Individual/Sole Proprietor</option>
                                <option value="llc">LLC</option>
                                <option value="corporation">Corporation</option>
                                <option value="partnership">Partnership</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Primary Category</label>
                            <select id="category" name="category" required 
                                    class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20">
                                <option value="">Select primary category</option>
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing & Fashion</option>
                                <option value="books">Books & Media</option>
                                <option value="home">Home & Garden</option>
                                <option value="sports">Sports & Outdoors</option>
                                <option value="beauty">Beauty & Personal Care</option>
                                <option value="automotive">Automotive</option>
                                <option value="toys">Toys & Games</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Business Address</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                            <input type="text" id="address" name="address" required 
                                   class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                   placeholder="Enter your street address">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" id="city" name="city" required 
                                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                       placeholder="City">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                <input type="text" id="state" name="state" required 
                                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                       placeholder="State">
                            </div>
                            <div>
                                <label for="zipCode" class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                                <input type="text" id="zipCode" name="zipCode" required 
                                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                       placeholder="ZIP">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Security</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required 
                                       class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                       placeholder="Create a password">
                                <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="text-xs text-gray-600">
                                    <div id="lengthCheck" class="flex items-center"><i class="fas fa-times text-red-500 w-4"></i> At least 8 characters</div>
                                    <div id="upperCheck" class="flex items-center"><i class="fas fa-times text-red-500 w-4"></i> One uppercase letter</div>
                                    <div id="numberCheck" class="flex items-center"><i class="fas fa-times text-red-500 w-4"></i> One number</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="confirmPassword" required 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-beige focus:ring-2 focus:ring-beige focus:ring-opacity-20" 
                                       placeholder="Confirm your password">
                                <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div id="passwordMatch" class="mt-2 text-xs text-gray-600 hidden">
                                <div class="flex items-center"><i class="fas fa-times text-red-500 w-4"></i> Passwords must match</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start">
                    <input type="checkbox" id="terms" name="terms" required class="mt-1 h-4 w-4 text-beige border-gray-300 rounded focus:ring-beige">
                    <label for="terms" class="ml-3 text-sm text-gray-700">
                        I agree to the <a href="#" class="text-beige hover:text-beige-dark">Terms of Service</a> and 
                        <a href="#" class="text-beige hover:text-beige-dark">Privacy Policy</a>
                    </label>
                </div>

                <!-- Marketing Consent -->
                <div class="flex items-start">
                    <input type="checkbox" id="marketing" name="marketing" class="mt-1 h-4 w-4 text-beige border-gray-300 rounded focus:ring-beige">
                    <label for="marketing" class="ml-3 text-sm text-gray-700">
                        I would like to receive marketing communications and tips to help grow my business
                    </label>
                </div>

                <button type="submit" class="w-full btn-beige py-3 flex items-center justify-center">
                    <span id="registerText">Create Seller Account</span>
                    <div id="registerSpinner" class="hidden ml-2">
                        <div class="spinner w-5 h-5 border-2 border-white border-t-transparent"></div>
                    </div>
                </button>
            </form>

            <!-- Sign In Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="font-medium text-beige hover:text-beige-dark">Sign in</a>
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
                © 2024 LuminoShop. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Success Message -->
    <div id="successMessage" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Registration successful! Redirecting to login...</span>
        </div>
    </div>

    <!-- Error Message -->
    <div id="errorMessage" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="errorText">Please fix the errors below</span>
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

        // Password validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const lengthCheck = document.getElementById('lengthCheck');
        const upperCheck = document.getElementById('upperCheck');
        const numberCheck = document.getElementById('numberCheck');
        const passwordMatch = document.getElementById('passwordMatch');

        function updateCheck(element, isValid) {
            const icon = element.querySelector('i');
            if (isValid) {
                icon.classList.remove('fa-times', 'text-red-500');
                icon.classList.add('fa-check', 'text-green-500');
            } else {
                icon.classList.remove('fa-check', 'text-green-500');
                icon.classList.add('fa-times', 'text-red-500');
            }
        }

        password.addEventListener('input', () => {
            const value = password.value;
            updateCheck(lengthCheck, value.length >= 8);
            updateCheck(upperCheck, /[A-Z]/.test(value));
            updateCheck(numberCheck, /\d/.test(value));
        });

        confirmPassword.addEventListener('input', () => {
            if (confirmPassword.value) {
                const match = password.value === confirmPassword.value;
                passwordMatch.classList.toggle('hidden', match);
                if (!match) {
                    updateCheck(passwordMatch.querySelector('div'), false);
                }
            } else {
                passwordMatch.classList.add('hidden');
            }
        });

        // Form submission
        const registerForm = document.getElementById('registerForm');
        const registerText = document.getElementById('registerText');
        const registerSpinner = document.getElementById('registerSpinner');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        function showMessage(element, duration = 3000) {
            element.classList.remove('translate-x-full');
            setTimeout(() => {
                element.classList.add('translate-x-full');
            }, duration);
        }

        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Check password requirements
            if (password.length < 8 || !/[A-Z]/.test(password) || !/\d/.test(password)) {
                document.getElementById('errorText').textContent = 'Password does not meet requirements';
                return false;
            }
            
            // Check password match
            if (password !== confirmPassword) {
                document.getElementById('errorText').textContent = 'Passwords do not match';
                return false;
            }
            
            return true;
        }

        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!validateForm()) {
                showMessage(errorMessage);
                return;
            }
            
            // Show loading state
            registerText.textContent = 'Creating Account...';
            registerSpinner.classList.remove('hidden');
            
            try {
                // Simulate registration process
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // Success
                showMessage(successMessage, 2000);
                
                // Redirect to login after 2 seconds
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
                
            } catch (error) {
                // Handle error
                document.getElementById('errorText').textContent = 'Registration failed. Please try again.';
                showMessage(errorMessage);
            } finally {
                // Reset button state
                registerText.textContent = 'Create Seller Account';
                registerSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>