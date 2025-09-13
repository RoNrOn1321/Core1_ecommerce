<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Page-specific variables
$page_title = 'Dashboard';
$page_description = 'Core1 E-commerce Admin Dashboard';

// Get dashboard statistics
try {
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE status = 'active'");
    $total_users = $stmt->fetch()['total_users'];
    
    // Total sellers
    $stmt = $pdo->query("SELECT COUNT(*) as total_sellers FROM sellers WHERE status = 'approved'");
    $total_sellers = $stmt->fetch()['total_sellers'];
    
    // Pending sellers
    $stmt = $pdo->query("SELECT COUNT(*) as pending_sellers FROM sellers WHERE status = 'pending'");
    $pending_sellers = $stmt->fetch()['pending_sellers'];
    
    // Total products
    $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products WHERE status = 'published'");
    $total_products = $stmt->fetch()['total_products'];
    
    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
    $total_orders = $stmt->fetch()['total_orders'];
    
    // Recent orders
    $stmt = $pdo->query("
        SELECT o.*, u.first_name, u.last_name, u.email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC 
        LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll();
    
    // Chat statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_chats,
            SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting_chats,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_chats,
            SUM(CASE WHEN DATE(started_at) = CURDATE() THEN 1 ELSE 0 END) as today_chats
        FROM chat_sessions
    ");
    $chat_stats = $stmt->fetch();
    
} catch (PDOException $e) {
    // If database is not ready, set default values
    $total_users = 0;
    $total_sellers = 0;
    $pending_sellers = 0;
    $total_products = 0;
    $total_orders = 0;
    $recent_orders = [];
    $chat_stats = ['total_chats' => 0, 'waiting_chats' => 0, 'active_chats' => 0, 'today_chats' => 0];
}

// Page-specific CSS
$additional_css = [];
$additional_head = '
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
</style>
';

// Include layout start
include 'includes/layout_start.php';
?>
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">Welcome back, <?php echo htmlspecialchars(getAdminName()); ?>!</h2>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row">
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Users</span>
                                                <span class="h3 font-bold mb-0"><?php echo number_format($total_users); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-users h2 text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Active Sellers</span>
                                                <span class="h3 font-bold mb-0"><?php echo number_format($total_sellers); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-user-check h2 text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Products</span>
                                                <span class="h3 font-bold mb-0"><?php echo number_format($total_products); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-package h2 text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total Orders</span>
                                                <span class="h3 font-bold mb-0"><?php echo number_format($total_orders); ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-shopping-cart h2 text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Management Quick Access -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <?php if (($chat_stats['waiting_chats'] + $chat_stats['active_chats']) > 0): ?>
                                <div class="alert alert-primary border-left-primary shadow">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="text-primary font-weight-bold mb-1">
                                                <i class="fe fe-message-circle mr-2"></i>
                                                Live Chat Activity
                                            </h6>
                                            <p class="mb-2">
                                                <span class="badge badge-warning mr-2"><?php echo $chat_stats['waiting_chats']; ?> waiting</span>
                                                <span class="badge badge-success mr-2"><?php echo $chat_stats['active_chats']; ?> active</span>
                                                <span class="badge badge-info"><?php echo $chat_stats['today_chats']; ?> today</span>
                                            </p>
                                            <p class="text-muted small mb-0">
                                                <?php if ($chat_stats['waiting_chats'] > 0): ?>
                                                    <strong><?php echo $chat_stats['waiting_chats']; ?> customer(s) waiting for support</strong> - Please respond promptly!
                                                <?php else: ?>
                                                    All chat sessions are being handled. Great job! 👍
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <a href="chat.php" class="btn btn-primary">
                                                <i class="fe fe-message-circle mr-2"></i>Manage Chats
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-light border shadow-sm">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="text-muted font-weight-bold mb-1">
                                                <i class="fe fe-message-circle mr-2"></i>
                                                Live Chat Management
                                            </h6>
                                            <p class="text-muted small mb-0">
                                                No active chat sessions. Ready to help customers when they need support.
                                                <?php if ($chat_stats['today_chats'] > 0): ?>
                                                    <span class="badge badge-info ml-1"><?php echo $chat_stats['today_chats']; ?> chats today</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="col-auto">
                                            <a href="chat.php" class="btn btn-outline-primary">
                                                <i class="fe fe-message-circle mr-2"></i>Chat Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <strong class="card-title">Recent Orders</strong>
                                        <a class="float-right small text-muted" href="orders.php">View all</a>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($recent_orders)): ?>
                                            <p class="text-muted">No orders yet. <a href="../customer/products.php" target="_blank">Visit store</a> to place test orders.</p>
                                        <?php else: ?>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Order #</th>
                                                        <th>Customer</th>
                                                        <th>Status</th>
                                                        <th>Total</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recent_orders as $order): ?>
                                                        <tr>
                                                            <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                                            <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                                            <td>
                                                                <span class="badge badge-<?php 
                                                                    echo $order['status'] === 'delivered' ? 'success' : 
                                                                        ($order['status'] === 'cancelled' ? 'danger' : 'warning'); 
                                                                ?>">
                                                                    <?php echo ucfirst($order['status']); ?>
                                                                </span>
                                                            </td>
                                                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($pending_sellers > 0): ?>
                        <!-- Pending Sellers Alert -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    <i class="fe fe-alert-triangle"></i>
                                    You have <strong><?php echo $pending_sellers; ?></strong> seller(s) waiting for approval. 
                                    <a href="sellers.php" class="alert-link">Review them now</a>.
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

<?php include 'includes/layout_end.php'; ?>