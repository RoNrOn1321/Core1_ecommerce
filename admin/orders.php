<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_orders')) {
    header('Location: dashboard.php');
    exit();
}

// Handle order actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $order_id = (int)($_POST['order_id'] ?? 0);
    
    if ($action === 'update_status' && $order_id > 0) {
        $new_status = $_POST['new_status'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        if (!empty($new_status)) {
            try {
                $pdo->beginTransaction();
                
                // Update order status
                $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$new_status, $order_id]);
                
                // Add to status history
                $stmt = $pdo->prepare("INSERT INTO order_status_history (order_id, status, notes, created_by) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $new_status, $notes, getAdminId()]);
                
                // If delivered, update delivered_at
                if ($new_status === 'delivered') {
                    $stmt = $pdo->prepare("UPDATE orders SET delivered_at = NOW() WHERE id = ?");
                    $stmt->execute([$order_id]);
                }
                
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute(['admin', getAdminId(), 'order_status_updated', 'order', $order_id, "Order status updated to $new_status", $_SERVER['REMOTE_ADDR']]);
                
                $pdo->commit();
                header('Location: orders.php?msg=status_updated');
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = 'Error updating order status.';
            }
        }
    }
    
    if ($action === 'update_tracking' && $order_id > 0) {
        $tracking_number = $_POST['tracking_number'] ?? '';
        $courier_company = $_POST['courier_company'] ?? '';
        
        try {
            $stmt = $pdo->prepare("UPDATE orders SET tracking_number = ?, courier_company = ? WHERE id = ?");
            $stmt->execute([$tracking_number, $courier_company, $order_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'order_tracking_updated', 'order', $order_id, 'Order tracking information updated', $_SERVER['REMOTE_ADDR']]);
            
            header('Location: orders.php?msg=tracking_updated');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error updating tracking information.';
        }
    }
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$payment_status_filter = $_GET['payment_status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(o.order_number LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if (!empty($status_filter)) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
}

if (!empty($payment_status_filter)) {
    $where_conditions[] = "o.payment_status = ?";
    $params[] = $payment_status_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

try {
    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_orders = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_orders / $per_page);
    
    // Get orders with pagination
    $stmt = $pdo->prepare("
        SELECT o.*, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email,
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        $where_clause 
        ORDER BY o.created_at DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    
    // Get status counts
    $status_counts = [];
    $status_stmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM orders 
        GROUP BY status
    ");
    while ($row = $status_stmt->fetch()) {
        $status_counts[$row['status']] = $row['count'];
    }
    
} catch (PDOException $e) {
    $error_message = 'Error fetching orders data.';
    $orders = [];
    $total_orders = 0;
    $total_pages = 0;
    $status_counts = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Orders Management - Lumino Admin</title>
    <!-- CSS files -->
    <link rel="stylesheet" href="css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap4.css">
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
                        <a class="nav-link active" href="orders.php">
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
                                <h2 class="h5 page-title">Orders Management</h2>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if (isset($_GET['msg'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php
                                switch ($_GET['msg']) {
                                    case 'status_updated':
                                        echo 'Order status updated successfully.';
                                        break;
                                    case 'tracking_updated':
                                        echo 'Tracking information updated successfully.';
                                        break;
                                    default:
                                        echo 'Operation completed successfully.';
                                }
                                ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Status Tabs -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo empty($status_filter) ? 'active' : ''; ?>" href="orders.php">
                                            All Orders
                                            <span class="badge badge-light ml-1"><?php echo array_sum($status_counts); ?></span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'pending' ? 'active' : ''; ?>" href="orders.php?status=pending">
                                            Pending
                                            <?php if (($status_counts['pending'] ?? 0) > 0): ?>
                                                <span class="badge badge-warning ml-1"><?php echo $status_counts['pending']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'processing' ? 'active' : ''; ?>" href="orders.php?status=processing">
                                            Processing
                                            <?php if (($status_counts['processing'] ?? 0) > 0): ?>
                                                <span class="badge badge-info ml-1"><?php echo $status_counts['processing']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'shipped' ? 'active' : ''; ?>" href="orders.php?status=shipped">
                                            Shipped
                                            <?php if (($status_counts['shipped'] ?? 0) > 0): ?>
                                                <span class="badge badge-primary ml-1"><?php echo $status_counts['shipped']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'delivered' ? 'active' : ''; ?>" href="orders.php?status=delivered">
                                            Delivered
                                            <?php if (($status_counts['delivered'] ?? 0) > 0): ?>
                                                <span class="badge badge-success ml-1"><?php echo $status_counts['delivered']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'cancelled' ? 'active' : ''; ?>" href="orders.php?status=cancelled">
                                            Cancelled
                                            <?php if (($status_counts['cancelled'] ?? 0) > 0): ?>
                                                <span class="badge badge-danger ml-1"><?php echo $status_counts['cancelled']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <!-- Filters -->
                                <form method="GET" action="">
                                    <?php if (!empty($status_filter)): ?>
                                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="search" 
                                                       value="<?php echo htmlspecialchars($search); ?>" 
                                                       placeholder="Search orders, customers...">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="form-control" name="payment_status">
                                                    <option value="">All Payments</option>
                                                    <option value="pending" <?php echo $payment_status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="paid" <?php echo $payment_status_filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                    <option value="failed" <?php echo $payment_status_filter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                                    <option value="refunded" <?php echo $payment_status_filter === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="date" class="form-control" name="date_from" 
                                                       value="<?php echo htmlspecialchars($date_from); ?>" 
                                                       placeholder="From Date">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="date" class="form-control" name="date_to" 
                                                       value="<?php echo htmlspecialchars($date_to); ?>" 
                                                       placeholder="To Date">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="orders.php<?php echo !empty($status_filter) ? '?status=' . urlencode($status_filter) : ''; ?>" class="btn btn-outline-secondary">Clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="card shadow">
                            <div class="card-header">
                                <strong class="card-title">
                                    <?php echo ucfirst($status_filter ?: 'All'); ?> Orders (<?php echo number_format($total_orders); ?> total)
                                </strong>
                            </div>
                            <div class="card-body">
                                <?php if (empty($orders)): ?>
                                    <p class="text-muted text-center py-4">No orders found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Customer</th>
                                                    <th>Items</th>
                                                    <th>Total</th>
                                                    <th>Payment</th>
                                                    <th>Status</th>
                                                    <th>Tracking</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orders as $order): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light"><?php echo $order['item_count']; ?> items</span>
                                                        </td>
                                                        <td>
                                                            <strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <span class="badge badge-<?php 
                                                                    echo $order['payment_status'] === 'paid' ? 'success' : 
                                                                        ($order['payment_status'] === 'pending' ? 'warning' : 
                                                                        ($order['payment_status'] === 'failed' ? 'danger' : 'secondary')); 
                                                                ?>">
                                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                                </span>
                                                                <br>
                                                                <small class="text-muted"><?php echo strtoupper($order['payment_method']); ?></small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $order['status'] === 'delivered' ? 'success' : 
                                                                    ($order['status'] === 'shipped' ? 'primary' : 
                                                                    ($order['status'] === 'processing' ? 'info' : 
                                                                    ($order['status'] === 'cancelled' ? 'danger' : 'warning'))); 
                                                            ?>">
                                                                <?php echo ucfirst($order['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($order['tracking_number'])): ?>
                                                                <div>
                                                                    <strong><?php echo htmlspecialchars($order['tracking_number']); ?></strong>
                                                                    <br>
                                                                    <small class="text-muted"><?php echo htmlspecialchars($order['courier_company']); ?></small>
                                                                </div>
                                                            <?php else: ?>
                                                                <span class="text-muted">Not set</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                                            <br>
                                                            <small class="text-muted"><?php echo date('h:i A', strtotime($order['created_at'])); ?></small>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="order_detail.php?id=<?php echo $order['id']; ?>">
                                                                        <i class="fe fe-eye"></i> View Details
                                                                    </a>
                                                                    <a class="dropdown-item" href="#" onclick="showStatusModal(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')">
                                                                        <i class="fe fe-edit"></i> Update Status
                                                                    </a>
                                                                    <a class="dropdown-item" href="#" onclick="showTrackingModal(<?php echo $order['id']; ?>, '<?php echo htmlspecialchars($order['tracking_number']); ?>', '<?php echo htmlspecialchars($order['courier_company']); ?>')">
                                                                        <i class="fe fe-truck"></i> Update Tracking
                                                                    </a>
                                                                    <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
                                                                        <div class="dropdown-divider"></div>
                                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmCancel(<?php echo $order['id']; ?>)">
                                                                            <i class="fe fe-x"></i> Cancel Order
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if ($total_pages > 1): ?>
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination justify-content-center">
                                                <?php if ($page > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php
                                                $start_page = max(1, $page - 2);
                                                $end_page = min($total_pages, $page + 2);
                                                
                                                for ($i = $start_page; $i <= $end_page; $i++):
                                                ?>
                                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <?php if ($page < $total_pages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" id="status_order_id">
                        
                        <div class="form-group">
                            <label for="new_status">New Status</label>
                            <select class="form-control" name="new_status" id="new_status" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Add notes about this status change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tracking Update Modal -->
    <div class="modal fade" id="trackingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Tracking Information</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_tracking">
                        <input type="hidden" name="order_id" id="tracking_order_id">
                        
                        <div class="form-group">
                            <label for="courier_company">Courier Company</label>
                            <select class="form-control" name="courier_company" id="courier_company">
                                <option value="">Select Courier</option>
                                <option value="LBC">LBC Express</option>
                                <option value="J&T">J&T Express</option>
                                <option value="Ninja Van">Ninja Van</option>
                                <option value="2GO">2GO Express</option>
                                <option value="Grab">Grab Express</option>
                                <option value="Lalamove">Lalamove</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tracking_number">Tracking Number</label>
                            <input type="text" class="form-control" name="tracking_number" id="tracking_number" placeholder="Enter tracking number">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Tracking</button>
                    </div>
                </form>
            </div>
        </div>
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
    function showStatusModal(orderId, currentStatus) {
        $('#status_order_id').val(orderId);
        $('#new_status').val(currentStatus);
        $('#statusModal').modal('show');
    }
    
    function showTrackingModal(orderId, trackingNumber, courierCompany) {
        $('#tracking_order_id').val(orderId);
        $('#tracking_number').val(trackingNumber);
        $('#courier_company').val(courierCompany);
        $('#trackingModal').modal('show');
    }
    
    function confirmCancel(orderId) {
        if (confirm('Are you sure you want to cancel this order?')) {
            showStatusModal(orderId, 'cancelled');
        }
    }
    </script>
</body>
</html>