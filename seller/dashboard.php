<?php
$page_title = "Dashboard";

// Include necessary files
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/order.php';

// Initialize authentication
$auth = new SellerAuth($pdo);
$auth->requireLogin();

$sellerId = $_SESSION['seller_id'];

// Get seller information
$stmt = $pdo->prepare("SELECT * FROM sellers WHERE id = ?");
$stmt->execute([$sellerId]);
$seller = $stmt->fetch();

// Get dashboard statistics
$orderManager = new OrderManager($pdo);
$orderStats = $orderManager->getOrderStats($sellerId);
$stats = $orderStats['success'] ? $orderStats['stats'] : ['by_status' => [], 'total_orders' => 0, 'total_revenue' => 0];

// Get product statistics
$productStmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as active_products,
        SUM(CASE WHEN stock_quantity <= low_stock_threshold AND stock_quantity > 0 THEN 1 ELSE 0 END) as low_stock_products,
        SUM(CASE WHEN stock_quantity = 0 OR stock_status = 'out_of_stock' THEN 1 ELSE 0 END) as out_of_stock_products
    FROM products 
    WHERE seller_id = ?
");
$productStmt->execute([$sellerId]);
$productStats = $productStmt->fetch();

// Get recent orders
$recentOrdersResult = $orderManager->getOrders($sellerId, ['limit' => 5, 'offset' => 0]);
$recentOrders = $recentOrdersResult['success'] ? $recentOrdersResult['orders'] : [];

// Get top products by sales
$topProductsStmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, 
           COUNT(oi.id) as orders_count,
           SUM(oi.quantity) as total_sold,
           SUM(oi.total_price) as total_revenue
    FROM products p
    LEFT JOIN order_items oi ON p.id = oi.product_id
    WHERE p.seller_id = ?
    GROUP BY p.id
    ORDER BY total_revenue DESC
    LIMIT 5
");
$topProductsStmt->execute([$sellerId]);
$topProducts = $topProductsStmt->fetchAll();

// Calculate monthly revenue
$monthlyRevenueStmt = $pdo->prepare("
    SELECT SUM(oi.total_price) as month_revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.seller_id = ? 
    AND o.payment_status = 'paid'
    AND MONTH(o.created_at) = MONTH(CURRENT_DATE())
    AND YEAR(o.created_at) = YEAR(CURRENT_DATE())
");
$monthlyRevenueStmt->execute([$sellerId]);
$monthlyRevenue = $monthlyRevenueStmt->fetch()['month_revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lumino Ecommerce</title>
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
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
                <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($seller['store_name'] ?? $seller['business_name'] ?? 'Seller'); ?>! Here's what's happening with your store today.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-beige rounded-full flex items-center justify-center">
                            <i class="fas fa-peso-sign text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">₱<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></p>
                            <p class="text-sm text-green-600 flex items-center">
                                <i class="fas fa-chart-line mr-1"></i>
                                ₱<?php echo number_format($monthlyRevenue, 2); ?> this month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_orders'] ?? 0; ?></p>
                            <p class="text-sm <?php echo ($stats['by_status']['pending'] ?? 0) > 0 ? 'text-orange-600' : 'text-green-600'; ?> flex items-center">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo $stats['by_status']['pending'] ?? 0; ?> pending orders
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Products</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $productStats['total_products'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600"><?php echo $productStats['active_products'] ?? 0; ?> published</p>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Low Stock</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $productStats['low_stock_products'] ?? 0; ?></p>
                            <p class="text-sm text-orange-600">Items need restocking</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                            <a href="orders.php" class="text-beige hover:text-beige-dark text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php if (empty($recentOrders)): ?>
                                <div class="text-center text-gray-500">No recent orders</div>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): 
                                    $statusClasses = [
                                        'pending' => 'bg-blue-100 text-blue-800',
                                        'processing' => 'bg-yellow-100 text-yellow-800',
                                        'shipped' => 'bg-purple-100 text-purple-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusClass = $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-beige rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            #<?php echo substr($order['order_number'], -4); ?>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($order['order_number']); ?></p>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">₱<?php echo number_format($order['seller_total'], 2); ?></p>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Status Distribution -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Order Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <?php 
                            $statusItems = [
                                ['status' => 'pending', 'count' => $stats['by_status']['pending'] ?? 0, 'color' => 'bg-yellow-500', 'textColor' => 'text-yellow-600'],
                                ['status' => 'processing', 'count' => $stats['by_status']['processing'] ?? 0, 'color' => 'bg-blue-500', 'textColor' => 'text-blue-600'],
                                ['status' => 'shipped', 'count' => $stats['by_status']['shipped'] ?? 0, 'color' => 'bg-purple-500', 'textColor' => 'text-purple-600'],
                                ['status' => 'delivered', 'count' => $stats['by_status']['delivered'] ?? 0, 'color' => 'bg-green-500', 'textColor' => 'text-green-600'],
                                ['status' => 'cancelled', 'count' => $stats['by_status']['cancelled'] ?? 0, 'color' => 'bg-red-500', 'textColor' => 'text-red-600']
                            ];
                            
                            foreach ($statusItems as $item): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 <?php echo $item['color']; ?> rounded-full mr-3"></div>
                                        <span class="text-gray-700 capitalize"><?php echo $item['status']; ?></span>
                                    </div>
                                    <span class="font-semibold <?php echo $item['textColor']; ?>"><?php echo $item['count']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products Section -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Top Performing Products</h3>
                        <a href="products.php" class="text-beige hover:text-beige-dark text-sm font-medium">Manage Products</a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($topProducts)): ?>
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-box-open text-4xl text-gray-400 mb-4"></i>
                            <p>No product sales data available yet</p>
                            <p class="text-sm text-gray-400 mt-2">Start selling products to see performance metrics</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($topProducts as $product): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">₱<?php echo number_format($product['price'], 2); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo $product['total_sold'] ?? 0; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-beige">₱<?php echo number_format($product['total_revenue'] ?? 0, 2); ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>