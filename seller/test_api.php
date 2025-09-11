<!DOCTYPE html>
<html>
<head>
    <title>Seller API Test</title>
</head>
<body>
    <h2>Seller API Test</h2>
    <button onclick="testCurrentSeller()">Test Current Seller API</button>
    <button onclick="testLogin()">Test Login</button>
    <div id="results"></div>

    <script>
        async function testCurrentSeller() {
            try {
                const response = await fetch('http://localhost/Core1_ecommerce/seller/api/auth/me', {
                    credentials: 'include'
                });
                const data = await response.json();
                
                document.getElementById('results').innerHTML = `
                    <h3>Current Seller API Response:</h3>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Response:</strong></p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                document.getElementById('results').innerHTML = `
                    <h3>Error:</h3>
                    <pre>${error.message}</pre>
                `;
            }
        }

        async function testLogin() {
            const email = prompt('Enter email:');
            const password = prompt('Enter password:');
            
            if (!email || !password) return;
            
            try {
                const response = await fetch('http://localhost/Core1_ecommerce/seller/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();
                
                document.getElementById('results').innerHTML = `
                    <h3>Login API Response:</h3>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Response:</strong></p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
                
                // If login successful, test current seller
                if (data.success) {
                    setTimeout(testCurrentSeller, 1000);
                }
            } catch (error) {
                document.getElementById('results').innerHTML = `
                    <h3>Login Error:</h3>
                    <pre>${error.message}</pre>
                `;
            }
        }
    </script>
</body>
</html>