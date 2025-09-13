<?php
session_start();
require_once 'config/database.php';
require_once 'config/auth.php';

requireAuth();

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        
        if (empty($first_name) || empty($last_name) || empty($email)) {
            $error_message = 'First name, last name, and email are required.';
        } else {
            try {
                // Check if email is already taken by another user
                $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $check_stmt->execute([$email, $user_id]);
                
                if ($check_stmt->rowCount() > 0) {
                    $error_message = 'Email address is already in use by another account.';
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
                    $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);
                    
                    $success_message = 'Profile updated successfully.';
                    
                    // Log the activity
                    $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
                    $log_stmt->execute([
                        $user_id,
                        'profile_update',
                        'Updated own profile information',
                        $_SERVER['REMOTE_ADDR']
                    ]);
                }
            } catch (PDOException $e) {
                $error_message = 'Error updating profile: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = 'All password fields are required.';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'New passwords do not match.';
        } elseif (strlen($new_password) < 8) {
            $error_message = 'New password must be at least 8 characters long.';
        } else {
            try {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $stored_password = $stmt->fetchColumn();
                
                if (!password_verify($current_password, $stored_password)) {
                    $error_message = 'Current password is incorrect.';
                } else {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed_password, $user_id]);
                    
                    $success_message = 'Password changed successfully.';
                    
                    // Log the activity
                    $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
                    $log_stmt->execute([
                        $user_id,
                        'password_change',
                        'Changed own password',
                        $_SERVER['REMOTE_ADDR']
                    ]);
                }
            } catch (PDOException $e) {
                $error_message = 'Error changing password: ' . $e->getMessage();
            }
        }
    }
}

// Get current user information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header('Location: login.php');
        exit();
    }
} catch (PDOException $e) {
    $error_message = 'Error loading profile data.';
    $user = [];
}

// Get recent activities
try {
    $activity_stmt = $pdo->prepare("
        SELECT action, details, created_at, ip_address 
        FROM activity_logs 
        WHERE admin_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $activity_stmt->execute([$user_id]);
    $recent_activities = $activity_stmt->fetchAll();
} catch (PDOException $e) {
    $recent_activities = [];
}

// Page-specific variables
$page_title = 'Admin Profile';
$page_description = 'Manage your admin profile settings';

// Include layout start
include 'includes/layout_start.php';
?>

                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">My Profile</h2>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted">Welcome, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if ($success_message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($success_message) ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error_message) ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- Profile Information Card -->
                            <div class="col-md-8">
                                <div class="card shadow mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Profile Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="first_name" class="form-label">First Name</label>
                                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                                           value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="last_name" class="form-label">Last Name</label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                                           value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" 
                                                           value="<?= htmlspecialchars($user['email']) ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="phone" class="form-label">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" 
                                                           value="<?= htmlspecialchars($user['phone']) ?>">
                                                </div>
                                            </div>
                                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Change Password Card -->
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Change Password</h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <div class="mb-3">
                                                <label for="current_password" class="form-label">Current Password</label>
                                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            </div>
                                            <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Log Card -->
                            <div class="col-md-4">
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Recent Activity</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($recent_activities)): ?>
                                            <p class="text-muted">No recent activity.</p>
                                        <?php else: ?>
                                            <?php foreach ($recent_activities as $activity): ?>
                                                <div class="activity-item mb-3 pb-3 border-bottom">
                                                    <h6 class="mb-1"><?= ucfirst(str_replace('_', ' ', $activity['action'])) ?></h6>
                                                    <p class="text-muted mb-1 small"><?= htmlspecialchars($activity['details']) ?></p>
                                                    <small class="text-muted"><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></small>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

<?php include 'includes/layout_end.php'; ?>