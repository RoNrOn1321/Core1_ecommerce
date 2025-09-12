<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_support')) {
    header('Location: dashboard.php');
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'bulk_update_priority') {
        $ticket_ids = $_POST['ticket_ids'] ?? [];
        $new_priority = $_POST['bulk_priority'] ?? '';
        
        if (!empty($ticket_ids) && !empty($new_priority)) {
            try {
                $placeholders = str_repeat('?,', count($ticket_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE support_tickets SET priority = ?, updated_at = NOW() WHERE id IN ($placeholders)");
                $params = array_merge([$new_priority], $ticket_ids);
                $stmt->execute($params);
                
                $success_message = "Updated priority for " . count($ticket_ids) . " ticket(s) successfully.";
            } catch (PDOException $e) {
                $error_message = 'Error updating ticket priorities.';
            }
        }
    }
    
    if ($action === 'bulk_update_status') {
        $ticket_ids = $_POST['ticket_ids'] ?? [];
        $new_status = $_POST['bulk_status'] ?? '';
        
        if (!empty($ticket_ids) && !empty($new_status)) {
            try {
                $placeholders = str_repeat('?,', count($ticket_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id IN ($placeholders)");
                $params = array_merge([$new_status], $ticket_ids);
                $stmt->execute($params);
                
                if ($new_status === 'resolved') {
                    $stmt = $pdo->prepare("UPDATE support_tickets SET resolved_at = NOW() WHERE id IN ($placeholders) AND resolved_at IS NULL");
                    $stmt->execute($ticket_ids);
                }
                
                $success_message = "Updated status for " . count($ticket_ids) . " ticket(s) successfully.";
            } catch (PDOException $e) {
                $error_message = 'Error updating ticket status.';
            }
        }
    }
}

try {
    // Get support statistics
    $stats_query = $pdo->query("
        SELECT 
            COUNT(*) as total_tickets,
            COUNT(CASE WHEN status = 'open' THEN 1 END) as open_tickets,
            COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_tickets,
            COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved_tickets,
            COUNT(CASE WHEN status = 'closed' THEN 1 END) as closed_tickets,
            COUNT(CASE WHEN priority = 'urgent' AND status NOT IN ('resolved', 'closed') THEN 1 END) as urgent_open,
            COUNT(CASE WHEN priority = 'high' AND status NOT IN ('resolved', 'closed') THEN 1 END) as high_open,
            COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as tickets_today,
            COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as tickets_week,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_hours
        FROM support_tickets
    ");
    $stats = $stats_query->fetch();
    
    // Get category distribution
    $category_query = $pdo->query("
        SELECT 
            category,
            COUNT(*) as count,
            COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved_count,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_hours
        FROM support_tickets
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY category
        ORDER BY count DESC
    ");
    $categories = $category_query->fetchAll();
    
    // Get priority distribution
    $priority_query = $pdo->query("
        SELECT 
            priority,
            COUNT(*) as count,
            COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved_count,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_hours
        FROM support_tickets
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY priority
        ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low')
    ");
    $priorities = $priority_query->fetchAll();
    
    // Get recent activity
    $activity_query = $pdo->query("
        SELECT 
            st.ticket_number,
            st.subject,
            st.status,
            st.priority,
            st.updated_at,
            CONCAT(u.first_name, ' ', u.last_name) as customer_name
        FROM support_tickets st
        LEFT JOIN users u ON st.user_id = u.id
        WHERE st.updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY st.updated_at DESC
        LIMIT 10
    ");
    $recent_activity = $activity_query->fetchAll();
    
    // Get workflow settings (these could be stored in database settings table)
    $workflow_settings = [
        'auto_assign' => true,
        'escalation_hours' => 24,
        'auto_close_resolved_days' => 7,
        'priority_sla' => [
            'urgent' => 2, // hours
            'high' => 8,
            'medium' => 24,
            'low' => 72
        ]
    ];
    
} catch (PDOException $e) {
    $error_message = 'Error fetching support settings data.';
    $stats = [];
    $categories = [];
    $priorities = [];
    $recent_activity = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Support Settings - Lumino Admin</title>
    <!-- CSS files -->
    <link rel="stylesheet" href="css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/daterangepicker.css">
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
    <link rel="stylesheet" href="css/app-dark.css" id="darkTheme" disabled>
    <style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
    }
    .progress-sm {
        height: 8px;
    }
    .metric-card {
        transition: transform 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-2px);
    }
    </style>
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
                        <a class="nav-link" href="reports.php">
                            <i class="fe fe-bar-chart-2 fe-16"></i>
                            <span class="ml-3 item-text">Reports</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a class="nav-link active" href="settings.php">
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
                                <h2 class="h5 page-title">Support Settings & Analytics</h2>
                            </div>
                            <div class="col-auto">
                                <a href="support.php" class="btn btn-outline-primary">
                                    <i class="fe fe-arrow-left fe-12 mr-2"></i>Back to Support
                                </a>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($success_message); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Overview Stats -->
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card metric-card stats-card h-100">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0"><?php echo number_format($stats['total_tickets'] ?? 0); ?></h3>
                                        <p class="mb-0">Total Tickets</p>
                                        <small class="opacity-75">All time</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card metric-card border-left-warning shadow h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Open</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['open_tickets'] ?? 0); ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-alert-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card metric-card border-left-success shadow h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Resolved</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['resolved_tickets'] ?? 0); ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-check-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card metric-card border-left-info shadow h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Resolution</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?php 
                                                    $avg_hours = $stats['avg_resolution_hours'] ?? 0;
                                                    if ($avg_hours < 24) {
                                                        echo number_format($avg_hours, 1) . 'h';
                                                    } else {
                                                        echo number_format($avg_hours / 24, 1) . 'd';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-clock fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics & Workflow Management -->
                        <div class="row">
                            <!-- Category Performance -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Category Performance (Last 30 Days)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $category): 
                                                $resolution_rate = $category['count'] > 0 ? ($category['resolved_count'] / $category['count']) * 100 : 0;
                                            ?>
                                                <div class="mb-4">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong><?php echo ucfirst($category['category']); ?></strong>
                                                            <small class="text-muted ml-2"><?php echo $category['count']; ?> tickets</small>
                                                        </div>
                                                        <div class="text-right">
                                                            <small class="text-success"><?php echo number_format($resolution_rate, 1); ?>% resolved</small>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-sm mt-2">
                                                        <div class="progress-bar bg-success" style="width: <?php echo $resolution_rate; ?>%"></div>
                                                    </div>
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            Avg resolution: 
                                                            <?php 
                                                            $avg_time = $category['avg_resolution_hours'] ?? 0;
                                                            if ($avg_time < 24) {
                                                                echo number_format($avg_time, 1) . ' hours';
                                                            } else {
                                                                echo number_format($avg_time / 24, 1) . ' days';
                                                            }
                                                            ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-4">
                                                <i class="fe fe-bar-chart-2 fe-48 mb-3"></i>
                                                <p>No category data available</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Priority Analysis -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Priority Analysis (Last 30 Days)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($priorities)): ?>
                                            <?php foreach ($priorities as $priority): 
                                                $resolution_rate = $priority['count'] > 0 ? ($priority['resolved_count'] / $priority['count']) * 100 : 0;
                                                $badge_class = $priority['priority'] === 'urgent' ? 'danger' : 
                                                              ($priority['priority'] === 'high' ? 'warning' : 
                                                              ($priority['priority'] === 'medium' ? 'info' : 'secondary'));
                                            ?>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <span class="badge badge-<?php echo $badge_class; ?>"><?php echo ucfirst($priority['priority']); ?></span>
                                                        <small class="ml-2 text-muted"><?php echo $priority['count']; ?> tickets</small>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-success font-weight-bold"><?php echo number_format($resolution_rate, 1); ?>%</div>
                                                        <small class="text-muted">
                                                            <?php 
                                                            $avg_time = $priority['avg_resolution_hours'] ?? 0;
                                                            if ($avg_time < 24) {
                                                                echo number_format($avg_time, 1) . 'h avg';
                                                            } else {
                                                                echo number_format($avg_time / 24, 1) . 'd avg';
                                                            }
                                                            ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-4">
                                                <i class="fe fe-zap fe-48 mb-3"></i>
                                                <p>No priority data available</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Recent Activity (Last 24 Hours)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($recent_activity)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Ticket</th>
                                                            <th>Customer</th>
                                                            <th>Subject</th>
                                                            <th>Priority</th>
                                                            <th>Status</th>
                                                            <th>Last Updated</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($recent_activity as $activity): ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="ticket_detail.php?id=<?php echo $activity['id'] ?? ''; ?>" class="font-weight-bold">
                                                                        <?php echo htmlspecialchars($activity['ticket_number']); ?>
                                                                    </a>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($activity['customer_name']); ?></td>
                                                                <td><?php echo htmlspecialchars($activity['subject']); ?></td>
                                                                <td>
                                                                    <span class="badge badge-<?php 
                                                                        echo $activity['priority'] === 'urgent' ? 'danger' : 
                                                                            ($activity['priority'] === 'high' ? 'warning' : 
                                                                            ($activity['priority'] === 'medium' ? 'info' : 'secondary')); 
                                                                    ?>">
                                                                        <?php echo ucfirst($activity['priority']); ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-<?php 
                                                                        echo $activity['status'] === 'open' ? 'danger' : 
                                                                            ($activity['status'] === 'in_progress' ? 'warning' : 
                                                                            ($activity['status'] === 'waiting_customer' ? 'info' : 
                                                                            ($activity['status'] === 'resolved' ? 'success' : 'secondary'))); 
                                                                    ?>">
                                                                        <?php echo ucfirst(str_replace('_', ' ', $activity['status'])); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo date('M d, h:i A', strtotime($activity['updated_at'])); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-5">
                                                <i class="fe fe-activity fe-48 mb-3"></i>
                                                <h5>No Recent Activity</h5>
                                                <p>No tickets have been updated in the last 24 hours.</p>
                                            </div>
                                        <?php endif; ?>
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
    <script src="js/apps.js"></script>
</body>
</html>