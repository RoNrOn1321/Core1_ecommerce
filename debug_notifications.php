<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔍 Core1 E-commerce Notification System Debug</h1>
    
    <?php
    // Set headers for JSON responses in API calls
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    echo "<div class='section info'>";
    echo "<h2>📊 1. Database Status</h2>";
    
    try {
        require_once 'customer/config/database.php';
        echo "<p>✅ Database connection: OK</p>";
        
        // Check notifications table
        $stmt = $pdo->query("SELECT COUNT(*) as total_notifications FROM user_notifications");
        $total = $stmt->fetch()['total_notifications'];
        echo "<p>📧 Total notifications in database: <strong>$total</strong></p>";
        
        // Check unread notifications by user
        $stmt = $pdo->query("
            SELECT 
                u.id, 
                u.first_name, 
                u.email, 
                COUNT(un.id) as unread_count 
            FROM users u 
            LEFT JOIN user_notifications un ON u.id = un.user_id AND un.is_read = 0 
            GROUP BY u.id 
            HAVING unread_count > 0 
            ORDER BY unread_count DESC
        ");
        $users = $stmt->fetchAll();
        
        if ($users) {
            echo "<h3>👥 Users with unread notifications:</h3>";
            echo "<table>";
            echo "<tr><th>User ID</th><th>Name</th><th>Email</th><th>Unread Count</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td><strong>" . $user['unread_count'] . "</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show recent notifications for user with most notifications
            $topUser = $users[0];
            $stmt = $pdo->prepare("
                SELECT type, title, message, created_at 
                FROM user_notifications 
                WHERE user_id = ? AND is_read = 0 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stmt->execute([$topUser['id']]);
            $notifications = $stmt->fetchAll();
            
            echo "<h3>🔔 Recent unread notifications for " . htmlspecialchars($topUser['first_name']) . ":</h3>";
            echo "<table>";
            echo "<tr><th>Type</th><th>Title</th><th>Message</th><th>Created</th></tr>";
            foreach ($notifications as $notif) {
                echo "<tr>";
                echo "<td><span style='background: #007bff; color: white; padding: 2px 6px; border-radius: 3px; font-size: 12px;'>" . htmlspecialchars($notif['type']) . "</span></td>";
                echo "<td>" . htmlspecialchars($notif['title']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($notif['message'], 0, 100)) . "...</td>";
                echo "<td>" . htmlspecialchars($notif['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>⚠️ No users have unread notifications</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "</div>";
    
    // Test API endpoints
    echo "<div class='section info'>";
    echo "<h2>🔌 2. API Endpoint Tests</h2>";
    
    // Test without authentication
    echo "<h3>Test without authentication (should fail):</h3>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/Core1_ecommerce/customer/api/notifications/count');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTP Status: <strong>$httpCode</strong></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    echo "</div>";
    
    // Customer login instructions
    echo "<div class='section success'>";
    echo "<h2>✅ 3. How to Test Notifications</h2>";
    echo "<h3>Step-by-step instructions:</h3>";
    echo "<ol>";
    echo "<li>📱 Go to: <strong><a href='http://localhost/Core1_ecommerce/customer/login.php' target='_blank'>Customer Login Page</a></strong></li>";
    
    if (isset($topUser)) {
        echo "<li>🔑 Login with these credentials:";
        echo "<ul>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($topUser['email']) . "</li>";
        echo "<li><strong>Password:</strong> (You need to know the password or create a test account)</li>";
        echo "</ul></li>";
    }
    
    echo "<li>🏠 After login, go to the customer dashboard</li>";
    echo "<li>🔔 Look at the top-right corner for a <strong>bell icon</strong></li>";
    echo "<li>👀 You should see a <strong>red badge</strong> with the number of unread notifications</li>";
    echo "<li>🖱️ <strong>Click the bell icon</strong> to see the notification dropdown</li>";
    echo "<li>📄 Or visit: <strong><a href='http://localhost/Core1_ecommerce/customer/account/notifications.php' target='_blank'>Full Notifications Page</a></strong></li>";
    echo "</ol>";
    echo "</div>";
    
    // Create test account option
    echo "<div class='section warning'>";
    echo "<h2>🆕 4. Create Test Account (if needed)</h2>";
    echo "<p>If you don't have login credentials, you can:</p>";
    echo "<ol>";
    echo "<li>📝 Go to: <strong><a href='http://localhost/Core1_ecommerce/customer/register.php' target='_blank'>Customer Registration</a></strong></li>";
    echo "<li>✍️ Create a new account with any email/password</li>";
    echo "<li>📞 Then contact support to generate test notifications for your new account</li>";
    echo "</ol>";
    echo "</div>";
    
    // Admin instructions
    echo "<div class='section info'>";
    echo "<h2>👨‍💼 5. Admin Side - Creating Notifications</h2>";
    echo "<p>To create new notifications as admin:</p>";
    echo "<ol>";
    echo "<li>🔐 Login to admin panel: <strong><a href='http://localhost/Core1_ecommerce/admin/login.php' target='_blank'>Admin Login</a></strong></li>";
    echo "<li>🎫 Go to Support Tickets section</li>";
    echo "<li>💬 Reply to any customer ticket</li>";
    echo "<li>✅ This will automatically create a notification for that customer</li>";
    echo "</ol>";
    echo "</div>";
    ?>
    
    <div class="section success">
        <h2>🔧 6. Troubleshooting</h2>
        <p>If notifications still don't appear:</p>
        <ul>
            <li>✅ Make sure you're logged into the <strong>customer portal</strong> (not admin)</li>
            <li>🔄 Try refreshing the page (notifications update every 20 seconds)</li>
            <li>🕵️ Check browser console for JavaScript errors (F12 → Console)</li>
            <li>🚪 Try logging out and logging back in</li>
            <li>🧹 Clear browser cache and cookies</li>
        </ul>
    </div>
</body>
</html>