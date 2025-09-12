<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Core1 E-commerce</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50">

<?php 
// Redirect if already logged in
require_once 'auth/functions.php';
requireGuest();
?>

<?php include 'components/navbar.php'; ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 bg-amber-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-plus text-amber-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="login.php" class="font-medium text-amber-600 hover:text-amber-500">
                    sign in to your existing account
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" id="registerForm">
            <div class="rounded-md shadow-sm space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input id="firstName" name="firstName" type="text" autocomplete="given-name" required
                               class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm"
                               placeholder="First name">
                    </div>
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input id="lastName" name="lastName" type="text" autocomplete="family-name" required
                               class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm"
                               placeholder="Last name">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" autocapitalize="off" autocorrect="off" spellcheck="false" required
                           class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm"
                           placeholder="Enter your email address"
                           style="text-transform: none !important;">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number (Optional)</label>
                    <input id="phone" name="phone" type="tel" autocomplete="tel"
                           class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm"
                           placeholder="e.g., +639123456789">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm pr-10"
                               placeholder="Enter your password">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="passwordIcon"></i>
                        </button>
                    </div>
                    <div class="mt-1">
                        <div class="text-xs text-gray-500">
                            Password strength: <span id="passwordStrength" class="font-medium">Weak</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div id="passwordStrengthBar" class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <div class="relative">
                        <input id="confirmPassword" name="confirmPassword" type="password" autocomplete="new-password" required
                               class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm pr-10"
                               placeholder="Confirm your password">
                        <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="confirmPasswordIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required
                       class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-900">
                    I agree to the 
                    <a href="#" class="text-amber-600 hover:text-amber-500">Terms and Conditions</a>
                    and 
                    <a href="#" class="text-amber-600 hover:text-amber-500">Privacy Policy</a>
                </label>
            </div>

            <div>
                <button type="submit" id="registerButton" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-amber-500 group-hover:text-amber-400" aria-hidden="true"></i>
                    </span>
                    <span id="buttonText">Create Account</span>
                    <i id="loadingSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                </button>
            </div>

            <div class="text-center">
                <span class="text-sm text-gray-500">Already have an account? </span>
                <a href="login.php" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                    Sign in
                </a>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/customer-api.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const button = document.getElementById('registerButton');
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');
    const passwordStrength = document.getElementById('passwordStrength');
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const emailInput = document.getElementById('email');

    // Force email to lowercase as user types
    emailInput.addEventListener('input', function() {
        const cursorPos = this.selectionStart;
        this.value = this.value.toLowerCase();
        this.setSelectionRange(cursorPos, cursorPos);
    });

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        passwordIcon.className = type === 'password' ? 'fas fa-eye text-gray-400 hover:text-gray-600' : 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
    });

    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        confirmPasswordIcon.className = type === 'password' ? 'fas fa-eye text-gray-400 hover:text-gray-600' : 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
    });

    // Password strength indicator
    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        let strength = 0;
        let strengthText = 'Weak';
        let strengthColor = 'bg-red-500';
        let strengthWidth = '25%';

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        if (strength >= 4) {
            strengthText = 'Strong';
            strengthColor = 'bg-green-500';
            strengthWidth = '100%';
        } else if (strength >= 3) {
            strengthText = 'Good';
            strengthColor = 'bg-yellow-500';
            strengthWidth = '75%';
        } else if (strength >= 2) {
            strengthText = 'Fair';
            strengthColor = 'bg-orange-500';
            strengthWidth = '50%';
        }

        passwordStrength.textContent = strengthText;
        passwordStrengthBar.className = `${strengthColor} h-2 rounded-full transition-all duration-300`;
        passwordStrengthBar.style.width = strengthWidth;
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const terms = document.getElementById('terms').checked;

        // Validation
        if (!firstName || !lastName || !email || !password || !confirmPassword) {
            showToast('Please fill in all required fields', 'error');
            return;
        }

        if (!terms) {
            showToast('Please accept the terms and conditions', 'error');
            return;
        }

        if (password !== confirmPassword) {
            showToast('Passwords do not match', 'error');
            return;
        }

        if (password.length < 6) {
            showToast('Password must be at least 6 characters long', 'error');
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showToast('Please enter a valid email address', 'error');
            return;
        }

        // Disable button and show loading
        button.disabled = true;
        buttonText.textContent = 'Creating Account...';
        loadingSpinner.classList.remove('hidden');

        try {
            const response = await customerAPI.auth.register({
                email: email,
                password: password,
                first_name: firstName,
                last_name: lastName,
                phone: phone || null
            });

            if (response.success) {
                showToast('Account created successfully! Please check your email for verification.', 'success');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                showToast(response.message, 'error');
            }
        } catch (error) {
            showToast('Registration failed. Please try again.', 'error');
            console.error('Registration error:', error);
        } finally {
            // Re-enable button and hide loading
            button.disabled = false;
            buttonText.textContent = 'Create Account';
            loadingSpinner.classList.add('hidden');
        }
    });
});
</script>

</body>
</html>
