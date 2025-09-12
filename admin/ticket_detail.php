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

// Get ticket ID
$ticket_id = (int)($_GET['id'] ?? 0);

if (!$ticket_id) {
    header('Location: support.php');
    exit();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'reply') {
        $message = trim($_POST['message'] ?? '');
        $is_internal = isset($_POST['is_internal']) ? 1 : 0;
        
        if (!empty($message)) {
            try {
                $pdo->beginTransaction();
                
                // Add reply message
                $stmt = $pdo->prepare("INSERT INTO support_ticket_messages (ticket_id, sender_type, sender_id, message, is_internal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$ticket_id, 'agent', getAdminId(), $message, $is_internal]);
                
                // Update ticket status and timestamp
                if (!$is_internal) {
                    $stmt = $pdo->prepare("UPDATE support_tickets SET status = CASE WHEN status = 'open' THEN 'in_progress' ELSE status END, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$ticket_id]);
                }
                
                $pdo->commit();
                $success_message = 'Reply sent successfully.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = 'Error sending reply.';
            }
        }
    }
    
    if ($action === 'update_status') {
        $new_status = $_POST['new_status'] ?? '';
        
        if (!empty($new_status)) {
            try {
                $stmt = $pdo->prepare("UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$new_status, $ticket_id]);
                
                if ($new_status === 'resolved') {
                    $stmt = $pdo->prepare("UPDATE support_tickets SET resolved_at = NOW() WHERE id = ?");
                    $stmt->execute([$ticket_id]);
                }
                
                // Log status change as internal message
                $status_message = "Ticket status changed to " . ucfirst(str_replace('_', ' ', $new_status));
                $stmt = $pdo->prepare("INSERT INTO support_ticket_messages (ticket_id, sender_type, sender_id, message, is_internal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$ticket_id, 'system', null, $status_message, 1]);
                
                $success_message = 'Ticket status updated successfully.';
            } catch (PDOException $e) {
                $error_message = 'Error updating ticket status.';
            }
        }
    }
    
    if ($action === 'update_priority') {
        $new_priority = $_POST['new_priority'] ?? '';
        
        if (!empty($new_priority)) {
            try {
                $stmt = $pdo->prepare("UPDATE support_tickets SET priority = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$new_priority, $ticket_id]);
                
                // Log priority change as internal message
                $priority_message = "Ticket priority changed to " . ucfirst($new_priority);
                $stmt = $pdo->prepare("INSERT INTO support_ticket_messages (ticket_id, sender_type, sender_id, message, is_internal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$ticket_id, 'system', null, $priority_message, 1]);
                
                $success_message = 'Ticket priority updated successfully.';
            } catch (PDOException $e) {
                $error_message = 'Error updating ticket priority.';
            }
        }
    }
}

try {
    // Get ticket details
    $stmt = $pdo->prepare("
        SELECT st.*, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email,
               u.phone as customer_phone,
               o.order_number
        FROM support_tickets st 
        LEFT JOIN users u ON st.user_id = u.id 
        LEFT JOIN orders o ON st.order_id = o.id
        WHERE st.id = ?
    ");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        header('Location: support.php');
        exit();
    }
    
    // Get ticket messages
    $stmt = $pdo->prepare("
        SELECT stm.*, 
               CASE 
                   WHEN stm.sender_type = 'customer' THEN CONCAT(u.first_name, ' ', u.last_name)
                   WHEN stm.sender_type = 'agent' THEN 'Support Agent'
                   ELSE 'System'
               END as sender_name,
               CASE 
                   WHEN stm.sender_type = 'customer' THEN u.email
                   ELSE NULL
               END as sender_email
        FROM support_ticket_messages stm
        LEFT JOIN users u ON stm.sender_type = 'customer' AND stm.sender_id = u.id
        WHERE stm.ticket_id = ?
        ORDER BY stm.created_at ASC
    ");
    $stmt->execute([$ticket_id]);
    $messages = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error_message = 'Error fetching ticket details.';
    $ticket = null;
    $messages = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ticket #<?php echo htmlspecialchars($ticket['ticket_number'] ?? ''); ?> - Lumino Admin</title>
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
                        <a class="nav-link active" href="support.php">
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
                        <?php if ($ticket): ?>
                        <div class="row align-items-center mb-4">
                            <div class="col">
                                <h2 class="h5 page-title">
                                    <small class="text-muted text-uppercase">Ticket</small><br />
                                    #<?php echo htmlspecialchars($ticket['ticket_number']); ?>
                                </h2>
                            </div>
                            <div class="col-auto">
                                <a href="support.php" class="btn btn-outline-secondary">
                                    <i class="fe fe-arrow-left fe-12 mr-2"></i>Back to Tickets
                                </a>
                                <button type="button" class="btn btn-primary" onclick="showReplyModal()">
                                    <i class="fe fe-message-circle fe-12 mr-2"></i>Reply
                                </button>
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

                        <div class="row my-4">
                            <div class="col-md-8">
                                <!-- Ticket Details Card -->
                                <div class="card shadow mb-4">
                                    <div class="card-header">
                                        <strong class="card-title"><?php echo htmlspecialchars($ticket['subject']); ?></strong>
                                        <span class="float-right">
                                            <span class="badge badge-<?php echo $ticket['category'] === 'order' ? 'info' : ($ticket['category'] === 'product' ? 'warning' : ($ticket['category'] === 'payment' ? 'success' : 'secondary')); ?>">
                                                <?php echo ucfirst($ticket['category']); ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row align-items-center mb-0">
                                            <dt class="col-sm-3 mb-3 text-muted">Customer</dt>
                                            <dd class="col-sm-9 mb-3">
                                                <strong><?php echo htmlspecialchars($ticket['customer_name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($ticket['customer_email']); ?></small>
                                                <?php if ($ticket['customer_phone']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($ticket['customer_phone']); ?></small>
                                                <?php endif; ?>
                                            </dd>
                                        </dl>
                                        <dl class="row mb-0">
                                            <dt class="col-sm-3 mb-3 text-muted">Priority</dt>
                                            <dd class="col-sm-3 mb-3">
                                                <span class="badge badge-<?php 
                                                    echo $ticket['priority'] === 'urgent' ? 'danger' : 
                                                        ($ticket['priority'] === 'high' ? 'warning' : 
                                                        ($ticket['priority'] === 'medium' ? 'info' : 'secondary')); 
                                                ?>">
                                                    <?php echo ucfirst($ticket['priority']); ?>
                                                </span>
                                                <div class="dropdown d-inline">
                                                    <button class="btn btn-sm p-0 dropdown-toggle" type="button" data-toggle="dropdown">
                                                        <span class="sr-only">Change Priority</span>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" onclick="updatePriority('urgent')">Urgent</a>
                                                        <a class="dropdown-item" href="#" onclick="updatePriority('high')">High</a>
                                                        <a class="dropdown-item" href="#" onclick="updatePriority('medium')">Medium</a>
                                                        <a class="dropdown-item" href="#" onclick="updatePriority('low')">Low</a>
                                                    </div>
                                                </div>
                                            </dd>
                                            <dt class="col-sm-3 mb-3 text-muted">Status</dt>
                                            <dd class="col-sm-3 mb-3">
                                                <span class="dot dot-md bg-<?php 
                                                    echo $ticket['status'] === 'open' ? 'danger' : 
                                                        ($ticket['status'] === 'in_progress' ? 'warning' : 
                                                        ($ticket['status'] === 'waiting_customer' ? 'info' : 
                                                        ($ticket['status'] === 'resolved' ? 'success' : 'secondary'))); 
                                                ?> mr-2"></span>
                                                <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                                <div class="dropdown d-inline">
                                                    <button class="btn btn-sm p-0 dropdown-toggle" type="button" data-toggle="dropdown">
                                                        <span class="sr-only">Change status</span>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" onclick="updateStatus('open')">Open</a>
                                                        <a class="dropdown-item" href="#" onclick="updateStatus('in_progress')">In Progress</a>
                                                        <a class="dropdown-item" href="#" onclick="updateStatus('waiting_customer')">Waiting Customer</a>
                                                        <a class="dropdown-item" href="#" onclick="updateStatus('resolved')">Resolved</a>
                                                        <a class="dropdown-item" href="#" onclick="updateStatus('closed')">Closed</a>
                                                    </div>
                                                </div>
                                            </dd>
                                            <dt class="col-sm-3 mb-3 text-muted">Created On</dt>
                                            <dd class="col-sm-3 mb-3"><?php echo date('M d, Y h:i A', strtotime($ticket['created_at'])); ?></dd>
                                            <dt class="col-sm-3 mb-3 text-muted">Last Update</dt>
                                            <dd class="col-sm-3 mb-3"><?php echo date('M d, Y h:i A', strtotime($ticket['updated_at'])); ?></dd>
                                            <?php if ($ticket['order_number']): ?>
                                                <dt class="col-sm-3 mb-3 text-muted">Related Order</dt>
                                                <dd class="col-sm-9 mb-3">
                                                    <a href="order_detail.php?id=<?php echo $ticket['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        #<?php echo htmlspecialchars($ticket['order_number']); ?>
                                                    </a>
                                                </dd>
                                            <?php endif; ?>
                                        </dl>
                                    </div>
                                </div>

                                <!-- Messages Card -->
                                <div class="card shadow mb-4">
                                    <div class="card-header">
                                        <strong class="card-title">Ticket Thread</strong>
                                        <span class="float-right">
                                            <i class="fe fe-message-circle mr-2"></i>
                                            <?php echo count($messages); ?> message(s)
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($messages as $message): ?>
                                            <?php if ($message['is_internal']): ?>
                                                <!-- Internal message -->
                                                <div class="row align-items-center mb-4 bg-light p-3 rounded">
                                                    <div class="col-auto">
                                                        <div class="avatar avatar-sm mb-3">
                                                            <span class="avatar-title bg-secondary rounded-circle">
                                                                <i class="fe fe-lock"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <strong><?php echo htmlspecialchars($message['sender_name']); ?></strong>
                                                        <small class="badge badge-secondary ml-2">Internal Note</small>
                                                        <div class="mb-2 text-muted"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                                        <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?></small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Regular message -->
                                                <div class="row align-items-center mb-4">
                                                    <div class="col-auto">
                                                        <div class="avatar avatar-sm mb-3 mx-2">
                                                            <?php if ($message['sender_type'] === 'customer'): ?>
                                                                <span class="avatar-title bg-primary rounded-circle text-white">
                                                                    <?php echo strtoupper(substr($message['sender_name'], 0, 1)); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="avatar-title bg-success rounded-circle text-white">
                                                                    <i class="fe fe-user"></i>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <strong><?php echo htmlspecialchars($message['sender_name']); ?></strong>
                                                        <?php if ($message['sender_email']): ?>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($message['sender_email']); ?></small>
                                                        <?php endif; ?>
                                                        <div class="mb-2 mt-2"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                                        <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?></small>
                                                    </div>
                                                    <div class="col-auto">
                                                        <span class="circle circle-sm bg-light">
                                                            <i class="fe fe-corner-down-left"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <?php if (empty($messages)): ?>
                                            <div class="text-center py-4">
                                                <i class="fe fe-message-circle fe-48 text-muted mb-3"></i>
                                                <h5 class="text-muted">No messages yet</h5>
                                                <p class="text-muted">This ticket doesn't have any messages yet.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <!-- Quick Actions Card -->
                                <div class="card shadow mb-4">
                                    <div class="card-header">
                                        <strong class="card-title">Quick Actions</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            <button type="button" class="list-group-item list-group-item-action" onclick="showReplyModal()">
                                                <i class="fe fe-message-circle mr-2"></i> Send Reply
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action" onclick="showInternalNoteModal()">
                                                <i class="fe fe-lock mr-2"></i> Add Internal Note
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action text-success" onclick="updateStatus('resolved')">
                                                <i class="fe fe-check-circle mr-2"></i> Mark as Resolved
                                            </button>
                                            <button type="button" class="list-group-item list-group-item-action text-danger" onclick="updateStatus('closed')">
                                                <i class="fe fe-x-circle mr-2"></i> Close Ticket
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Reply Modal -->
    <div class="modal fade" id="replyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Reply</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reply">
                        
                        <div class="form-group">
                            <label for="message">Your Reply</label>
                            <textarea class="form-control" name="message" id="message" rows="5" 
                                      placeholder="Type your reply to the customer..." required></textarea>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_internal" id="is_internal">
                            <label class="form-check-label" for="is_internal">
                                Internal note (not visible to customer)
                            </label>
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

    <!-- Hidden forms for status/priority updates -->
    <form id="updateStatusForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="new_status" id="new_status">
    </form>

    <form id="updatePriorityForm" method="POST" action="" style="display: none;">
        <input type="hidden" name="action" value="update_priority">
        <input type="hidden" name="new_priority" id="new_priority">
    </form>

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
    function showReplyModal() {
        $('#is_internal').prop('checked', false);
        $('#message').val('');
        $('#replyModal').modal('show');
    }
    
    function showInternalNoteModal() {
        $('#is_internal').prop('checked', true);
        $('#message').val('');
        $('#replyModal').modal('show');
    }
    
    function updateStatus(status) {
        if (confirm('Are you sure you want to change the ticket status to "' + status.replace('_', ' ') + '"?')) {
            $('#new_status').val(status);
            $('#updateStatusForm').submit();
        }
    }
    
    function updatePriority(priority) {
        if (confirm('Are you sure you want to change the ticket priority to "' + priority + '"?')) {
            $('#new_priority').val(priority);
            $('#updatePriorityForm').submit();
        }
    }
    </script>
</body>
</html>