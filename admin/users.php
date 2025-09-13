<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_users')) {
    header('Location: dashboard.php');
    exit();
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = (int)($_POST['user_id'] ?? 0);
    
    if ($action === 'toggle_status' && $user_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'suspended' ELSE 'active' END WHERE id = ?");
            $stmt->execute([$user_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'user_status_updated', 'user', $user_id, 'User status toggled', $_SERVER['REMOTE_ADDR']]);
            
            header('Location: users.php?msg=status_updated');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error updating user status.';
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
    $where_conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

try {
    // Get total count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users $where_clause");
    $count_stmt->execute($params);
    $total_users = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_users / $per_page);
    
    // Get users with pagination
    $stmt = $pdo->prepare("
        SELECT id, email, phone, first_name, last_name, status, email_verified, phone_verified, created_at,
               (SELECT COUNT(*) FROM orders WHERE user_id = users.id) as total_orders
        FROM users 
        $where_clause 
        ORDER BY created_at DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error_message = 'Error fetching users data.';
    $users = [];
    $total_users = 0;
    $total_pages = 0;
}

// Page-specific variables
$page_title = 'Users Management';
$page_description = 'Manage all users in the Core1 E-commerce platform';
$additional_css = ['css/dataTables.bootstrap4.css'];

// Include layout start
include 'includes/layout_start.php';
?>
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">Users Management</h2>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if (isset($_GET['msg'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php
                                switch ($_GET['msg']) {
                                    case 'status_updated':
                                        echo 'User status updated successfully.';
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

                        <!-- Filters -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <strong class="card-title">Filter Users</strong>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="search">Search</label>
                                                <input type="text" class="form-control" id="search" name="search" 
                                                       value="<?php echo htmlspecialchars($search); ?>" 
                                                       placeholder="Name, email, or phone">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="">All Statuses</option>
                                                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="suspended" <?php echo $status_filter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                                    <option value="deleted" <?php echo $status_filter === 'deleted' ? 'selected' : ''; ?>>Deleted</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Filter</button>
                                                    <a href="users.php" class="btn btn-outline-secondary">Clear</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div class="card shadow">
                            <div class="card-header">
                                <strong class="card-title">Users (<?php echo number_format($total_users); ?> total)</strong>
                            </div>
                            <div class="card-body">
                                <?php if (empty($users)): ?>
                                    <p class="text-muted text-center py-4">No users found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Status</th>
                                                    <th>Verified</th>
                                                    <th>Orders</th>
                                                    <th>Joined</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($users as $user): ?>
                                                    <tr>
                                                        <td><?php echo $user['id']; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $user['status'] === 'active' ? 'success' : 
                                                                    ($user['status'] === 'suspended' ? 'warning' : 'danger'); 
                                                            ?>">
                                                                <?php echo ucfirst($user['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($user['email_verified']): ?>
                                                                <i class="fe fe-mail text-success" title="Email verified"></i>
                                                            <?php else: ?>
                                                                <i class="fe fe-mail text-muted" title="Email not verified"></i>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($user['phone_verified']): ?>
                                                                <i class="fe fe-phone text-success ml-1" title="Phone verified"></i>
                                                            <?php else: ?>
                                                                <i class="fe fe-phone text-muted ml-1" title="Phone not verified"></i>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo number_format($user['total_orders']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="user_detail.php?id=<?php echo $user['id']; ?>">
                                                                        <i class="fe fe-eye"></i> View Details
                                                                    </a>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="toggle_status">
                                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                        <button type="submit" class="dropdown-item" 
                                                                                onclick="return confirm('Are you sure you want to <?php echo $user['status'] === 'active' ? 'suspend' : 'activate'; ?> this user?')">
                                                                            <i class="fe fe-<?php echo $user['status'] === 'active' ? 'user-x' : 'user-check'; ?>"></i>
                                                                            <?php echo $user['status'] === 'active' ? 'Suspend' : 'Activate'; ?>
                                                                        </button>
                                                                    </form>
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

<?php include 'includes/layout_end.php'; ?>