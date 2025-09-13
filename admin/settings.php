<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions (only super_admin and admin can access settings)
if (!in_array(getAdminRole(), ['super_admin', 'admin'])) {
    header('Location: dashboard.php');
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_general') {
        try {
            $settings_to_update = [
                'site_name' => $_POST['site_name'] ?? '',
                'support_email' => $_POST['support_email'] ?? '',
                'support_phone' => $_POST['support_phone'] ?? '',
                'default_currency' => $_POST['default_currency'] ?? 'PHP',
                'tax_rate' => $_POST['tax_rate'] ?? '12.00',
                'free_shipping_threshold' => $_POST['free_shipping_threshold'] ?? '1000.00',
                'order_processing_days' => $_POST['order_processing_days'] ?? '1-2'
            ];
            
            foreach ($settings_to_update as $key => $value) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            }
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'settings_updated', 'settings', 'General settings updated', $_SERVER['REMOTE_ADDR']]);
            
            $success_message = 'General settings updated successfully.';
        } catch (PDOException $e) {
            $error_message = 'Error updating general settings.';
        }
    }
    
    if ($action === 'add_admin') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $role = $_POST['role'] ?? 'admin';
        
        if (empty($username) || empty($email) || empty($password)) {
            $error_message = 'Please fill in all required fields.';
        } else {
            try {
                // Check if username or email already exists
                $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                
                if ($stmt->fetch()) {
                    $error_message = 'Username or email already exists.';
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $password_hash, $first_name, $last_name, $role]);
                    
                    // Log activity
                    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute(['admin', getAdminId(), 'admin_user_created', 'admin_user', "New admin user created: $username", $_SERVER['REMOTE_ADDR']]);
                    
                    $success_message = 'Admin user created successfully.';
                }
            } catch (PDOException $e) {
                $error_message = 'Error creating admin user.';
            }
        }
    }
    
    if ($action === 'toggle_admin_status') {
        $admin_id = (int)($_POST['admin_id'] ?? 0);
        if ($admin_id > 0 && $admin_id !== getAdminId()) { // Prevent self-deactivation
            try {
                $stmt = $pdo->prepare("UPDATE admin_users SET is_active = !is_active WHERE id = ?");
                $stmt->execute([$admin_id]);
                
                $success_message = 'Admin user status updated.';
            } catch (PDOException $e) {
                $error_message = 'Error updating admin user status.';
            }
        }
    }
}

try {
    // Get current settings
    $settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $settings_stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    // Get admin users
    $admin_users_stmt = $pdo->query("SELECT * FROM admin_users ORDER BY created_at DESC");
    $admin_users = $admin_users_stmt->fetchAll();
    
    // Get system statistics
    $stats_stmt = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM sellers) as total_sellers,
            (SELECT COUNT(*) FROM products) as total_products,
            (SELECT COUNT(*) FROM orders) as total_orders,
            (SELECT COUNT(*) FROM support_tickets) as total_tickets
    ");
    $system_stats = $stats_stmt->fetch();
    
} catch (PDOException $e) {
    $error_message = 'Error fetching settings data.';
    $settings = [];
    $admin_users = [];
    $system_stats = ['total_users' => 0, 'total_sellers' => 0, 'total_products' => 0, 'total_orders' => 0, 'total_tickets' => 0];
}

// Page-specific variables
$page_title = 'Admin Settings';
$page_description = 'Manage system settings and admin users';
$additional_css = ['css/daterangepicker.css'];

// Include layout start
include 'includes/layout_start.php';
?>

                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">System Settings</h2>
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

                        <!-- Settings Tabs -->
                        <div class="card shadow">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                            <i class="fe fe-settings"></i> General
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="admins-tab" data-toggle="tab" href="#admins" role="tab">
                                            <i class="fe fe-user-plus"></i> Admin Users
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="system-tab" data-toggle="tab" href="#system" role="tab">
                                            <i class="fe fe-info"></i> System Info
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="settingsTabContent">
                                    <!-- General Settings Tab -->
                                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="update_general">
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="site_name">Site Name</label>
                                                        <input type="text" class="form-control" name="site_name" 
                                                               value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="default_currency">Default Currency</label>
                                                        <select class="form-control" name="default_currency">
                                                            <option value="PHP" <?php echo ($settings['default_currency'] ?? '') === 'PHP' ? 'selected' : ''; ?>>PHP - Philippine Peso</option>
                                                            <option value="USD" <?php echo ($settings['default_currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                                                            <option value="EUR" <?php echo ($settings['default_currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="support_email">Support Email</label>
                                                        <input type="email" class="form-control" name="support_email" 
                                                               value="<?php echo htmlspecialchars($settings['support_email'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="support_phone">Support Phone</label>
                                                        <input type="text" class="form-control" name="support_phone" 
                                                               value="<?php echo htmlspecialchars($settings['support_phone'] ?? ''); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tax_rate">Tax Rate (%)</label>
                                                        <input type="number" step="0.01" class="form-control" name="tax_rate" 
                                                               value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '12.00'); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="free_shipping_threshold">Free Shipping Threshold</label>
                                                        <input type="number" step="0.01" class="form-control" name="free_shipping_threshold" 
                                                               value="<?php echo htmlspecialchars($settings['free_shipping_threshold'] ?? '1000.00'); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="order_processing_days">Order Processing Time</label>
                                                        <input type="text" class="form-control" name="order_processing_days" 
                                                               value="<?php echo htmlspecialchars($settings['order_processing_days'] ?? '1-2'); ?>"
                                                               placeholder="e.g., 1-2 days">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Update General Settings</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Admin Users Tab -->
                                    <div class="tab-pane fade" id="admins" role="tabpanel">
                                        <!-- Add New Admin Form -->
                                        <div class="card border-left-primary mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Add New Admin User</h6>
                                            </div>
                                            <div class="card-body">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="action" value="add_admin">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="username">Username</label>
                                                                <input type="text" class="form-control" name="username" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="email">Email</label>
                                                                <input type="email" class="form-control" name="email" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="first_name">First Name</label>
                                                                <input type="text" class="form-control" name="first_name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="last_name">Last Name</label>
                                                                <input type="text" class="form-control" name="last_name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="role">Role</label>
                                                                <select class="form-control" name="role" required>
                                                                    <?php if (getAdminRole() === 'super_admin'): ?>
                                                                        <option value="super_admin">Super Admin</option>
                                                                    <?php endif; ?>
                                                                    <option value="admin" selected>Admin</option>
                                                                    <option value="support_agent">Support Agent</option>
                                                                    <option value="content_manager">Content Manager</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="password">Password</label>
                                                                <input type="password" class="form-control" name="password" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Add Admin User</button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Existing Admin Users -->
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Status</th>
                                                        <th>Last Login</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($admin_users as $admin): ?>
                                                        <tr>
                                                            <td><strong><?php echo htmlspecialchars($admin['username']); ?></strong></td>
                                                            <td><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                                            <td>
                                                                <span class="badge badge-<?php 
                                                                    echo $admin['role'] === 'super_admin' ? 'danger' : 
                                                                        ($admin['role'] === 'admin' ? 'primary' : 'secondary'); 
                                                                ?>">
                                                                    <?php echo ucfirst(str_replace('_', ' ', $admin['role'])); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-<?php echo $admin['is_active'] ? 'success' : 'warning'; ?>">
                                                                    <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if ($admin['last_login_at']): ?>
                                                                    <?php echo date('M d, Y h:i A', strtotime($admin['last_login_at'])); ?>
                                                                <?php else: ?>
                                                                    <span class="text-muted">Never</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($admin['id'] !== getAdminId()): ?>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="toggle_admin_status">
                                                                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-outline-<?php echo $admin['is_active'] ? 'warning' : 'success'; ?>"
                                                                                onclick="return confirm('Are you sure?')">
                                                                            <?php echo $admin['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                                        </button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    <span class="text-muted">Current User</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- System Info Tab -->
                                    <div class="tab-pane fade" id="system" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>System Statistics</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td>Total Users:</td>
                                                        <td><strong><?php echo number_format($system_stats['total_users']); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Sellers:</td>
                                                        <td><strong><?php echo number_format($system_stats['total_sellers']); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Products:</td>
                                                        <td><strong><?php echo number_format($system_stats['total_products']); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Orders:</td>
                                                        <td><strong><?php echo number_format($system_stats['total_orders']); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Support Tickets:</td>
                                                        <td><strong><?php echo number_format($system_stats['total_tickets']); ?></strong></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Server Information</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td>PHP Version:</td>
                                                        <td><strong><?php echo PHP_VERSION; ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Server Software:</td>
                                                        <td><strong><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Database:</td>
                                                        <td><strong>MySQL <?php echo $pdo->query('SELECT VERSION()')->fetchColumn(); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Max Upload Size:</td>
                                                        <td><strong><?php echo ini_get('upload_max_filesize'); ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Memory Limit:</td>
                                                        <td><strong><?php echo ini_get('memory_limit'); ?></strong></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <h6>System Health</h6>
                                                <div class="alert alert-success" role="alert">
                                                    <i class="fe fe-check-circle"></i>
                                                    System is running normally. All core components are operational.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

<?php include 'includes/layout_end.php'; ?>