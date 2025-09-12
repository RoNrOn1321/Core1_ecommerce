<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Core1 E-commerce</title>

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
                <i class="fas fa-sign-in-alt text-amber-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="register.php" class="font-medium text-amber-600 hover:text-amber-500">
                    create a new account
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" id="loginForm">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" autocapitalize="off" autocorrect="off" spellcheck="false" required
                           class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm"
                           placeholder="Enter your email address"
                           style="text-transform: none !important;">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent focus:z-10 sm:text-sm pr-10"
                               placeholder="Enter your password">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox"
                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="forgot-password.php" class="font-medium text-amber-600 hover:text-amber-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" id="loginButton" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-amber-500 group-hover:text-amber-400" aria-hidden="true"></i>
                    </span>
                    <span id="buttonText">Sign in</span>
                    <i id="loadingSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                </button>
            </div>

            <div class="text-center">
                <span class="text-sm text-gray-500">Don't have an account? </span>
                <a href="register.php" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                    Sign up now
                </a>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/customer-api.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const button = document.getElementById('loginButton');
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
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

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            showToast('Please fill in all fields', 'error');
            return;
        }

        // Disable button and show loading
        button.disabled = true;
        buttonText.textContent = 'Signing in...';
        loadingSpinner.classList.remove('hidden');

        try {
            const response = await customerAPI.auth.login(email, password);

            if (response.success) {
                showToast('Login successful! Redirecting...', 'success');
                
                // Check for redirect parameter
                const urlParams = new URLSearchParams(window.location.search);
                const redirectTo = urlParams.get('redirect');
                const redirectUrl = redirectTo === 'checkout' ? 'checkout.php' : 'index.php';
                
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1500);
            } else {
                showToast(response.message, 'error');
            }
        } catch (error) {
            showToast('Login failed. Please try again.', 'error');
            console.error('Login error:', error);
        } finally {
            // Re-enable button and hide loading
            button.disabled = false;
            buttonText.textContent = 'Sign in';
            loadingSpinner.classList.add('hidden');
        }
    });
});
</script>

<?php include 'components/footer.php'; ?>

</body>
</html>
