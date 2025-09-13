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

// Get product ID
$product_id = (int)($_GET['id'] ?? 0);

if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

try {
    // Get basic product details first
    $stmt = $pdo->prepare("
        SELECT p.*, s.store_name, s.store_description, s.business_type,
               c.name as category_name, c.id as category_id
        FROM products p 
        LEFT JOIN sellers s ON p.seller_id = s.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header('Location: products.php');
        exit();
    }
    
    // Get review statistics
    $review_stats_stmt = $pdo->prepare("
        SELECT COUNT(*) as review_count, AVG(rating) as avg_rating
        FROM product_reviews 
        WHERE product_id = ? AND is_approved = 1
    ");
    $review_stats_stmt->execute([$product_id]);
    $review_stats = $review_stats_stmt->fetch();
    $product['review_count'] = $review_stats['review_count'] ?? 0;
    $product['avg_rating'] = $review_stats['avg_rating'] ?? 0;
    
    // Get product images
    $images_stmt = $pdo->prepare("
        SELECT * FROM product_images 
        WHERE product_id = ? 
        ORDER BY is_primary DESC, sort_order ASC, created_at ASC
    ");
    $images_stmt->execute([$product_id]);
    $images = $images_stmt->fetchAll();
    
    // Get product variants
    $variants_stmt = $pdo->prepare("
        SELECT * FROM product_variants 
        WHERE product_id = ? 
        ORDER BY variant_name
    ");
    $variants_stmt->execute([$product_id]);
    $variants = $variants_stmt->fetchAll();
    
    // Get recent reviews (limit to 5)
    $reviews_stmt = $pdo->prepare("
        SELECT pr.*, u.first_name, u.last_name, u.email
        FROM product_reviews pr
        LEFT JOIN users u ON pr.user_id = u.id
        WHERE pr.product_id = ? AND pr.is_approved = 1
        ORDER BY pr.created_at DESC
        LIMIT 5
    ");
    $reviews_stmt->execute([$product_id]);
    $reviews = $reviews_stmt->fetchAll();
    
    // Get order statistics for this product
    $order_stats_stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT oi.order_id) as total_orders,
            SUM(oi.quantity) as total_sold,
            SUM(oi.total_price) as total_revenue
        FROM order_items oi
        INNER JOIN orders o ON oi.order_id = o.id
        WHERE oi.product_id = ? AND o.status NOT IN ('cancelled', 'failed')
    ");
    $order_stats_stmt->execute([$product_id]);
    $order_stats = $order_stats_stmt->fetch();
    
} catch (PDOException $e) {
    $error_message = 'Error fetching product details.';
    header('Location: products.php');
    exit();
}

// Page-specific variables
$page_title = 'Product Details - ' . htmlspecialchars($product['name']);
$page_description = 'Product details for ' . htmlspecialchars($product['name']);

// Include layout start
include 'includes/layout_start.php';
?>

<style>
/* Star rating styles */
.fe-star {
    color: #e0e0e0; /* Empty star color */
}
.fe-star.fill {
    color: #ffc107 !important; /* Filled star color - golden yellow */
    text-shadow: 0 0 1px rgba(255, 193, 7, 0.5);
}
.text-warning .fe-star {
    color: #e0e0e0; /* Empty stars */
}
.text-warning .fe-star.fill {
    color: #ffc107 !important; /* Filled stars */
    text-shadow: 0 0 1px rgba(255, 193, 7, 0.5);
}
</style>

<div class="row align-items-center mb-2">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
    </div>
    <div class="col-auto">
        <a href="products.php" class="btn btn-outline-secondary">
            <i class="fe fe-arrow-left"></i> Back to Products
        </a>
    </div>
</div>

<!-- Product Header -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex align-items-start">
                    <?php if (!empty($images) && !empty($images[0]['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($images[0]['image_url']); ?>" 
                             alt="Product" class="rounded mr-3" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded mr-3 d-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; min-width: 80px;">
                            <i class="fe fe-image text-muted" style="font-size: 24px;"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="h4 mb-1"><?php echo htmlspecialchars($product['name']); ?></h2>
                        <p class="text-muted mb-2">SKU: <code><?php echo htmlspecialchars($product['sku']); ?></code></p>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-<?php 
                                echo $product['status'] === 'published' ? 'success' : 
                                    ($product['status'] === 'draft' ? 'warning' : 'secondary'); 
                            ?> mr-2">
                                <?php echo ucfirst($product['status']); ?>
                            </span>
                            <?php if ($product['featured']): ?>
                                <span class="badge badge-warning mr-2">
                                    <i class="fe fe-star"></i> Featured
                                </span>
                            <?php endif; ?>
                            <span class="text-muted">
                                Created: <?php echo date('M d, Y', strtotime($product['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="mb-2">
                    <strong class="h4 text-primary">₱<?php echo number_format($product['price'], 2); ?></strong>
                    <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                        <div>
                            <small class="text-muted text-decoration-line-through">₱<?php echo number_format($product['compare_price'], 2); ?></small>
                            <small class="text-success ml-1">
                                (<?php echo round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100); ?>% off)
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="../customer/products/detail.php?id=<?php echo $product['id']; ?>" target="_blank" class="btn btn-outline-primary">
                    <i class="fe fe-external-link"></i> View on Store
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Product Information -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Product Name</label>
                            <p><?php echo htmlspecialchars($product['name']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Category</label>
                            <p><?php echo htmlspecialchars($product['category_name'] ?: 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Seller</label>
                            <p>
                                <a href="seller_detail.php?id=<?php echo $product['seller_id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($product['store_name']); ?>
                                </a>
                                <?php if (!empty($product['business_type'])): ?>
                                    <br><small class="text-muted"><?php echo ucfirst($product['business_type']); ?> Account</small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Weight</label>
                            <p><?php echo $product['weight'] ? number_format($product['weight'], 2) . ' kg' : 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($product['short_description'])): ?>
                <div class="form-group">
                    <label class="font-weight-bold">Short Description</label>
                    <p><?php echo nl2br(htmlspecialchars($product['short_description'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($product['description'])): ?>
                <div class="form-group">
                    <label class="font-weight-bold">Description</label>
                    <div class="border p-3 rounded bg-light">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Images -->
        <?php if (!empty($images)): ?>
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Images (<?php echo count($images); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($images as $image): ?>
                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                            <div class="position-relative">
                                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                     alt="Product Image" class="img-fluid rounded border">
                                <?php if ($image['is_primary']): ?>
                                    <span class="badge badge-primary position-absolute" style="top: 5px; left: 5px;">Primary</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($image['alt_text'])): ?>
                                <small class="text-muted d-block mt-1"><?php echo htmlspecialchars($image['alt_text']); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Product Variants -->
        <?php if (!empty($variants)): ?>
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Variants (<?php echo count($variants); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Variant Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>SKU</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variants as $variant): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($variant['variant_name'] ?: 'Default'); ?></td>
                                    <td>
                                        <?php if (!empty($variant['price'])): ?>
                                            <strong>₱<?php echo number_format($variant['price'], 2); ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">Base Price</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $variant['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                            <?php echo number_format($variant['stock_quantity']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($variant['sku'] ?: 'N/A'); ?></code>
                                    </td>
                                    <td>
                                        <?php if (!empty($variant['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($variant['image_url']); ?>" 
                                                 alt="Variant" class="rounded" style="width: 30px; height: 30px; object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Recent Reviews -->
        <?php if (!empty($reviews)): ?>
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Reviews</h5>
                <small class="text-muted">Showing <?php echo count($reviews); ?> of <?php echo $product['review_count']; ?> reviews</small>
            </div>
            <div class="card-body">
                <?php foreach ($reviews as $review): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></strong>
                                <div class="text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $review['rating']): ?>
                                            <span style="color: #ffc107; font-size: 14px;">★</span>
                                        <?php else: ?>
                                            <span style="color: #e0e0e0; font-size: 14px;">★</span>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                        </div>
                        <?php if (!empty($review['review_text'])): ?>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <?php if ($product['review_count'] > count($reviews)): ?>
                    <div class="text-center">
                        <small class="text-muted">
                            Showing <?php echo count($reviews); ?> of <?php echo $product['review_count']; ?> reviews
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 border-right">
                        <div class="mb-2">
                            <h4 class="mb-0 text-primary"><?php echo number_format($order_stats['total_orders'] ?: 0); ?></h4>
                            <small class="text-muted">Total Orders</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-2">
                            <h4 class="mb-0 text-success"><?php echo number_format($order_stats['total_sold'] ?: 0); ?></h4>
                            <small class="text-muted">Units Sold</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6 border-right">
                        <div class="mb-2">
                            <h4 class="mb-0 text-info">₱<?php echo number_format($order_stats['total_revenue'] ?: 0, 2); ?></h4>
                            <small class="text-muted">Total Revenue</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-2">
                            <h4 class="mb-0 text-warning">
                                <?php if ($product['review_count'] > 0): ?>
                                    <?php echo number_format($product['avg_rating'], 1); ?>
                                    <span style="color: #ffc107; font-size: 0.8em;">★</span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </h4>
                            <small class="text-muted"><?php echo number_format($product['review_count']); ?> Reviews</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Inventory & Pricing -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Inventory & Pricing</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">Current Stock</label>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?> mr-2">
                            <?php echo number_format($product['stock_quantity']); ?> units
                        </span>
                        <?php if ($product['stock_quantity'] <= $product['low_stock_threshold']): ?>
                            <small class="text-warning">Low Stock Alert</small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Low Stock Threshold</label>
                    <p><?php echo number_format($product['low_stock_threshold']); ?> units</p>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Regular Price</label>
                    <p class="h5 text-primary mb-0">₱<?php echo number_format($product['price'], 2); ?></p>
                </div>
                
                <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
                <div class="form-group">
                    <label class="font-weight-bold">Compare Price</label>
                    <p class="text-muted text-decoration-line-through">₱<?php echo number_format($product['compare_price'], 2); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- SEO Information -->
        <?php if (!empty($product['meta_title']) || !empty($product['meta_description'])): ?>
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">SEO Information</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($product['meta_title'])): ?>
                <div class="form-group">
                    <label class="font-weight-bold">Meta Title</label>
                    <p><?php echo htmlspecialchars($product['meta_title']); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($product['meta_description'])): ?>
                <div class="form-group">
                    <label class="font-weight-bold">Meta Description</label>
                    <p><?php echo htmlspecialchars($product['meta_description']); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Last Updated -->
        <div class="card shadow">
            <div class="card-body">
                <small class="text-muted d-block">
                    <strong>Created:</strong> <?php echo date('M d, Y \a\t g:i A', strtotime($product['created_at'])); ?>
                </small>
                <?php if ($product['updated_at'] && $product['updated_at'] !== $product['created_at']): ?>
                <small class="text-muted d-block">
                    <strong>Last Updated:</strong> <?php echo date('M d, Y \a\t g:i A', strtotime($product['updated_at'])); ?>
                </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/layout_end.php'; ?>