<?php
require_once 'config/auth.php';
require_once 'config/database.php';

requireAuth();

$seller_id = $_GET['id'] ?? null;
$success_message = '';
$error_message = '';

if (!$seller_id) {
    header('Location: sellers.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $new_status = $_POST['status'];
        $rejection_reason = $_POST['rejection_reason'] ?? '';
        
        try {
            $stmt = $pdo->prepare("UPDATE sellers SET status = ?, rejection_reason = ? WHERE id = ?");
            $stmt->execute([$new_status, $rejection_reason, $seller_id]);
            
            $success_message = "Seller status updated to " . ucfirst($new_status);
            
            // Log the activity
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $log_stmt->execute([
                $_SESSION['user_id'],
                'seller_status_update',
                "Updated seller ID {$seller_id} status to {$new_status}",
                $_SERVER['REMOTE_ADDR']
            ]);
        } catch (PDOException $e) {
            $error_message = 'Error updating seller status: ' . $e->getMessage();
        }
    }
}

// Get seller data with user information
try {
    $stmt = $pdo->prepare("
        SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.status as user_status, u.created_at as user_created_at,
               s.store_name as business_name, s.store_description as business_description
        FROM sellers s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.id = ?
    ");
    $stmt->execute([$seller_id]);
    $seller = $stmt->fetch();
    
    if (!$seller) {
        header('Location: sellers.php');
        exit;
    }
} catch (PDOException $e) {
    $error_message = 'Error loading seller data: ' . $e->getMessage();
}

// Get seller's products
$products = [];
try {
    $product_stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, pi.image_url 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE p.seller_id = ? 
        ORDER BY p.created_at DESC 
        LIMIT 10
    ");
    $product_stmt->execute([$seller_id]);
    $products = $product_stmt->fetchAll();
} catch (PDOException $e) {
    // Handle error silently
}

// Get seller's recent orders
$orders = [];
try {
    $order_stmt = $pdo->prepare("
        SELECT DISTINCT o.*, COUNT(oi.id) as item_count
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        JOIN products p ON oi.product_id = p.id 
        WHERE p.seller_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC 
        LIMIT 10
    ");
    $order_stmt->execute([$seller_id]);
    $orders = $order_stmt->fetchAll();
} catch (PDOException $e) {
    // Handle error silently
}

// Get seller statistics
$stats = [
    'total_products' => 0,
    'active_products' => 0,
    'total_orders' => 0,
    'total_revenue' => 0
];

try {
    // Product stats
    $product_stats = $pdo->prepare("
        SELECT 
            COUNT(*) as total_products,
            COUNT(CASE WHEN status = 'published' THEN 1 END) as active_products
        FROM products WHERE seller_id = ?
    ");
    $product_stats->execute([$seller_id]);
    $product_data = $product_stats->fetch();
    $stats['total_products'] = $product_data['total_products'];
    $stats['active_products'] = $product_data['active_products'];
    
    // Order and revenue stats
    $order_stats = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT o.id) as total_orders,
            COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        JOIN products p ON oi.product_id = p.id 
        WHERE p.seller_id = ?
    ");
    $order_stats->execute([$seller_id]);
    $order_data = $order_stats->fetch();
    $stats['total_orders'] = $order_data['total_orders'];
    $stats['total_revenue'] = $order_data['total_revenue'];
} catch (PDOException $e) {
    // Handle error silently
}

// Page-specific variables
$page_title = 'Seller Details - ' . htmlspecialchars($seller['business_name'] ?? 'Unknown Business');
$page_description = 'Detailed view and management of seller account';

// Include layout start
include 'includes/layout_start.php';
?>
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">Seller Details</h2>
                            </div>
                            <div class="col-auto">
                                <a href="sellers.php" class="btn btn-outline-secondary">
                                    <span class="fe fe-arrow-left fe-12 mr-2"></span>Back to Sellers
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

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-box fa-2x mb-2"></i>
                                <h4><?= $stats['total_products'] ?></h4>
                                <p class="mb-0">Total Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-eye fa-2x mb-2"></i>
                                <h4><?= $stats['active_products'] ?></h4>
                                <p class="mb-0">Active Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h4><?= $stats['total_orders'] ?></h4>
                                <p class="mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                <h4>$<?= number_format($stats['total_revenue'], 2) ?></h4>
                                <p class="mb-0">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Seller Profile -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <div class="seller-avatar mx-auto mb-3">
                                    <i class="fas fa-store"></i>
                                </div>
                                <h5><?= htmlspecialchars($seller['business_name'] ?? 'Unknown Business') ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?></p>
                                <span class="badge status-badge <?= 
                                    $seller['status'] === 'approved' ? 'bg-success' : 
                                    ($seller['status'] === 'pending' ? 'bg-warning' : 'bg-danger') 
                                ?>">
                                    <?= ucfirst($seller['status']) ?>
                                </span>
                                <hr>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <strong>Business Type</strong><br>
                                        <span class="badge bg-primary"><?= ucfirst($seller['business_type']) ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Applied</strong><br>
                                        <small class="text-muted"><?= date('M j, Y', strtotime($seller['created_at'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Management -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Status Management</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" onchange="toggleRejectionReason()">
                                            <option value="pending" <?= $seller['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="approved" <?= $seller['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="rejected" <?= $seller['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                            <option value="suspended" <?= $seller['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="rejection-reason-field" style="display: <?= $seller['status'] === 'rejected' ? 'block' : 'none' ?>;">
                                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                                  placeholder="Provide reason for rejection..."><?= htmlspecialchars($seller['rejection_reason'] ?? '') ?></textarea>
                                    </div>
                                    <button type="submit" name="update_status" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Status
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Seller Details -->
                    <div class="col-md-8">
                        <!-- Business Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Business Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Business Name:</strong></td>
                                                <td><?= htmlspecialchars($seller['business_name'] ?? 'Not provided') ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Business Type:</strong></td>
                                                <td><?= htmlspecialchars($seller['business_type']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Store Slug:</strong></td>
                                                <td><?= htmlspecialchars($seller['store_slug'] ?? 'Not provided') ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tax ID:</strong></td>
                                                <td><?= htmlspecialchars($seller['tax_id'] ?? 'Not provided') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Contact Person:</strong></td>
                                                <td><?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td><?= htmlspecialchars($seller['email']) ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phone:</strong></td>
                                                <td><?= htmlspecialchars($seller['phone'] ?? 'Not provided') ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Store Status:</strong></td>
                                                <td><span class="badge bg-<?= $seller['status'] === 'approved' ? 'success' : 'warning' ?>"><?= ucfirst($seller['status']) ?></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <?php if (isset($seller['business_description']) && $seller['business_description']): ?>
                                <hr>
                                <div>
                                    <strong>Business Description:</strong>
                                    <p class="mt-2"><?= nl2br(htmlspecialchars($seller['business_description'])) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Products -->
                        <?php if (!empty($products)): ?>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Recent Products</h6>
                                <small class="text-muted">Showing latest 10 products</small>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= htmlspecialchars($product['image_url'] ?? '/placeholder.jpg') ?>" 
                                                             alt="Product" style="width: 40px; height: 40px; object-fit: cover;" class="rounded me-2">
                                                        <div>
                                                            <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                                                            <small class="text-muted">#<?= $product['id'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
                                                <td>$<?= number_format($product['price'], 2) ?></td>
                                                <td><?= $product['stock_quantity'] ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $product['status'] === 'published' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst($product['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M j', strtotime($product['created_at'])) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Recent Orders -->
                        <?php if (!empty($orders)): ?>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Recent Orders</h6>
                                <small class="text-muted">Orders containing this seller's products</small>
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
                                                <th>Payment</th>
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
                                                <td>
                                                    <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst($order['payment_status']) ?>
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
<?php
// Inline JavaScript for page-specific functionality
$inline_js = '
function toggleRejectionReason() {
    const status = document.getElementById("status").value;
    const rejectionField = document.getElementById("rejection-reason-field");
    
    if (status === "rejected") {
        rejectionField.style.display = "block";
        document.getElementById("rejection_reason").required = true;
    } else {
        rejectionField.style.display = "none";
        document.getElementById("rejection_reason").required = false;
    }
}';

// Include layout end
include 'includes/layout_end.php';
?>