<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_sellers')) {
    header('Location: dashboard.php');
    exit();
}

// Handle seller actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $seller_id = (int)($_POST['seller_id'] ?? 0);
    
    if ($action === 'approve' && $seller_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE sellers SET status = 'approved' WHERE id = ?");
            $stmt->execute([$seller_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'seller_approved', 'seller', $seller_id, 'Seller approved', $_SERVER['REMOTE_ADDR']]);
            
            header('Location: sellers.php?msg=approved');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error approving seller.';
        }
    }
    
    if ($action === 'reject' && $seller_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE sellers SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$seller_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'seller_rejected', 'seller', $seller_id, 'Seller rejected', $_SERVER['REMOTE_ADDR']]);
            
            header('Location: sellers.php?msg=rejected');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error rejecting seller.';
        }
    }
    
    if ($action === 'suspend' && $seller_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE sellers SET status = 'suspended' WHERE id = ?");
            $stmt->execute([$seller_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'seller_suspended', 'seller', $seller_id, 'Seller suspended', $_SERVER['REMOTE_ADDR']]);
            
            header('Location: sellers.php?msg=suspended');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error suspending seller.';
        }
    }
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(s.store_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if (!empty($status_filter)) {
    $where_conditions[] = "s.status = ?";
    $params[] = $status_filter;
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

try {
    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM sellers s 
        LEFT JOIN users u ON s.user_id = u.id 
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_sellers = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_sellers / $per_page);
    
    // Get sellers with pagination
    $stmt = $pdo->prepare("
        SELECT s.*, u.first_name, u.last_name, u.email, u.phone,
               (SELECT COUNT(*) FROM products WHERE seller_id = s.id) as total_products,
               (SELECT COUNT(*) FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id 
                WHERE oi.seller_id = s.id) as total_orders
        FROM sellers s 
        LEFT JOIN users u ON s.user_id = u.id 
        $where_clause 
        ORDER BY 
            CASE WHEN s.status = 'pending' THEN 1 ELSE 2 END,
            s.created_at DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $stmt->execute($params);
    $sellers = $stmt->fetchAll();
    
    // Get status counts for tabs
    $status_counts = [];
    $status_stmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM sellers 
        GROUP BY status
    ");
    while ($row = $status_stmt->fetch()) {
        $status_counts[$row['status']] = $row['count'];
    }
    
} catch (PDOException $e) {
    $error_message = 'Error fetching sellers data.';
    $sellers = [];
    $total_sellers = 0;
    $total_pages = 0;
    $status_counts = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sellers Management - Lumino Admin</title>
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
                        <a class="nav-link active" href="sellers.php">
                            <i class="fe fe-user-check fe-16"></i>
                            <span class="ml-3 item-text">Sellers</span>
                            <?php if (($status_counts['pending'] ?? 0) > 0): ?>
                                <span class="badge badge-pill badge-warning ml-auto"><?php echo $status_counts['pending']; ?></span>
                            <?php endif; ?>
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
                                <h2 class="h5 page-title">Sellers Management</h2>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if (isset($_GET['msg'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php
                                switch ($_GET['msg']) {
                                    case 'approved':
                                        echo 'Seller approved successfully.';
                                        break;
                                    case 'rejected':
                                        echo 'Seller rejected successfully.';
                                        break;
                                    case 'suspended':
                                        echo 'Seller suspended successfully.';
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
                                        <a class="nav-link <?php echo empty($status_filter) ? 'active' : ''; ?>" href="sellers.php">
                                            All Sellers
                                            <span class="badge badge-light ml-1"><?php echo array_sum($status_counts); ?></span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'pending' ? 'active' : ''; ?>" href="sellers.php?status=pending">
                                            Pending
                                            <?php if (($status_counts['pending'] ?? 0) > 0): ?>
                                                <span class="badge badge-warning ml-1"><?php echo $status_counts['pending']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'approved' ? 'active' : ''; ?>" href="sellers.php?status=approved">
                                            Approved
                                            <?php if (($status_counts['approved'] ?? 0) > 0): ?>
                                                <span class="badge badge-success ml-1"><?php echo $status_counts['approved']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>" href="sellers.php?status=rejected">
                                            Rejected
                                            <?php if (($status_counts['rejected'] ?? 0) > 0): ?>
                                                <span class="badge badge-danger ml-1"><?php echo $status_counts['rejected']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'suspended' ? 'active' : ''; ?>" href="sellers.php?status=suspended">
                                            Suspended
                                            <?php if (($status_counts['suspended'] ?? 0) > 0): ?>
                                                <span class="badge badge-secondary ml-1"><?php echo $status_counts['suspended']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <!-- Search Form -->
                                <form method="GET" action="">
                                    <?php if (!empty($status_filter)): ?>
                                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="search" 
                                                       value="<?php echo htmlspecialchars($search); ?>" 
                                                       placeholder="Search store name or owner details...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <a href="sellers.php<?php echo !empty($status_filter) ? '?status=' . urlencode($status_filter) : ''; ?>" class="btn btn-outline-secondary">Clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Sellers Table -->
                        <div class="card shadow">
                            <div class="card-header">
                                <strong class="card-title">
                                    <?php echo ucfirst($status_filter ?: 'All'); ?> Sellers (<?php echo number_format($total_sellers); ?> total)
                                </strong>
                            </div>
                            <div class="card-body">
                                <?php if (empty($sellers)): ?>
                                    <p class="text-muted text-center py-4">No sellers found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Store Info</th>
                                                    <th>Owner</th>
                                                    <th>Business Type</th>
                                                    <th>Status</th>
                                                    <th>Products</th>
                                                    <th>Orders</th>
                                                    <th>Applied</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($sellers as $seller): ?>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($seller['store_name']); ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?php echo htmlspecialchars($seller['store_slug']); ?></small>
                                                                <?php if (!empty($seller['store_description'])): ?>
                                                                    <br>
                                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($seller['store_description'], 0, 50)) . '...'; ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']); ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?php echo htmlspecialchars($seller['email']); ?></small>
                                                                <?php if (!empty($seller['phone'])): ?>
                                                                    <br>
                                                                    <small class="text-muted"><?php echo htmlspecialchars($seller['phone']); ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php echo $seller['business_type'] === 'business' ? 'primary' : 'secondary'; ?>">
                                                                <?php echo ucfirst($seller['business_type']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $seller['status'] === 'approved' ? 'success' : 
                                                                    ($seller['status'] === 'pending' ? 'warning' : 
                                                                    ($seller['status'] === 'rejected' ? 'danger' : 'secondary')); 
                                                            ?>">
                                                                <?php echo ucfirst($seller['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo number_format($seller['total_products']); ?></td>
                                                        <td><?php echo number_format($seller['total_orders']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($seller['created_at'])); ?></td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="seller_detail.php?id=<?php echo $seller['id']; ?>">
                                                                        <i class="fe fe-eye"></i> View Details
                                                                    </a>
                                                                    
                                                                    <?php if ($seller['status'] === 'pending'): ?>
                                                                        <div class="dropdown-divider"></div>
                                                                        <form method="POST" class="d-inline">
                                                                            <input type="hidden" name="action" value="approve">
                                                                            <input type="hidden" name="seller_id" value="<?php echo $seller['id']; ?>">
                                                                            <button type="submit" class="dropdown-item text-success" 
                                                                                    onclick="return confirm('Are you sure you want to approve this seller?')">
                                                                                <i class="fe fe-check"></i> Approve
                                                                            </button>
                                                                        </form>
                                                                        <form method="POST" class="d-inline">
                                                                            <input type="hidden" name="action" value="reject">
                                                                            <input type="hidden" name="seller_id" value="<?php echo $seller['id']; ?>">
                                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                                    onclick="return confirm('Are you sure you want to reject this seller?')">
                                                                                <i class="fe fe-x"></i> Reject
                                                                            </button>
                                                                        </form>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php if ($seller['status'] === 'approved'): ?>
                                                                        <form method="POST" class="d-inline">
                                                                            <input type="hidden" name="action" value="suspend">
                                                                            <input type="hidden" name="seller_id" value="<?php echo $seller['id']; ?>">
                                                                            <button type="submit" class="dropdown-item text-warning" 
                                                                                    onclick="return confirm('Are you sure you want to suspend this seller?')">
                                                                                <i class="fe fe-pause"></i> Suspend
                                                                            </button>
                                                                        </form>
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
</body>
</html>