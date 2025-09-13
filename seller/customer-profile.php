<?php
$page_title = "Customer Profile";
require_once 'config/database.php';

// Get customer ID from URL
$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$customer_id) {
    header('Location: customers.php');
    exit;
}

// Get customer details with statistics
$customer_query = "
    SELECT 
        u.id,
        u.email,
        u.first_name,
        u.last_name,
        u.phone,
        u.profile_image,
        u.date_of_birth,
        u.gender,
        u.email_verified,
        u.phone_verified,
        u.status,
        u.created_at,
        COUNT(o.id) as total_orders,
        COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN o.total_amount ELSE 0 END), 0) as total_spent,
        COALESCE(AVG(CASE WHEN o.status != 'cancelled' THEN o.total_amount ELSE NULL END), 0) as avg_order_value,
        MAX(o.created_at) as last_order_date,
        MIN(o.created_at) as first_order_date,
        COUNT(DISTINCT CASE WHEN o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN o.id END) as orders_last_30_days
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id
    WHERE u.id = ? AND u.status = 'active'
    GROUP BY u.id
";
$stmt = $pdo->prepare($customer_query);
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    header('Location: customers.php');
    exit;
}

// Get customer's recent orders
$orders_query = "
    SELECT 
        o.id,
        o.order_number,
        o.status,
        o.total_amount,
        o.created_at,
        o.payment_method,
        o.payment_status,
        COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 10
";
$stmt = $pdo->prepare($orders_query);
$stmt->execute([$customer_id]);
$recent_orders = $stmt->fetchAll();

// Get customer's addresses
$addresses_query = "
    SELECT *
    FROM user_addresses
    WHERE user_id = ?
    ORDER BY is_default DESC, created_at DESC
";
$stmt = $pdo->prepare($addresses_query);
$stmt->execute([$customer_id]);
$addresses = $stmt->fetchAll();

// Get order status distribution
$status_query = "
    SELECT 
        status,
        COUNT(*) as count,
        SUM(total_amount) as total_amount
    FROM orders
    WHERE user_id = ?
    GROUP BY status
";
$stmt = $pdo->prepare($status_query);
$stmt->execute([$customer_id]);
$order_status_stats = $stmt->fetchAll();

// Helper functions
function getCustomerType($customer) {
    if ($customer['total_spent'] >= 1000) return 'VIP Customer';
    if ($customer['total_orders'] > 0) return 'Regular Customer';
    if (strtotime($customer['created_at']) >= strtotime('-1 month')) return 'New Customer';
    return 'Inactive';
}

function getCustomerTypeClass($type) {
    switch($type) {
        case 'VIP Customer': return 'bg-yellow-100 text-yellow-800';
        case 'Regular Customer': return 'bg-green-100 text-green-800';
        case 'New Customer': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

function getProfileImage($customer) {
    if ($customer['profile_image']) {
        return 'http://localhost' . $customer['profile_image'];
    }
    return 'https://ui-avatars.com/api/?name=' . urlencode($customer['first_name'] . ' ' . $customer['last_name']) . '&background=d4a574&color=fff&size=128';
}

function getOrderStatusClass($status) {
    switch($status) {
        case 'delivered': return 'bg-green-100 text-green-800';
        case 'shipped': return 'bg-blue-100 text-blue-800';
        case 'processing': return 'bg-yellow-100 text-yellow-800';
        case 'pending': return 'bg-orange-100 text-orange-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

$customer_type = getCustomerType($customer);
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="customers.php" class="inline-flex items-center text-beige hover:text-beige-dark">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                </a>
            </div>

            <!-- Customer Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex items-center">
                        <img src="<?= htmlspecialchars(getProfileImage($customer)) ?>" 
                             alt="<?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?>" 
                             class="w-24 h-24 rounded-full">
                        <div class="ml-6">
                            <h1 class="text-3xl font-bold text-gray-800">
                                <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?>
                            </h1>
                            <p class="text-gray-600 mb-2"><?= htmlspecialchars($customer['email']) ?></p>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?= getCustomerTypeClass($customer_type) ?>">
                                    <?= $customer_type ?>
                                </span>
                                <?php if ($customer['email_verified']): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Email Verified
                                    </span>
                                <?php endif; ?>
                                <?php if ($customer['phone_verified']): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-phone mr-1"></i>Phone Verified
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button class="btn-beige" onclick="contactCustomer('<?= htmlspecialchars($customer['email']) ?>')">
                            <i class="fas fa-envelope mr-2"></i>Send Email
                        </button>
                        <?php if ($customer['phone']): ?>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50" 
                                    onclick="callCustomer('<?= htmlspecialchars($customer['phone']) ?>')">
                                <i class="fas fa-phone mr-2"></i>Call
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Customer Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($customer['total_orders']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-peso-sign text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Spent</p>
                            <p class="text-2xl font-bold text-beige"><?= formatCurrency($customer['total_spent']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Average Order</p>
                            <p class="text-2xl font-bold text-gray-900"><?= formatCurrency($customer['avg_order_value']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Last 30 Days</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($customer['orders_last_30_days']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Tabs -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6">
                        <button class="tab-button active py-4 px-1 border-b-2 border-beige font-medium text-sm text-beige" 
                                onclick="showTab('orders')">
                            Orders History
                        </button>
                        <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                onclick="showTab('info')">
                            Personal Info
                        </button>
                        <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                onclick="showTab('addresses')">
                            Addresses
                        </button>
                        <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" 
                                onclick="showTab('analytics')">
                            Analytics
                        </button>
                    </nav>
                </div>

                <!-- Orders Tab -->
                <div id="orders-tab" class="tab-content p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Orders</h3>
                    <?php if (empty($recent_orders)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Orders Yet</h3>
                            <p class="text-gray-500">This customer hasn't placed any orders.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['order_number']) ?></div>
                                                <div class="text-sm text-gray-500">#<?= $order['id'] ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                                                <div class="text-sm text-gray-500"><?= date('g:i A', strtotime($order['created_at'])) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?= $order['item_count'] ?> items</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?= formatCurrency($order['total_amount']) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?= ucfirst($order['payment_method']) ?></div>
                                                <div class="text-sm text-gray-500"><?= ucfirst($order['payment_status']) ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getOrderStatusClass($order['status']) ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="text-beige hover:text-beige-dark" onclick="viewOrder(<?= $order['id'] ?>)">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Personal Info Tab -->
                <div id="info-tab" class="tab-content p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <p class="text-gray-900"><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <p class="text-gray-900"><?= htmlspecialchars($customer['email']) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <p class="text-gray-900"><?= $customer['phone'] ? htmlspecialchars($customer['phone']) : 'Not provided' ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <p class="text-gray-900"><?= $customer['date_of_birth'] ? date('M j, Y', strtotime($customer['date_of_birth'])) : 'Not provided' ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                            <p class="text-gray-900"><?= $customer['gender'] ? ucfirst($customer['gender']) : 'Not provided' ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                            <p class="text-gray-900"><?= date('M j, Y', strtotime($customer['created_at'])) ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Order</label>
                            <p class="text-gray-900"><?= $customer['first_order_date'] ? date('M j, Y', strtotime($customer['first_order_date'])) : 'No orders yet' ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Order</label>
                            <p class="text-gray-900"><?= $customer['last_order_date'] ? date('M j, Y', strtotime($customer['last_order_date'])) : 'No orders yet' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Addresses Tab -->
                <div id="addresses-tab" class="tab-content p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Addresses</h3>
                    <?php if (empty($addresses)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-map-marker-alt text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Addresses</h3>
                            <p class="text-gray-500">This customer hasn't saved any addresses.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($addresses as $address): ?>
                                <div class="border border-gray-200 rounded-lg p-4 <?= $address['is_default'] ? 'border-beige bg-beige bg-opacity-5' : '' ?>">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-900"><?= htmlspecialchars($address['label']) ?></h4>
                                        <?php if ($address['is_default']): ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-beige text-white">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-1"><?= htmlspecialchars($address['street_address']) ?></p>
                                    <?php if ($address['apartment']): ?>
                                        <p class="text-gray-600 text-sm mb-1"><?= htmlspecialchars($address['apartment']) ?></p>
                                    <?php endif; ?>
                                    <p class="text-gray-600 text-sm">
                                        <?= htmlspecialchars($address['city'] . ', ' . $address['state'] . ' ' . $address['postal_code']) ?>
                                    </p>
                                    <?php if ($address['phone']): ?>
                                        <p class="text-gray-600 text-sm mt-2">
                                            <i class="fas fa-phone mr-1"></i><?= htmlspecialchars($address['phone']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Analytics Tab -->
                <div id="analytics-tab" class="tab-content p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Analytics</h3>
                    
                    <!-- Order Status Distribution -->
                    <?php if (!empty($order_status_stats)): ?>
                        <div class="mb-8">
                            <h4 class="text-md font-semibold text-gray-700 mb-4">Order Status Distribution</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <?php foreach ($order_status_stats as $stat): ?>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm text-gray-600"><?= ucfirst($stat['status']) ?> Orders</p>
                                                <p class="text-xl font-bold text-gray-900"><?= $stat['count'] ?></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-600">Total Value</p>
                                                <p class="text-lg font-semibold text-beige"><?= formatCurrency($stat['total_amount']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Customer Insights -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-700 mb-2">Customer Lifetime Value</h5>
                            <p class="text-2xl font-bold text-beige"><?= formatCurrency($customer['total_spent']) ?></p>
                            <p class="text-sm text-gray-600 mt-1">Total spent across all orders</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-700 mb-2">Purchase Frequency</h5>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                if ($customer['total_orders'] > 0 && $customer['first_order_date']) {
                                    $days_since_first = (time() - strtotime($customer['first_order_date'])) / (60 * 60 * 24);
                                    $frequency = $days_since_first > 0 ? round($days_since_first / $customer['total_orders']) : 0;
                                    echo $frequency . ' days';
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </p>
                            <p class="text-sm text-gray-600 mt-1">Average days between orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-beige', 'text-beige');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Add active class to selected button
            event.target.classList.add('active', 'border-beige', 'text-beige');
            event.target.classList.remove('border-transparent', 'text-gray-500');
        }

        function contactCustomer(email) {
            window.location.href = 'mailto:' + email;
        }

        function callCustomer(phone) {
            window.location.href = 'tel:' + phone;
        }

        function viewOrder(orderId) {
            // TODO: Implement order view
            console.log('Viewing order:', orderId);
            alert('Order details view will be implemented soon.');
        }
    </script>

<?php include 'includes/footer.php'; ?>