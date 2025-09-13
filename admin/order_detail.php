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

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) {
    header('Location: orders.php');
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
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
                $success_message = 'Order status updated successfully.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = 'Error updating order status.';
            }
        }
    }
    
    if ($action === 'update_tracking') {
        $tracking_number = $_POST['tracking_number'] ?? '';
        $courier_company = $_POST['courier_company'] ?? '';
        
        try {
            $stmt = $pdo->prepare("UPDATE orders SET tracking_number = ?, courier_company = ? WHERE id = ?");
            $stmt->execute([$tracking_number, $courier_company, $order_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'order_tracking_updated', 'order', $order_id, 'Order tracking information updated', $_SERVER['REMOTE_ADDR']]);
            
            $success_message = 'Tracking information updated successfully.';
        } catch (PDOException $e) {
            $error_message = 'Error updating tracking information.';
        }
    }
}

try {
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name,
               u.email as customer_email,
               u.phone as customer_phone
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        header('Location: orders.php');
        exit();
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as current_product_name, s.store_name,
               (SELECT image_url FROM product_images WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) as product_image
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN sellers s ON oi.seller_id = s.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll();
    
    // Get order status history
    $stmt = $pdo->prepare("
        SELECT osh.*, au.username as admin_name
        FROM order_status_history osh
        LEFT JOIN admin_users au ON osh.created_by = au.id
        WHERE osh.order_id = ?
        ORDER BY osh.created_at DESC
    ");
    $stmt->execute([$order_id]);
    $status_history = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error_message = 'Error fetching order details.';
    $order = null;
    $order_items = [];
    $status_history = [];
}

if (!$order) {
    header('Location: orders.php');
    exit();
}

// Page-specific variables
$page_title = 'Order #' . htmlspecialchars($order['order_number']);
$page_description = 'Order details for order #' . htmlspecialchars($order['order_number']);

// Include the shared layout
include 'includes/layout_start.php';
?>
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">Order Details</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="orders.php">Orders</a></li>
                                        <li class="breadcrumb-item active">#<?php echo htmlspecialchars($order['order_number']); ?></li>
                                    </ol>
                                </nav>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" onclick="window.print()">
                                    <i class="fe fe-printer"></i> Print
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

                        <!-- Order Header -->
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title mb-0">Order #<?php echo htmlspecialchars($order['order_number']); ?></h4>
                                        <small class="text-muted">Placed on <?php echo date('F d, Y \a\t h:i A', strtotime($order['created_at'])); ?></small>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge badge-<?php 
                                            echo $order['status'] === 'delivered' ? 'success' : 
                                                ($order['status'] === 'shipped' ? 'primary' : 
                                                ($order['status'] === 'processing' ? 'info' : 
                                                ($order['status'] === 'cancelled' ? 'danger' : 'warning'))); 
                                        ?> badge-lg">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6>Customer Information</h6>
                                        <p class="mb-1"><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                                        <p class="mb-1"><?php echo htmlspecialchars($order['customer_email']); ?></p>
                                        <?php if (!empty($order['customer_phone'])): ?>
                                            <p class="mb-1"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Payment Information</h6>
                                        <p class="mb-1">
                                            <span class="badge badge-<?php 
                                                echo $order['payment_status'] === 'paid' ? 'success' : 
                                                    ($order['payment_status'] === 'pending' ? 'warning' : 
                                                    ($order['payment_status'] === 'failed' ? 'danger' : 'secondary')); 
                                            ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </p>
                                        <p class="mb-1">Method: <strong><?php echo strtoupper($order['payment_method']); ?></strong></p>
                                        <?php if (!empty($order['payment_reference'])): ?>
                                            <p class="mb-1">Ref: <?php echo htmlspecialchars($order['payment_reference']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Order Summary</h6>
                                        <p class="mb-1">Total: <strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></p>
                                        <p class="mb-1">Items: <strong><?php echo count($order_items); ?></strong></p>
                                        <?php if (!empty($order['tracking_number'])): ?>
                                            <p class="mb-1">Tracking: <strong><?php echo htmlspecialchars($order['tracking_number']); ?></strong></p>
                                            <p class="mb-1">Courier: <?php echo htmlspecialchars($order['courier_company']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Order Items -->
                            <div class="col-md-8">
                                <div class="card shadow mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Order Items</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Seller</th>
                                                        <th>Qty</th>
                                                        <th>Unit Price</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($order_items as $item): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <?php if (!empty($item['product_image'])): ?>
                                                                        <img src="<?php echo htmlspecialchars($item['product_image']); ?>" 
                                                                             alt="Product" class="avatar avatar-sm rounded mr-2" 
                                                                             style="object-fit: cover; width: 40px; height: 40px; max-width: 40px; max-height: 40px;">
                                                                    <?php else: ?>
                                                                        <div class="avatar avatar-sm rounded mr-2 bg-light d-flex align-items-center justify-content-center">
                                                                            <i class="fe fe-image text-muted"></i>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div>
                                                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                                                        <?php if (!empty($item['product_sku'])): ?>
                                                                            <br><small class="text-muted">SKU: <?php echo htmlspecialchars($item['product_sku']); ?></small>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($item['variant_details'])): ?>
                                                                            <br><small class="text-muted"><?php echo htmlspecialchars($item['variant_details']); ?></small>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($item['store_name']); ?></td>
                                                            <td><?php echo number_format($item['quantity']); ?></td>
                                                            <td>₱<?php echo number_format($item['unit_price'], 2); ?></td>
                                                            <td><strong>₱<?php echo number_format($item['total_price'], 2); ?></strong></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                                        <td><strong>₱<?php echo number_format($order['subtotal'], 2); ?></strong></td>
                                                    </tr>
                                                    <?php if ($order['tax_amount'] > 0): ?>
                                                        <tr>
                                                            <td colspan="4" class="text-right">Tax:</td>
                                                            <td>₱<?php echo number_format($order['tax_amount'], 2); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php if ($order['shipping_cost'] > 0): ?>
                                                        <tr>
                                                            <td colspan="4" class="text-right">Shipping:</td>
                                                            <td>₱<?php echo number_format($order['shipping_cost'], 2); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php if ($order['discount_amount'] > 0): ?>
                                                        <tr>
                                                            <td colspan="4" class="text-right">Discount:</td>
                                                            <td>-₱<?php echo number_format($order['discount_amount'], 2); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <tr class="table-active">
                                                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                                        <td><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Shipping Address -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card shadow mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Shipping Address</h6>
                                            </div>
                                            <div class="card-body">
                                                <address>
                                                    <strong><?php echo htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']); ?></strong><br>
                                                    <?php if (!empty($order['shipping_company'])): ?>
                                                        <?php echo htmlspecialchars($order['shipping_company']); ?><br>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($order['shipping_address_1']); ?><br>
                                                    <?php if (!empty($order['shipping_address_2'])): ?>
                                                        <?php echo htmlspecialchars($order['shipping_address_2']); ?><br>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state']); ?><br>
                                                    <?php echo htmlspecialchars($order['shipping_postal_code'] . ' ' . $order['shipping_country']); ?><br>
                                                    <?php if (!empty($order['shipping_phone'])): ?>
                                                        Tel: <?php echo htmlspecialchars($order['shipping_phone']); ?>
                                                    <?php endif; ?>
                                                </address>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card shadow mb-4">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Billing Address</h6>
                                            </div>
                                            <div class="card-body">
                                                <address>
                                                    <strong><?php echo htmlspecialchars($order['billing_first_name'] . ' ' . $order['billing_last_name']); ?></strong><br>
                                                    <?php if (!empty($order['billing_company'])): ?>
                                                        <?php echo htmlspecialchars($order['billing_company']); ?><br>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($order['billing_address_1']); ?><br>
                                                    <?php if (!empty($order['billing_address_2'])): ?>
                                                        <?php echo htmlspecialchars($order['billing_address_2']); ?><br>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($order['billing_city'] . ', ' . $order['billing_state']); ?><br>
                                                    <?php echo htmlspecialchars($order['billing_postal_code'] . ' ' . $order['billing_country']); ?><br>
                                                    <?php if (!empty($order['billing_phone'])): ?>
                                                        Tel: <?php echo htmlspecialchars($order['billing_phone']); ?>
                                                    <?php endif; ?>
                                                </address>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Actions & History -->
                            <div class="col-md-4">
                                <!-- Quick Actions -->
                                <div class="card shadow mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Quick Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <button class="btn btn-outline-primary btn-block mb-2" onclick="showStatusModal()">
                                            <i class="fe fe-edit"></i> Update Status
                                        </button>
                                        <button class="btn btn-outline-info btn-block mb-2" onclick="showTrackingModal()">
                                            <i class="fe fe-truck"></i> Update Tracking
                                        </button>
                                        <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
                                            <button class="btn btn-outline-danger btn-block" onclick="confirmCancel()">
                                                <i class="fe fe-x"></i> Cancel Order
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Order History -->
                                <div class="card shadow">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Order History</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($status_history)): ?>
                                            <p class="text-muted">No status history available.</p>
                                        <?php else: ?>
                                            <div class="timeline">
                                                <?php foreach ($status_history as $history): ?>
                                                    <div class="timeline-item">
                                                        <div class="timeline-marker bg-<?php 
                                                            echo $history['status'] === 'delivered' ? 'success' : 
                                                                ($history['status'] === 'shipped' ? 'primary' : 
                                                                ($history['status'] === 'processing' ? 'info' : 
                                                                ($history['status'] === 'cancelled' ? 'danger' : 'warning'))); 
                                                        ?>"></div>
                                                        <div class="timeline-content">
                                                            <h6 class="mb-1"><?php echo ucfirst($history['status']); ?></h6>
                                                            <p class="text-muted mb-1">
                                                                <?php echo date('M d, Y h:i A', strtotime($history['created_at'])); ?>
                                                            </p>
                                                            <?php if (!empty($history['notes'])): ?>
                                                                <p class="mb-1"><?php echo htmlspecialchars($history['notes']); ?></p>
                                                            <?php endif; ?>
                                                            <?php if (!empty($history['admin_name'])): ?>
                                                                <small class="text-muted">by <?php echo htmlspecialchars($history['admin_name']); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
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
                        
                        <div class="form-group">
                            <label for="new_status">New Status</label>
                            <select class="form-control" name="new_status" id="new_status" required>
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="refunded" <?php echo $order['status'] === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
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
                        
                        <div class="form-group">
                            <label for="courier_company">Courier Company</label>
                            <select class="form-control" name="courier_company" id="courier_company">
                                <option value="">Select Courier</option>
                                <option value="LBC" <?php echo $order['courier_company'] === 'LBC' ? 'selected' : ''; ?>>LBC Express</option>
                                <option value="J&T" <?php echo $order['courier_company'] === 'J&T' ? 'selected' : ''; ?>>J&T Express</option>
                                <option value="Ninja Van" <?php echo $order['courier_company'] === 'Ninja Van' ? 'selected' : ''; ?>>Ninja Van</option>
                                <option value="2GO" <?php echo $order['courier_company'] === '2GO' ? 'selected' : ''; ?>>2GO Express</option>
                                <option value="Grab" <?php echo $order['courier_company'] === 'Grab' ? 'selected' : ''; ?>>Grab Express</option>
                                <option value="Lalamove" <?php echo $order['courier_company'] === 'Lalamove' ? 'selected' : ''; ?>>Lalamove</option>
                                <option value="Other" <?php echo $order['courier_company'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tracking_number">Tracking Number</label>
                            <input type="text" class="form-control" name="tracking_number" id="tracking_number" 
                                   value="<?php echo htmlspecialchars($order['tracking_number']); ?>" 
                                   placeholder="Enter tracking number">
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

<?php
// Set page-specific JavaScript
$inline_js = '
// Timeline CSS styles
$("<style>").appendTo("head").text(`
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: "";
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 10px;
}
`);

// Page-specific functions
function showStatusModal() {
    $("#statusModal").modal("show");
}

function showTrackingModal() {
    $("#trackingModal").modal("show");
}

function confirmCancel() {
    window.confirmModal.confirm("Are you sure you want to cancel this order?", function(confirmed) {
        if (confirmed) {
            $("#new_status").val("cancelled");
            $("#statusModal").modal("show");
        }
    }, {title: 'Cancel Order', confirmText: 'Cancel Order', confirmClass: 'btn-danger'});
}
';

// Include the shared layout end
include 'includes/layout_end.php';
?>