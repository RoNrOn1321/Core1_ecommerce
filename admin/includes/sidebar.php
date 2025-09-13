<?php
// Get dashboard statistics for sidebar badges
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE status = 'active'");
    $total_users = $stmt->fetch()['total_users'];
    
    // Pending sellers
    $stmt = $pdo->query("SELECT COUNT(*) as pending_sellers FROM sellers WHERE status = 'pending'");
    $pending_sellers = $stmt->fetch()['pending_sellers'];
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products WHERE status = 'published'");
    $total_products = $stmt->fetch()['total_products'];
    
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
    $total_orders = $stmt->fetch()['total_orders'];
    
} catch (PDOException $e) {
    // If database is not ready, set default values
    $total_users = 0;
    $pending_sellers = 0;
    $total_products = 0;
    $total_orders = 0;
}

// Get current page to highlight active menu item
$current_page = basename($_SERVER['PHP_SELF']);
?>

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
                <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fe fe-home fe-16"></i>
                    <span class="ml-3 item-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>" href="users.php">
                    <i class="fe fe-users fe-16"></i>
                    <span class="ml-3 item-text">Users</span>
                    <span class="badge badge-pill badge-secondary ml-auto"><?php echo $total_users; ?></span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link <?php echo ($current_page == 'sellers.php') ? 'active' : ''; ?>" href="sellers.php">
                    <i class="fe fe-user-check fe-16"></i>
                    <span class="ml-3 item-text">Sellers</span>
                    <?php if ($pending_sellers > 0): ?>
                        <span class="badge badge-pill badge-warning ml-auto"><?php echo $pending_sellers; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>" href="products.php">
                    <i class="fe fe-package fe-16"></i>
                    <span class="ml-3 item-text">Products</span>
                    <span class="badge badge-pill badge-secondary ml-auto"><?php echo $total_products; ?></span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>" href="orders.php">
                    <i class="fe fe-shopping-cart fe-16"></i>
                    <span class="ml-3 item-text">Orders</span>
                    <span class="badge badge-pill badge-secondary ml-auto"><?php echo $total_orders; ?></span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link <?php echo ($current_page == 'support.php') ? 'active' : ''; ?>" href="support.php">
                    <i class="fe fe-headphones fe-16"></i>
                    <span class="ml-3 item-text">Support</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>" href="reports.php">
                    <i class="fe fe-bar-chart-2 fe-16"></i>
                    <span class="ml-3 item-text">Reports</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>" href="settings.php">
                    <i class="fe fe-settings fe-16"></i>
                    <span class="ml-3 item-text">Settings</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>