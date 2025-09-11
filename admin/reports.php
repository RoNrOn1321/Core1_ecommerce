<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('view_reports')) {
    header('Location: dashboard.php');
    exit();
}

// Get date range (default to last 30 days)
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

try {
    // Sales Overview
    $sales_stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_orders,
            SUM(total_amount) as total_revenue,
            AVG(total_amount) as avg_order_value,
            SUM(CASE WHEN status = 'delivered' THEN total_amount ELSE 0 END) as delivered_revenue,
            COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
        FROM orders 
        WHERE DATE(created_at) BETWEEN ? AND ?
    ");
    $sales_stmt->execute([$date_from, $date_to]);
    $sales_data = $sales_stmt->fetch();
    
    // Daily sales for chart
    $daily_sales_stmt = $pdo->prepare("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as orders,
            SUM(total_amount) as revenue
        FROM orders 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date
    ");
    $daily_sales_stmt->execute([$date_from, $date_to]);
    $daily_sales = $daily_sales_stmt->fetchAll();
    
    // Top selling products
    $top_products_stmt = $pdo->prepare("
        SELECT 
            p.name,
            p.sku,
            s.store_name,
            SUM(oi.quantity) as total_sold,
            SUM(oi.total_price) as revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN sellers s ON oi.seller_id = s.id
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        GROUP BY oi.product_id
        ORDER BY total_sold DESC
        LIMIT 10
    ");
    $top_products_stmt->execute([$date_from, $date_to]);
    $top_products = $top_products_stmt->fetchAll();
    
    // Top sellers
    $top_sellers_stmt = $pdo->prepare("
        SELECT 
            s.store_name,
            s.id,
            COUNT(DISTINCT oi.order_id) as total_orders,
            SUM(oi.total_price) as revenue,
            AVG(oi.total_price) as avg_order_value
        FROM order_items oi
        JOIN sellers s ON oi.seller_id = s.id
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        GROUP BY s.id
        ORDER BY revenue DESC
        LIMIT 10
    ");
    $top_sellers_stmt->execute([$date_from, $date_to]);
    $top_sellers = $top_sellers_stmt->fetchAll();
    
    // Customer analytics
    $customer_stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT u.id) as total_customers,
            COUNT(DISTINCT CASE WHEN o.created_at >= ? THEN u.id END) as new_customers,
            COUNT(DISTINCT CASE WHEN o.user_id IN (
                SELECT user_id FROM orders 
                GROUP BY user_id 
                HAVING COUNT(*) > 1
            ) THEN u.id END) as returning_customers
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
    ");
    $customer_stmt->execute([$date_from, $date_from, $date_to]);
    $customer_data = $customer_stmt->fetch();
    
    // Payment method breakdown
    $payment_stmt = $pdo->prepare("
        SELECT 
            payment_method,
            COUNT(*) as count,
            SUM(total_amount) as total
        FROM orders 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY payment_method
        ORDER BY total DESC
    ");
    $payment_stmt->execute([$date_from, $date_to]);
    $payment_methods = $payment_stmt->fetchAll();
    
    // Order status breakdown
    $status_stmt = $pdo->prepare("
        SELECT 
            status,
            COUNT(*) as count,
            SUM(total_amount) as total
        FROM orders 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY status
    ");
    $status_stmt->execute([$date_from, $date_to]);
    $order_statuses = $status_stmt->fetchAll();
    
} catch (PDOException $e) {
    $error_message = 'Error fetching reports data.';
    // Set default values
    $sales_data = ['total_orders' => 0, 'total_revenue' => 0, 'avg_order_value' => 0, 'delivered_revenue' => 0, 'delivered_orders' => 0, 'cancelled_orders' => 0];
    $daily_sales = [];
    $top_products = [];
    $top_sellers = [];
    $customer_data = ['total_customers' => 0, 'new_customers' => 0, 'returning_customers' => 0];
    $payment_methods = [];
    $order_statuses = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reports & Analytics - ShopZone Admin</title>
    <!-- CSS files -->
    <link rel="stylesheet" href="css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/daterangepicker.css">
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
    <link rel="stylesheet" href="css/app-dark.css" id="darkTheme" disabled>
</head>
<body class="vertical light">
    <div class="wrapper">
        <!-- Top Navigation -->
        <nav class="topnav navbar navbar-light">
            <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
                <i class="fe fe-menu navbar-toggler-icon"></i>
            </button>
            <form class="form-inline mr-auto searchform text-muted">
                <input class="form-control mr-sm-2 bg-transparent border-0 pl-4 text-muted" type="search" placeholder="Search..." aria-label="Search">
            </form>
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
                        <i class="fe fe-sun fe-16"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="avatar avatar-sm mt-2">
                            <img src="assets/avatars/face-1.jpg" alt="<?php echo htmlspecialchars(getAdminName()); ?>" class="avatar-img rounded-circle">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        <h6 class="dropdown-header"><?php echo htmlspecialchars(getAdminName()); ?></h6>
                        <a class="dropdown-item" href="profile.php">Profile</a>
                        <a class="dropdown-item" href="settings.php">Settings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
            <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
                <i class="fe fe-x"><span class="sr-only"></span></i>
            </a>
            <nav class="vertnav navbar navbar-light">
                <!-- Logo -->
                <div class="w-100 mb-4 d-flex">
                    <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="dashboard.php">
                        <svg version="1.1" id="logo" class="navbar-brand-img brand-sm" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" xml:space="preserve">
                            <g>
                                <polygon class="st0" points="78,105 15,105 24,87 87,87" />
                                <polygon class="st0" points="96,69 33,69 42,51 105,51" />
                                <polygon class="st0" points="78,33 15,33 24,15 87,15" />
                            </g>
                        </svg>
                    </a>
                </div>
                
                <!-- Navigation Menu -->
                <ul class="navbar-nav flex-fill w-100 mb-2">
                    <li class="nav-item w-100">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fe fe-home fe-16"></i>
                            <span class="ml-3 item-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link" href="users.php">
                            <i class="fe fe-users fe-16"></i>
                            <span class="ml-3 item-text">Users</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link" href="sellers.php">
                            <i class="fe fe-user-check fe-16"></i>
                            <span class="ml-3 item-text">Sellers</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link" href="products.php">
                            <i class="fe fe-package fe-16"></i>
                            <span class="ml-3 item-text">Products</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link" href="orders.php">
                            <i class="fe fe-shopping-cart fe-16"></i>
                            <span class="ml-3 item-text">Orders</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link" href="support.php">
                            <i class="fe fe-headphones fe-16"></i>
                            <span class="ml-3 item-text">Support</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link active" href="reports.php">
                            <i class="fe fe-bar-chart-2 fe-16"></i>
                            <span class="ml-3 item-text">Reports</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link" href="settings.php">
                            <i class="fe fe-settings fe-16"></i>
                            <span class="ml-3 item-text">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main role="main" class="main-content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">Reports & Analytics</h2>
                            </div>
                        </div>

                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Date Range Filter -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <strong class="card-title">Date Range</strong>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_from">From Date</label>
                                                <input type="date" class="form-control" name="date_from" 
                                                       value="<?php echo htmlspecialchars($date_from); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_to">To Date</label>
                                                <input type="date" class="form-control" name="date_to" 
                                                       value="<?php echo htmlspecialchars($date_to); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Update Report</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <small class="text-muted">
                                                        Showing data from <?php echo date('M d, Y', strtotime($date_from)); ?> 
                                                        to <?php echo date('M d, Y', strtotime($date_to)); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Sales Overview Cards -->
                        <div class="row">
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow border-left-primary">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Revenue</span>
                                                <span class="h3 font-bold mb-0">₱<?php echo number_format($sales_data['total_revenue'], 2); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-dollar-sign h2 text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow border-left-success">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Orders</span>
                                                <span class="h3 font-bold mb-0"><?php echo number_format($sales_data['total_orders']); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-shopping-cart h2 text-success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow border-left-info">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Avg Order Value</span>
                                                <span class="h3 font-bold mb-0">₱<?php echo number_format($sales_data['avg_order_value'], 2); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-trending-up h2 text-info"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow border-left-warning">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Delivered Orders</span>
                                                <span class="h3 font-bold mb-0"><?php echo number_format($sales_data['delivered_orders']); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-check-circle h2 text-warning"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Chart -->
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <strong class="card-title">Daily Sales Trend</strong>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="salesChart" width="400" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Tables -->
                        <div class="row">
                            <!-- Top Products -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <strong class="card-title">Top Selling Products</strong>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($top_products)): ?>
                                            <p class="text-muted text-center">No product sales data available for this period.</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Seller</th>
                                                            <th>Sold</th>
                                                            <th>Revenue</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($top_products as $product): ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                                    <br>
                                                                    <small class="text-muted"><?php echo htmlspecialchars($product['sku']); ?></small>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($product['store_name']); ?></td>
                                                                <td><?php echo number_format($product['total_sold']); ?></td>
                                                                <td>₱<?php echo number_format($product['revenue'], 2); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Sellers -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <strong class="card-title">Top Performing Sellers</strong>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($top_sellers)): ?>
                                            <p class="text-muted text-center">No seller data available for this period.</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Store</th>
                                                            <th>Orders</th>
                                                            <th>Revenue</th>
                                                            <th>Avg Order</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($top_sellers as $seller): ?>
                                                            <tr>
                                                                <td><strong><?php echo htmlspecialchars($seller['store_name']); ?></strong></td>
                                                                <td><?php echo number_format($seller['total_orders']); ?></td>
                                                                <td>₱<?php echo number_format($seller['revenue'], 2); ?></td>
                                                                <td>₱<?php echo number_format($seller['avg_order_value'], 2); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Breakdown -->
                        <div class="row">
                            <!-- Payment Methods -->
                            <div class="col-md-4 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <strong class="card-title">Payment Methods</strong>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($payment_methods)): ?>
                                            <p class="text-muted text-center">No payment data available.</p>
                                        <?php else: ?>
                                            <?php foreach ($payment_methods as $method): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><?php echo strtoupper($method['payment_method']); ?></span>
                                                    <div>
                                                        <span class="badge badge-primary"><?php echo $method['count']; ?></span>
                                                        <small class="text-muted">₱<?php echo number_format($method['total'], 0); ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Status -->
                            <div class="col-md-4 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <strong class="card-title">Order Status</strong>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($order_statuses)): ?>
                                            <p class="text-muted text-center">No order status data available.</p>
                                        <?php else: ?>
                                            <?php foreach ($order_statuses as $status): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span><?php echo ucfirst($status['status']); ?></span>
                                                    <div>
                                                        <span class="badge badge-<?php 
                                                            echo $status['status'] === 'delivered' ? 'success' : 
                                                                ($status['status'] === 'cancelled' ? 'danger' : 'primary'); 
                                                        ?>"><?php echo $status['count']; ?></span>
                                                        <small class="text-muted">₱<?php echo number_format($status['total'], 0); ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Analytics -->
                            <div class="col-md-4 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <strong class="card-title">Customer Analytics</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>Total Customers</span>
                                            <span class="badge badge-primary"><?php echo number_format($customer_data['total_customers']); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>New Customers</span>
                                            <span class="badge badge-success"><?php echo number_format($customer_data['new_customers']); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>Returning Customers</span>
                                            <span class="badge badge-info"><?php echo number_format($customer_data['returning_customers']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src='js/daterangepicker.js'></script>
    <script src='js/jquery.stickOnScroll.js'></script>
    <script src="js/tinycolor-min.js"></script>
    <script src="js/config.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/apps.js"></script>
    
    <script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = <?php echo json_encode($daily_sales); ?>;
    
    const chartLabels = salesData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    
    const chartRevenue = salesData.map(item => parseFloat(item.revenue) || 0);
    const chartOrders = salesData.map(item => parseInt(item.orders) || 0);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Revenue (₱)',
                data: chartRevenue,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Orders',
                data: chartOrders,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue (₱)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Orders'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
    </script>
</body>
</html>