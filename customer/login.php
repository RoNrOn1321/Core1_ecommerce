<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Lumino</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- BoxIcons CDN -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-10 rounded-2xl shadow-lg w-96">
    <div class="text-center mb-6">
      <img src="images/logo1.png" alt="Lumino Logo" class="w-20 mx-auto">
      <h2 class="text-2xl font-bold mt-2">Login to Lumino</h2>
    </div>

    <!-- Error Message -->
    <p id="errorMsg" class="text-red-500 text-sm text-center mb-3 hidden">Invalid email or password</p>

    <form id="loginForm" class="space-y-5">
      <div>
        <input type="email" id="email" placeholder="Email" required
          class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
      </div>

      <div class="relative">
        <input type="password" id="password" placeholder="Password" required
          class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        <i id="togglePassword" class='bx bx-hide absolute right-3 top-3 text-gray-500 cursor-pointer'></i>
      </div>

      <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 transition">Login</button>
    </form>

    <div class="text-center mt-4 text-sm text-gray-600">
      <p>Don't have an account? <a href="register.html" class="text-green-500 hover:underline">Register</a></p>
      <p><a href="#" class="text-green-500 hover:underline">Forgot Password?</a></p>
    </div>
  </div>

  <script>
    // Eye toggle
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', () => {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      togglePassword.classList.toggle('bx-show');
      togglePassword.classList.toggle('bx-hide');
    });

    // Frontend login simulation
    const loginForm = document.querySelector('#loginForm');
    const errorMsg = document.querySelector('#errorMsg');

    // Sample frontend users with roles
    const users = [
      { email: 'admin@gmail.com', password: 'admin123', name: 'Admin', role: 'admin' },
      { email: 'user@gmail.com', password: 'user123', name: 'User', role: 'user' }
    ];

    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const email = document.querySelector('#email').value;
      const pass = document.querySelector('#password').value;

      const user = users.find(u => u.email === email && u.password === pass);

      if(user) {
        if(user.role === 'admin') {
          alert(`Welcome ${user.name}! Redirecting to Admin Dashboard...`);
          window.location.href = "http://localhost/core1-ecommerce/admin/index.html";
        } else if(user.role === 'user') {
          alert(`Welcome ${user.name}! Redirecting to Customer Dashboard...`);
          window.location.href = "customer/dashboard.html";
        }
      } else {
        errorMsg.classList.remove('hidden');
      }
    });
  </script>
</body>
</html>
