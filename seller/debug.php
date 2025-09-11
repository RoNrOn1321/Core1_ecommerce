<!DOCTYPE html>
<html>
<head>
    <title>Seller Authentication Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        button { padding: 10px 20px; margin: 5px; }
    </style>
</head>
<body>
    <h1>Seller Authentication Debug Tool</h1>
    
    <div class="section">
        <h3>Step 1: Test Direct Login</h3>
        <form method="post">
            <input type="email" name="test_email" placeholder="Seller Email" required style="padding: 8px; margin: 5px;">
            <input type="password" name="test_password" placeholder="Password" required style="padding: 8px; margin: 5px;">
            <button type="submit" name="test_login">Test Login</button>
        </form>
    </div>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/includes/auth.php';

    echo "<div class='section'>";
    echo "<h3>Current Session Status</h3>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? '<span class="success">Active</span>' : '<span class="error">Inactive</span>') . "</p>";
    echo "<pre>";
    var_dump($_SESSION);
    echo "</pre>";
    echo "</div>";

    $auth = new SellerAuth($pdo);
    
    // Test login if form submitted
    if (isset($_POST['test_login'])) {
        echo "<div class='section'>";
        echo "<h3>Login Test Results</h3>";
        
        $email = $_POST['test_email'];
        $password = $_POST['test_password'];
        
        echo "<p><strong>Testing login for:</strong> $email</p>";
        
        $result = $auth->login($email, $password);
        
        if ($result['success']) {
            echo "<p class='success'>✓ Login successful!</p>";
            echo "<pre>";
            var_dump($result);
            echo "</pre>";
            
            echo "<p><strong>Session after login:</strong></p>";
            echo "<pre>";
            var_dump($_SESSION);
            echo "</pre>";
        } else {
            echo "<p class='error'>✗ Login failed: " . $result['message'] . "</p>";
        }
        echo "</div>";
    }

    echo "<div class='section'>";
    echo "<h3>Auth Class Status</h3>";
    echo "<p><strong>Is Logged In:</strong> " . ($auth->isLoggedIn() ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "</p>";
    
    if ($auth->isLoggedIn()) {
        $seller = $auth->getCurrentSeller();
        echo "<p class='success'>✓ Current seller data found</p>";
        echo "<pre>";
        var_dump($seller);
        echo "</pre>";
    } else {
        echo "<p class='warning'>No current seller session</p>";
    }
    echo "</div>";

    // Test database connection and seller data
    echo "<div class='section'>";
    echo "<h3>Database Test</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM sellers WHERE status = 'approved'");
        $count = $stmt->fetch();
        echo "<p><strong>Approved sellers:</strong> " . $count['count'] . "</p>";
        
        $stmt = $pdo->query("
            SELECT s.id, s.store_name, u.email, s.status as seller_status, u.status as user_status 
            FROM sellers s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.status = 'approved' 
            ORDER BY s.id DESC 
            LIMIT 5
        ");
        $sellers = $stmt->fetchAll();
        
        echo "<p><strong>Recent approved sellers:</strong></p>";
        echo "<pre>";
        foreach ($sellers as $seller) {
            echo "ID: {$seller['id']}, Email: {$seller['email']}, Store: {$seller['store_name']}, Seller Status: {$seller['seller_status']}, User Status: {$seller['user_status']}\n";
        }
        echo "</pre>";
        
    } catch (Exception $e) {
        echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";

    // Test API endpoint
    echo "<div class='section'>";
    echo "<h3>API Test</h3>";
    echo "<button onclick='testAPI()'>Test /auth/me API</button>";
    echo "<div id='api-results'></div>";
    echo "</div>";

    // PHP Configuration
    echo "<div class='section'>";
    echo "<h3>PHP Session Configuration</h3>";
    echo "<p><strong>session.cookie_lifetime:</strong> " . ini_get('session.cookie_lifetime') . "</p>";
    echo "<p><strong>session.cookie_path:</strong> " . ini_get('session.cookie_path') . "</p>";
    echo "<p><strong>session.cookie_domain:</strong> " . ini_get('session.cookie_domain') . "</p>";
    echo "<p><strong>session.cookie_secure:</strong> " . (ini_get('session.cookie_secure') ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>session.cookie_httponly:</strong> " . (ini_get('session.cookie_httponly') ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>session.cookie_samesite:</strong> " . ini_get('session.cookie_samesite') . "</p>";
    echo "</div>";
    ?>

    <script>
        async function testAPI() {
            const resultsDiv = document.getElementById('api-results');
            resultsDiv.innerHTML = '<p>Testing API...</p>';
            
            try {
                const response = await fetch('http://localhost/Core1_ecommerce/seller/api/auth/me', {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                resultsDiv.innerHTML = `
                    <h4>API Response</h4>
                    <p><strong>Status:</strong> ${response.status}</p>
                    <p><strong>Success:</strong> ${response.ok ? '<span class="success">Yes</span>' : '<span class="error">No</span>'}</p>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                `;
            } catch (error) {
                resultsDiv.innerHTML = `
                    <h4>API Error</h4>
                    <p class="error">${error.message}</p>
                `;
            }
        }
    </script>
</body>
</html>