<?php
// Test database connection and API endpoints
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Seller Backend Connection Test</h1>";

// Test database connection
echo "<h2>1. Testing Database Connection</h2>";
try {
    require_once 'config/database.php';
    echo "✅ Database connection successful<br>";
    echo "Connected to database: core1_ecommerce<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test table existence
echo "<h2>2. Testing Table Structure</h2>";
$tables = ['users', 'sellers', 'products', 'orders', 'categories'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->prepare("DESCRIBE $table");
        $stmt->execute();
        echo "✅ Table '$table' exists<br>";
    } catch (Exception $e) {
        echo "❌ Table '$table' missing or error: " . $e->getMessage() . "<br>";
    }
}

// Test API endpoints
echo "<h2>3. Testing API Endpoints</h2>";
$baseUrl = 'http://localhost/Core1_ecommerce/seller/api';

// Test API info endpoint
$apiInfo = @file_get_contents($baseUrl . '/index.php');
if ($apiInfo) {
    echo "✅ API info endpoint working<br>";
} else {
    echo "❌ API info endpoint not accessible<br>";
}

// Check if we have test data
echo "<h2>4. Database Data Check</h2>";

// Check for sellers
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM sellers");
    $stmt->execute();
    $sellerCount = $stmt->fetch()['count'];
    echo "Sellers in database: $sellerCount<br>";
    
    if ($sellerCount == 0) {
        echo "<strong>⚠️ No sellers found. You need to register a seller account first.</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking sellers: " . $e->getMessage() . "<br>";
}

// Check for products
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products");
    $stmt->execute();
    $productCount = $stmt->fetch()['count'];
    echo "Products in database: $productCount<br>";
} catch (Exception $e) {
    echo "❌ Error checking products: " . $e->getMessage() . "<br>";
}

// Check for orders
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders");
    $stmt->execute();
    $orderCount = $stmt->fetch()['count'];
    echo "Orders in database: $orderCount<br>";
} catch (Exception $e) {
    echo "❌ Error checking orders: " . $e->getMessage() . "<br>";
}

// Check for categories
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM categories");
    $stmt->execute();
    $categoryCount = $stmt->fetch()['count'];
    echo "Categories in database: $categoryCount<br>";
    
    if ($categoryCount == 0) {
        echo "<strong>Creating sample categories...</strong><br>";
        
        $categories = [
            ['Electronics', 'electronics', 'Electronic devices and gadgets'],
            ['Clothing', 'clothing', 'Fashion and apparel'],
            ['Home & Garden', 'home-garden', 'Home improvement and garden supplies'],
            ['Books', 'books', 'Books and educational materials'],
            ['Sports', 'sports', 'Sports and fitness equipment']
        ];
        
        foreach ($categories as $cat) {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, is_active) VALUES (?, ?, ?, 1)");
            $stmt->execute($cat);
        }
        
        echo "✅ Sample categories created<br>";
    }
} catch (Exception $e) {
    echo "❌ Error with categories: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Register a seller account:</strong> Go to <a href='register.php'>register.php</a></li>";
echo "<li><strong>Login:</strong> Go to <a href='login.php'>login.php</a></li>";
echo "<li><strong>Access dashboard:</strong> Go to <a href='dashboard.php'>dashboard.php</a></li>";
echo "<li><strong>Test API directly:</strong> <a href='examples/javascript/basic-usage.html'>Basic API Usage Example</a></li>";
echo "</ol>";

echo "<h2>6. API Endpoints Available</h2>";
echo "<ul>";
echo "<li>POST /api/auth/register - Register seller</li>";
echo "<li>POST /api/auth/login - Login seller</li>";
echo "<li>GET /api/auth/me - Get current seller</li>";
echo "<li>GET /api/products/ - Get products</li>";
echo "<li>POST /api/products/ - Create product</li>";
echo "<li>GET /api/orders/ - Get orders</li>";
echo "<li>GET /api/store/dashboard - Get dashboard data</li>";
echo "</ul>";

?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
    a { color: #b48d6b; text-decoration: none; }
    a:hover { text-decoration: underline; }
    ul, ol { margin: 10px 0; }
    li { margin: 5px 0; }
</style>