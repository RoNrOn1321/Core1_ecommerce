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

// Handle ticket actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $ticket_id = (int)($_POST['ticket_id'] ?? 0);
    
    if ($action === 'update_status' && $ticket_id > 0) {
        $new_status = $_POST['new_status'] ?? '';
        
        if (!empty($new_status)) {
            try {
                $stmt = $pdo->prepare("UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$new_status, $ticket_id]);
                
                if ($new_status === 'resolved') {
                    $stmt = $pdo->prepare("UPDATE support_tickets SET resolved_at = NOW() WHERE id = ?");
                    $stmt->execute([$ticket_id]);
                }
                
                // Log activity
                $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute(['admin', getAdminId(), 'ticket_status_updated', 'support_ticket', $ticket_id, "Ticket status updated to $new_status", $_SERVER['REMOTE_ADDR']]);
                
                $success_message = 'Ticket status updated successfully.';
            } catch (PDOException $e) {
                $error_message = 'Error updating ticket status.';
            }
        }
    }
    
    if ($action === 'reply_ticket' && $ticket_id > 0) {
        $message = trim($_POST['message'] ?? '');
        
        if (!empty($message)) {
            try {
                $pdo->beginTransaction();
                
                // Add reply message
                $stmt = $pdo->prepare("INSERT INTO support_ticket_messages (ticket_id, sender_type, sender_id, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$ticket_id, 'agent', getAdminId(), $message]);
                
                // Update ticket status to in_progress if it was open
                $stmt = $pdo->prepare("UPDATE support_tickets SET status = CASE WHEN status = 'open' THEN 'in_progress' ELSE status END, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$ticket_id]);
                
                $pdo->commit();
                $success_message = 'Reply sent successfully.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = 'Error sending reply.';
            }
        }
    }
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$priority_filter = $_GET['priority'] ?? '';
$category_filter = $_GET['category'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(st.subject LIKE ? OR st.ticket_number LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if (!empty($status_filter)) {
    $where_conditions[] = "st.status = ?";
    $params[] = $status_filter;
}

if (!empty($priority_filter)) {
    $where_conditions[] = "st.priority = ?";
    $params[] = $priority_filter;
}

if (!empty($category_filter)) {
    $where_conditions[] = "st.category = ?";
    $params[] = $category_filter;
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

try {
    // Get analytics data
    $analytics = [];
    
    // Get ticket analytics for the last 30 days
    $analytics_stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_tickets,
            SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as tickets_last_7_days,
            SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 1 ELSE 0 END) as tickets_today,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_time_hours,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_tickets,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_tickets,
            SUM(CASE WHEN priority = 'urgent' AND status IN ('open', 'in_progress') THEN 1 ELSE 0 END) as urgent_tickets,
            SUM(CASE WHEN updated_at < DATE_SUB(NOW(), INTERVAL 1 DAY) AND status IN ('open', 'in_progress') THEN 1 ELSE 0 END) as stale_tickets
        FROM support_tickets
    ");
    $analytics = $analytics_stmt->fetch();
    
    // Get category breakdown
    $category_stmt = $pdo->query("
        SELECT category, COUNT(*) as count 
        FROM support_tickets 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY category 
        ORDER BY count DESC
    ");
    $category_data = $category_stmt->fetchAll();
    
    // Get response time by priority
    $priority_stmt = $pdo->query("
        SELECT 
            priority,
            COUNT(*) as total,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_time
        FROM support_tickets 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY priority
    ");
    $priority_data = $priority_stmt->fetchAll();
    
    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM support_tickets st 
        LEFT JOIN users u ON st.user_id = u.id 
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_tickets = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_tickets / $per_page);
    
    // Get tickets with pagination
    $stmt = $pdo->prepare("
        SELECT st.*, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email,
               (SELECT COUNT(*) FROM support_ticket_messages WHERE ticket_id = st.id) as message_count,
               (SELECT created_at FROM support_ticket_messages WHERE ticket_id = st.id ORDER BY created_at DESC LIMIT 1) as last_message_at
        FROM support_tickets st 
        LEFT JOIN users u ON st.user_id = u.id 
        $where_clause 
        ORDER BY 
            CASE WHEN st.status = 'open' THEN 1 
                 WHEN st.status = 'in_progress' THEN 2 
                 ELSE 3 END,
            st.priority = 'urgent' DESC,
            st.priority = 'high' DESC,
            st.created_at DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();
    
    // Get status counts
    $status_counts = [];
    $status_stmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM support_tickets 
        GROUP BY status
    ");
    while ($row = $status_stmt->fetch()) {
        $status_counts[$row['status']] = $row['count'];
    }
    
} catch (PDOException $e) {
    $error_message = 'Error fetching support tickets data.';
    $tickets = [];
    $total_tickets = 0;
    $total_pages = 0;
    $status_counts = [];
    $analytics = [];
    $category_data = [];
    $priority_data = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Support & Tickets - Lumino Admin</title>
    <!-- CSS files -->
    <link rel="stylesheet" href="css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/feather.css">
    <link rel="stylesheet" href="css/daterangepicker.css">
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
    <link rel="stylesheet" href="css/app-dark.css" id="darkTheme" disabled>
    <style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .font-weight-bold {
        font-weight: 700 !important;
    }
    .progress {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 0.35rem;
    }
    .analytics-card {
        transition: transform 0.2s;
    }
    .analytics-card:hover {
        transform: translateY(-2px);
    }
    .alert-urgent {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.8; }
        100% { opacity: 1; }
    }
    .card-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .card-stats .card-body {
        padding: 1.5rem;
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
                        <a class="nav-link active" href="support.php">
                            <i class="fe fe-headphones fe-16"></i>
                            <span class="ml-3 item-text">Support</span>
                            <?php if (($status_counts['open'] ?? 0) > 0): ?>
                                <span class="badge badge-pill badge-danger ml-auto"><?php echo $status_counts['open']; ?></span>
                            <?php endif; ?>
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
                                <h2 class="h5 page-title">Support & Tickets</h2>
                            </div>
                            <div class="col-auto">
                                <a href="support_settings.php" class="btn btn-outline-primary">
                                    <i class="fe fe-settings fe-12 mr-2"></i>Support Settings
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

                        <!-- Analytics Dashboard -->
                        <div class="row mb-4">
                            <!-- Key Metrics Cards -->
                            <div class="col-md-3 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Open Tickets</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?php echo number_format($analytics['open_tickets'] ?? 0); ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-alert-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Resolved (Total)</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?php echo number_format($analytics['resolved_tickets'] ?? 0); ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-check-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Urgent Tickets</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?php echo number_format($analytics['urgent_tickets'] ?? 0); ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fe fe-zap fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Resolution</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?php 
                                                    $avg_hours = $analytics['avg_resolution_time_hours'] ?? 0;
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

                        <!-- Additional Analytics Row -->
                        <div class="row mb-4">
                            <!-- Category Breakdown -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Category Breakdown (Last 30 Days)</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($category_data)): ?>
                                            <?php 
                                            $total_category_tickets = array_sum(array_column($category_data, 'count'));
                                            foreach ($category_data as $category): 
                                                $percentage = $total_category_tickets > 0 ? ($category['count'] / $total_category_tickets) * 100 : 0;
                                            ?>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-sm font-weight-bold"><?php echo ucfirst($category['category']); ?></span>
                                                        <span class="text-sm"><?php echo $category['count']; ?> tickets</span>
                                                    </div>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" 
                                                             style="width: <?php echo $percentage; ?>%" 
                                                             aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-3">
                                                <i class="fe fe-bar-chart-2 fe-32 mb-2"></i>
                                                <p>No ticket data available</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Priority Response Times -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Avg Response Time by Priority</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($priority_data)): ?>
                                            <?php foreach ($priority_data as $priority): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <span class="badge badge-<?php 
                                                            echo $priority['priority'] === 'urgent' ? 'danger' : 
                                                                ($priority['priority'] === 'high' ? 'warning' : 
                                                                ($priority['priority'] === 'medium' ? 'info' : 'secondary')); 
                                                        ?>"><?php echo ucfirst($priority['priority']); ?></span>
                                                        <small class="ml-2 text-muted"><?php echo $priority['total']; ?> tickets</small>
                                                    </div>
                                                    <div class="text-right">
                                                        <strong>
                                                            <?php 
                                                            $avg_time = $priority['avg_resolution_time'] ?? 0;
                                                            if ($avg_time < 24) {
                                                                echo number_format($avg_time, 1) . ' hours';
                                                            } else {
                                                                echo number_format($avg_time / 24, 1) . ' days';
                                                            }
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-3">
                                                <i class="fe fe-clock fe-32 mb-2"></i>
                                                <p>No response time data available</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Cards -->
                        <div class="row mb-4">
                            <?php if (($analytics['urgent_tickets'] ?? 0) > 0): ?>
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-danger">
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-alert-triangle mr-3 fe-24"></i>
                                        <div>
                                            <h6 class="mb-1">Urgent Tickets Need Attention</h6>
                                            <p class="mb-0">You have <?php echo $analytics['urgent_tickets']; ?> urgent ticket(s) that need immediate attention.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (($analytics['stale_tickets'] ?? 0) > 0): ?>
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-warning">
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-clock mr-3 fe-24"></i>
                                        <div>
                                            <h6 class="mb-1">Stale Tickets</h6>
                                            <p class="mb-0"><?php echo $analytics['stale_tickets']; ?> ticket(s) haven't been updated in 24+ hours.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Status Tabs -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo empty($status_filter) ? 'active' : ''; ?>" href="support.php">
                                            All Tickets
                                            <span class="badge badge-light ml-1"><?php echo array_sum($status_counts); ?></span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'open' ? 'active' : ''; ?>" href="support.php?status=open">
                                            Open
                                            <?php if (($status_counts['open'] ?? 0) > 0): ?>
                                                <span class="badge badge-danger ml-1"><?php echo $status_counts['open']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'in_progress' ? 'active' : ''; ?>" href="support.php?status=in_progress">
                                            In Progress
                                            <?php if (($status_counts['in_progress'] ?? 0) > 0): ?>
                                                <span class="badge badge-warning ml-1"><?php echo $status_counts['in_progress']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'waiting_customer' ? 'active' : ''; ?>" href="support.php?status=waiting_customer">
                                            Waiting Customer
                                            <?php if (($status_counts['waiting_customer'] ?? 0) > 0): ?>
                                                <span class="badge badge-info ml-1"><?php echo $status_counts['waiting_customer']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'resolved' ? 'active' : ''; ?>" href="support.php?status=resolved">
                                            Resolved
                                            <?php if (($status_counts['resolved'] ?? 0) > 0): ?>
                                                <span class="badge badge-success ml-1"><?php echo $status_counts['resolved']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'closed' ? 'active' : ''; ?>" href="support.php?status=closed">
                                            Closed
                                            <?php if (($status_counts['closed'] ?? 0) > 0): ?>
                                                <span class="badge badge-secondary ml-1"><?php echo $status_counts['closed']; ?></span>
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
                                                       placeholder="Search tickets or customers...">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="form-control" name="priority">
                                                    <option value="">All Priorities</option>
                                                    <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                                                    <option value="medium" <?php echo $priority_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                                    <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
                                                    <option value="urgent" <?php echo $priority_filter === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="form-control" name="category">
                                                    <option value="">All Categories</option>
                                                    <option value="order" <?php echo $category_filter === 'order' ? 'selected' : ''; ?>>Order</option>
                                                    <option value="product" <?php echo $category_filter === 'product' ? 'selected' : ''; ?>>Product</option>
                                                    <option value="payment" <?php echo $category_filter === 'payment' ? 'selected' : ''; ?>>Payment</option>
                                                    <option value="shipping" <?php echo $category_filter === 'shipping' ? 'selected' : ''; ?>>Shipping</option>
                                                    <option value="technical" <?php echo $category_filter === 'technical' ? 'selected' : ''; ?>>Technical</option>
                                                    <option value="other" <?php echo $category_filter === 'other' ? 'selected' : ''; ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="support.php<?php echo !empty($status_filter) ? '?status=' . urlencode($status_filter) : ''; ?>" class="btn btn-outline-secondary">Clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tickets Table -->
                        <div class="card shadow">
                            <div class="card-header">
                                <strong class="card-title">
                                    <?php echo ucfirst($status_filter ?: 'All'); ?> Support Tickets (<?php echo number_format($total_tickets); ?> total)
                                </strong>
                            </div>
                            <div class="card-body">
                                <?php if (empty($tickets)): ?>
                                    <div class="text-center py-5">
                                        <i class="fe fe-headphones fe-48 text-muted mb-3"></i>
                                        <h5 class="text-muted">No support tickets found</h5>
                                        <p class="text-muted">No tickets match your current filters.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Ticket</th>
                                                    <th>Customer</th>
                                                    <th>Subject</th>
                                                    <th>Category</th>
                                                    <th>Priority</th>
                                                    <th>Status</th>
                                                    <th>Messages</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tickets as $ticket): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($ticket['ticket_number']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars($ticket['customer_name']); ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?php echo htmlspecialchars($ticket['customer_email']); ?></small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($ticket['subject']); ?></strong>
                                                            <?php if ($ticket['last_message_at']): ?>
                                                                <br>
                                                                <small class="text-muted">Last reply: <?php echo date('M d, h:i A', strtotime($ticket['last_message_at'])); ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light"><?php echo ucfirst($ticket['category']); ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $ticket['priority'] === 'urgent' ? 'danger' : 
                                                                    ($ticket['priority'] === 'high' ? 'warning' : 
                                                                    ($ticket['priority'] === 'medium' ? 'info' : 'secondary')); 
                                                            ?>">
                                                                <?php echo ucfirst($ticket['priority']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $ticket['status'] === 'open' ? 'danger' : 
                                                                    ($ticket['status'] === 'in_progress' ? 'warning' : 
                                                                    ($ticket['status'] === 'waiting_customer' ? 'info' : 
                                                                    ($ticket['status'] === 'resolved' ? 'success' : 'secondary'))); 
                                                            ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light"><?php echo $ticket['message_count']; ?></span>
                                                        </td>
                                                        <td>
                                                            <?php echo date('M d, Y', strtotime($ticket['created_at'])); ?>
                                                            <br>
                                                            <small class="text-muted"><?php echo date('h:i A', strtotime($ticket['created_at'])); ?></small>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="ticket_detail.php?id=<?php echo $ticket['id']; ?>">
                                                                        <i class="fe fe-eye"></i> View Details
                                                                    </a>
                                                                    <a class="dropdown-item" href="#" onclick="showReplyModal(<?php echo $ticket['id']; ?>, '<?php echo htmlspecialchars($ticket['ticket_number']); ?>')">
                                                                        <i class="fe fe-message-circle"></i> Quick Reply
                                                                    </a>
                                                                    <a class="dropdown-item" href="#" onclick="showStatusModal(<?php echo $ticket['id']; ?>, '<?php echo $ticket['status']; ?>')">
                                                                        <i class="fe fe-edit"></i> Update Status
                                                                    </a>
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

    <!-- Quick Reply Modal -->
    <div class="modal fade" id="replyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Reply - <span id="reply_ticket_number"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reply_ticket">
                        <input type="hidden" name="ticket_id" id="reply_ticket_id">
                        
                        <div class="form-group">
                            <label for="message">Your Reply</label>
                            <textarea class="form-control" name="message" id="message" rows="5" 
                                      placeholder="Type your reply to the customer..." required></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fe fe-info"></i>
                            <strong>Note:</strong> This reply will be sent to the customer and the ticket status will be updated to "In Progress" if currently open.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Ticket Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="ticket_id" id="status_ticket_id">
                        
                        <div class="form-group">
                            <label for="new_status">New Status</label>
                            <select class="form-control" name="new_status" id="new_status" required>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="waiting_customer">Waiting Customer</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
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
    function showReplyModal(ticketId, ticketNumber) {
        $('#reply_ticket_id').val(ticketId);
        $('#reply_ticket_number').text(ticketNumber);
        $('#message').val('');
        $('#replyModal').modal('show');
    }
    
    function showStatusModal(ticketId, currentStatus) {
        $('#status_ticket_id').val(ticketId);
        $('#new_status').val(currentStatus);
        $('#statusModal').modal('show');
    }
    </script>
</body>
</html>