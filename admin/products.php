<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();

// Check permissions
if (!hasPermission('manage_products')) {
    header('Location: dashboard.php');
    exit();
}

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if ($action === 'toggle_status' && $product_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET status = CASE WHEN status = 'published' THEN 'archived' ELSE 'published' END WHERE id = ?");
            $stmt->execute([$product_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'product_status_updated', 'product', $product_id, 'Product status toggled', $_SERVER['REMOTE_ADDR']]);
            
            header('Location: products.php?msg=status_updated');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error updating product status.';
        }
    }
    
    if ($action === 'toggle_featured' && $product_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET featured = !featured WHERE id = ?");
            $stmt->execute([$product_id]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(['admin', getAdminId(), 'product_featured_updated', 'product', $product_id, 'Product featured status toggled', $_SERVER['REMOTE_ADDR']]);
            
            header('Location: products.php?msg=featured_updated');
            exit();
        } catch (PDOException $e) {
            $error_message = 'Error updating featured status.';
        }
    }
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$seller_filter = $_GET['seller'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(p.name LIKE ? OR p.sku LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
}

if (!empty($status_filter)) {
    $where_conditions[] = "p.status = ?";
    $params[] = $status_filter;
}

if (!empty($category_filter)) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($seller_filter)) {
    $where_conditions[] = "p.seller_id = ?";
    $params[] = $seller_filter;
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

try {
    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM products p 
        LEFT JOIN sellers s ON p.seller_id = s.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause
    ");
    $count_stmt->execute($params);
    $total_products = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_products / $per_page);
    
    // Get products with pagination
    $stmt = $pdo->prepare("
        SELECT p.*, s.store_name, c.name as category_name,
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p 
        LEFT JOIN sellers s ON p.seller_id = s.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause 
        ORDER BY p.created_at DESC 
        LIMIT $per_page OFFSET $offset
    ");
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Get categories for filter dropdown
    $categories_stmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
    $categories = $categories_stmt->fetchAll();
    
    // Get sellers for filter dropdown
    $sellers_stmt = $pdo->query("SELECT id, store_name FROM sellers WHERE status = 'approved' ORDER BY store_name");
    $sellers = $sellers_stmt->fetchAll();
    
    // Get status counts
    $status_counts = [];
    $status_stmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM products 
        GROUP BY status
    ");
    while ($row = $status_stmt->fetch()) {
        $status_counts[$row['status']] = $row['count'];
    }
    
} catch (PDOException $e) {
    $error_message = 'Error fetching products data.';
    $products = [];
    $categories = [];
    $sellers = [];
    $total_products = 0;
    $total_pages = 0;
    $status_counts = [];
}

// Page-specific variables
$page_title = 'Products Management';
$page_description = 'Manage all products in the Core1 E-commerce platform';
$additional_css = ['css/dataTables.bootstrap4.css'];

// Include layout start
include 'includes/layout_start.php';
?>
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <h2 class="h5 page-title">Products Management</h2>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if (isset($_GET['msg'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php
                                switch ($_GET['msg']) {
                                    case 'status_updated':
                                        echo 'Product status updated successfully.';
                                        break;
                                    case 'featured_updated':
                                        echo 'Product featured status updated successfully.';
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
                                        <a class="nav-link <?php echo empty($status_filter) ? 'active' : ''; ?>" href="products.php">
                                            All Products
                                            <span class="badge badge-light ml-1"><?php echo array_sum($status_counts); ?></span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'published' ? 'active' : ''; ?>" href="products.php?status=published">
                                            Published
                                            <?php if (($status_counts['published'] ?? 0) > 0): ?>
                                                <span class="badge badge-success ml-1"><?php echo $status_counts['published']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'draft' ? 'active' : ''; ?>" href="products.php?status=draft">
                                            Draft
                                            <?php if (($status_counts['draft'] ?? 0) > 0): ?>
                                                <span class="badge badge-warning ml-1"><?php echo $status_counts['draft']; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $status_filter === 'archived' ? 'active' : ''; ?>" href="products.php?status=archived">
                                            Archived
                                            <?php if (($status_counts['archived'] ?? 0) > 0): ?>
                                                <span class="badge badge-secondary ml-1"><?php echo $status_counts['archived']; ?></span>
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
                                                       placeholder="Search products...">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="form-control" name="category">
                                                    <option value="">All Categories</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo $category['id']; ?>" 
                                                                <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($category['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="form-control" name="seller">
                                                    <option value="">All Sellers</option>
                                                    <?php foreach ($sellers as $seller): ?>
                                                        <option value="<?php echo $seller['id']; ?>" 
                                                                <?php echo $seller_filter == $seller['id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($seller['store_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="products.php<?php echo !empty($status_filter) ? '?status=' . urlencode($status_filter) : ''; ?>" class="btn btn-outline-secondary">Clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="card shadow">
                            <div class="card-header">
                                <strong class="card-title">
                                    <?php echo ucfirst($status_filter ?: 'All'); ?> Products (<?php echo number_format($total_products); ?> total)
                                </strong>
                            </div>
                            <div class="card-body">
                                <?php if (empty($products)): ?>
                                    <p class="text-muted text-center py-4">No products found.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>SKU</th>
                                                    <th>Category</th>
                                                    <th>Seller</th>
                                                    <th>Price</th>
                                                    <th>Stock</th>
                                                    <th>Status</th>
                                                    <th>Featured</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($products as $product): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <?php if (!empty($product['primary_image'])): ?>
                                                                    <img src="<?php echo htmlspecialchars($product['primary_image']); ?>" 
                                                                         alt="Product" class="avatar avatar-sm rounded mr-2" 
                                                                         style="width: 28px !important; height: 28px !important; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="avatar avatar-sm rounded mr-2 bg-light d-flex align-items-center justify-content-center" 
                                                                         style="width: 28px; height: 28px; min-width: 28px;">
                                                                        <i class="fe fe-image text-muted" style="font-size: 12px;"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                                    <?php if (!empty($product['short_description'])): ?>
                                                                        <br>
                                                                        <small class="text-muted"><?php echo htmlspecialchars(substr($product['short_description'], 0, 50)) . '...'; ?></small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <code><?php echo htmlspecialchars($product['sku']); ?></code>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($product['store_name']); ?></td>
                                                        <td>
                                                            <strong>₱<?php echo number_format($product['price'], 2); ?></strong>
                                                            <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                                                                <br>
                                                                <small class="text-muted text-decoration-line-through">₱<?php echo number_format($product['compare_price'], 2); ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                                <?php echo number_format($product['stock_quantity']); ?>
                                                            </span>
                                                            <?php if ($product['stock_quantity'] <= $product['low_stock_threshold']): ?>
                                                                <br>
                                                                <small class="text-warning">Low Stock</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $product['status'] === 'published' ? 'success' : 
                                                                    ($product['status'] === 'draft' ? 'warning' : 'secondary'); 
                                                            ?>">
                                                                <?php echo ucfirst($product['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($product['featured']): ?>
                                                                <i class="fe fe-star text-warning" title="Featured"></i>
                                                            <?php else: ?>
                                                                <i class="fe fe-star text-muted" title="Not Featured"></i>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="product_detail.php?id=<?php echo $product['id']; ?>">
                                                                        <i class="fe fe-eye"></i> View Details
                                                                    </a>
                                                                    <a class="dropdown-item" href="../customer/products/detail.php?id=<?php echo $product['id']; ?>" target="_blank">
                                                                        <i class="fe fe-external-link"></i> View on Store
                                                                    </a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="toggle_status">
                                                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fe fe-<?php echo $product['status'] === 'published' ? 'archive' : 'check'; ?>"></i>
                                                                            <?php echo $product['status'] === 'published' ? 'Archive' : 'Publish'; ?>
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST" class="d-inline">
                                                                        <input type="hidden" name="action" value="toggle_featured">
                                                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fe fe-star"></i>
                                                                            <?php echo $product['featured'] ? 'Remove Featured' : 'Set Featured'; ?>
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