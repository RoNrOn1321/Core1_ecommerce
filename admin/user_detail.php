<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

requireAuth();

$user_id = $_GET['id'] ?? null;
$success_message = '';
$error_message = '';

if (!$user_id) {
    header('Location: users.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $new_status = $_POST['status'];
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            
            $success_message = "User status updated to " . ucfirst($new_status);
            
            // Log the activity
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $log_stmt->execute([
                $_SESSION['user_id'],
                'user_status_update',
                "Updated user ID {$user_id} status to {$new_status}",
                $_SERVER['REMOTE_ADDR']
            ]);
        } catch (PDOException $e) {
            $error_message = 'Error updating user status: ' . $e->getMessage();
        }
    }
}

// Get user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header('Location: users.php');
        exit;
    }
} catch (PDOException $e) {
    $error_message = 'Error loading user data: ' . $e->getMessage();
}

// Get user's orders if they are a customer
$orders = [];
if (isset($user['role']) && $user['role'] === 'customer') {
    try {
        $order_stmt = $pdo->prepare("
            SELECT o.*, COUNT(oi.id) as item_count 
            FROM orders o 
            LEFT JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.customer_id = ? 
            GROUP BY o.id 
            ORDER BY o.created_at DESC 
            LIMIT 10
        ");
        $order_stmt->execute([$user_id]);
        $orders = $order_stmt->fetchAll();
    } catch (PDOException $e) {
        // Handle error silently
    }
}

// Get user's seller profile if they are a seller
$seller_profile = null;
if (isset($user['role']) && $user['role'] === 'seller') {
    try {
        $seller_stmt = $pdo->prepare("SELECT * FROM sellers WHERE user_id = ?");
        $seller_stmt->execute([$user_id]);
        $seller_profile = $seller_stmt->fetch();
    } catch (PDOException $e) {
        // Handle error silently
    }
}

// Get user's support tickets
$support_tickets = [];
try {
    $ticket_stmt = $pdo->prepare("
        SELECT st.*, u.first_name, u.last_name 
        FROM support_tickets st 
        LEFT JOIN users u ON st.assigned_to = u.id 
        WHERE st.customer_id = ? 
        ORDER BY st.created_at DESC 
        LIMIT 5
    ");
    $ticket_stmt->execute([$user_id]);
    $support_tickets = $ticket_stmt->fetchAll();
} catch (PDOException $e) {
    // Handle error silently
}

// Get user activity logs
$activity_logs = [];
try {
    $activity_stmt = $pdo->prepare("
        SELECT * FROM activity_logs 
        WHERE details LIKE ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $activity_stmt->execute(["%user ID {$user_id}%"]);
    $activity_logs = $activity_stmt->fetchAll();
} catch (PDOException $e) {
    // Handle error silently
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="User Details - ShopZone Admin">
    <meta name="author" content="Lumino Team">
    <title>User Details - <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="css/feather.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
</head>
<body class="vertical light">
    <div class="wrapper">
            <!-- Sidebar -->
            <aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
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
                            <a class="nav-link active" href="users.php">
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
                            <a class="nav-link" href="reports.php">
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
                                <h2 class="h5 page-title">User Details</h2>
                            </div>
                            <div class="col-auto">
                                <a href="users.php" class="btn btn-outline-secondary">
                                    <span class="fe fe-arrow-left fe-12 mr-2"></span>Back to Users
                                </a>
                            </div>
                        </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- User Profile Card -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <div class="user-avatar mx-auto mb-3">
                                    <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                </div>
                                <h5><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                                <span class="badge status-badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                                <hr>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <strong>Role</strong><br>
                                        <span class="badge bg-primary"><?= ucfirst($user['role'] ?? 'customer') ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Joined</strong><br>
                                        <small class="text-muted"><?= date('M j, Y', strtotime($user['created_at'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="mb-3">
                                    <label for="status" class="form-label">Change Status</label>
                                    <div class="input-group">
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-warning btn-sm" onclick="resetPassword()">
                                        <i class="fas fa-key me-2"></i>Reset Password
                                    </button>
                                    <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-envelope me-2"></i>Send Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Details and Activity -->
                    <div class="col-md-8">
                        <!-- User Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">User Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Full Name:</strong></td>
                                                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phone:</strong></td>
                                                <td><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Role:</strong></td>
                                                <td><?= ucfirst($user['role'] ?? 'customer') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= ucfirst($user['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Joined:</strong></td>
                                                <td><?= date('F j, Y g:i A', strtotime($user['created_at'])) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last Login:</strong></td>
                                                <td><?= isset($user['last_login']) && $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never' ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>User ID:</strong></td>
                                                <td>#<?= $user['id'] ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($user['role']) && $user['role'] === 'seller' && $seller_profile): ?>
                        <!-- Seller Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Seller Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Business Name:</strong> <?= htmlspecialchars($seller_profile['business_name']) ?></p>
                                        <p><strong>Business Type:</strong> <?= htmlspecialchars($seller_profile['business_type']) ?></p>
                                        <p><strong>Status:</strong> 
                                            <span class="badge bg-<?= $seller_profile['status'] === 'approved' ? 'success' : ($seller_profile['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($seller_profile['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Business Address:</strong> <?= htmlspecialchars($seller_profile['business_address']) ?></p>
                                        <p><strong>Applied:</strong> <?= date('F j, Y', strtotime($seller_profile['created_at'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($user['role']) && $user['role'] === 'customer' && !empty($orders)): ?>
                        <!-- Recent Orders -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Recent Orders</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Items</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><a href="order_detail.php?id=<?= $order['id'] ?>">#<?= $order['id'] ?></a></td>
                                                <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                                <td><?= $order['item_count'] ?> items</td>
                                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Support Tickets -->
                        <?php if (!empty($support_tickets)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Support Tickets</h6>
                            </div>
                            <div class="card-body">
                                <?php foreach ($support_tickets as $ticket): ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong><?= htmlspecialchars($ticket['subject']) ?></strong><br>
                                        <small class="text-muted">
                                            <?= date('M j, Y', strtotime($ticket['created_at'])) ?>
                                            <?php if ($ticket['assigned_to']): ?>
                                                - Assigned to <?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-<?= $ticket['status'] === 'open' ? 'danger' : 'success' ?>">
                                            <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                                        </span>
                                        <span class="badge bg-secondary"><?= ucfirst($ticket['priority']) ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Activity Timeline -->
                        <?php if (!empty($activity_logs)): ?>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Recent Activity</h6>
                            </div>
                            <div class="card-body">
                                <?php foreach ($activity_logs as $log): ?>
                                <div class="timeline-item">
                                    <strong><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $log['action']))) ?></strong>
                                    <p class="mb-1"><?= htmlspecialchars($log['details']) ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= date('M j, Y g:i A', strtotime($log['created_at'])) ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
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
    <script src="js/apps.js"></script>
    <script>
        function resetPassword() {
            if (confirm('Are you sure you want to reset this user\'s password? They will receive an email with a new temporary password.')) {
                // In a real implementation, this would send an AJAX request
                alert('Password reset functionality would be implemented here.');
            }
        }
    </script>
</body>
</html>