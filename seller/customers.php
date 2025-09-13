<?php
$page_title = "Customers";
require_once 'config/database.php';

// Get customer statistics
$stats_query = "
    SELECT 
        COUNT(DISTINCT u.id) as total_customers,
        COUNT(DISTINCT CASE WHEN u.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN u.id END) as new_this_month,
        COUNT(DISTINCT CASE WHEN o.id IS NOT NULL THEN o.user_id END) as returning_customers,
        COUNT(DISTINCT CASE WHEN customer_stats.total_spent >= 1000 THEN customer_stats.user_id END) as vip_customers
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id 
    LEFT JOIN (
        SELECT user_id, SUM(total_amount) as total_spent
        FROM orders 
        WHERE status != 'cancelled'
        GROUP BY user_id
    ) customer_stats ON u.id = customer_stats.user_id
    WHERE u.status = 'active'
";
$stats = $pdo->query($stats_query)->fetch();

// Get customers with their order statistics
$customers_query = "
    SELECT 
        u.id,
        u.email,
        u.first_name,
        u.last_name,
        u.profile_image,
        u.created_at,
        COUNT(o.id) as total_orders,
        COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN o.total_amount ELSE 0 END), 0) as total_spent,
        MAX(o.created_at) as last_order_date,
        CASE 
            WHEN COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN o.total_amount ELSE 0 END), 0) >= 1000 THEN 'VIP Customer'
            WHEN COUNT(o.id) > 0 THEN 'Regular Customer'
            WHEN u.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 'New Customer'
            ELSE 'Inactive'
        END as customer_type
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id AND o.status != 'cancelled'
    WHERE u.status = 'active'
    GROUP BY u.id, u.email, u.first_name, u.last_name, u.profile_image, u.created_at
    ORDER BY total_spent DESC, total_orders DESC
    LIMIT 12
";
$customers = $pdo->query($customers_query)->fetchAll();

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

function getProfileImage($user) {
    if ($user['profile_image']) {
        return 'http://localhost' . $user['profile_image'];
    }
    return 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . ' ' . $user['last_name']) . '&background=d4a574&color=fff&size=64';
}
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Customers</h1>
                <p class="text-gray-600">Manage your customer relationships and view customer insights</p>
            </div>

            <!-- Customer Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-beige rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Customers</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_customers']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">New This Month</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['new_this_month']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-redo text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Returning</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['returning_customers']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">VIP Customers</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['vip_customers']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="Search customers..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>All Customers</option>
                            <option>VIP Customers</option>
                            <option>New Customers</option>
                            <option>Returning Customers</option>
                            <option>Inactive</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                            <option>Sort by Name</option>
                            <option>Sort by Orders</option>
                            <option>Sort by Spent</option>
                            <option>Sort by Join Date</option>
                        </select>
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($customers)): ?>
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Customers Yet</h3>
                        <p class="text-gray-500">When customers place orders, they'll appear here.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                        <!-- Customer Card -->
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center mb-4">
                                <img src="<?= htmlspecialchars(getProfileImage($customer)) ?>" 
                                     alt="<?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?>" 
                                     class="w-16 h-16 rounded-full">
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900">
                                        <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($customer['email']) ?></p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= getCustomerTypeClass($customer['customer_type']) ?> mt-1">
                                        <?= htmlspecialchars($customer['customer_type']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Orders</p>
                                    <p class="font-semibold text-gray-900"><?= number_format($customer['total_orders']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Spent</p>
                                    <p class="font-semibold text-beige"><?= formatCurrency($customer['total_spent']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Last Order</p>
                                    <p class="font-semibold text-gray-900">
                                        <?php if ($customer['last_order_date']): ?>
                                            <?= date('M j, Y', strtotime($customer['last_order_date'])) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">Never</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Member Since</p>
                                    <p class="font-semibold text-gray-900"><?= date('M Y', strtotime($customer['created_at'])) ?></p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex space-x-2">
                                    <button class="flex-1 btn-beige text-sm py-2" onclick="viewCustomer(<?= $customer['id'] ?>)">
                                        <i class="fas fa-eye mr-2"></i>View Profile
                                    </button>
                                    <button class="px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-full hover:bg-gray-50" 
                                            onclick="contactCustomer('<?= htmlspecialchars($customer['email']) ?>')">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex items-center justify-center">
                <div class="flex items-center space-x-2">
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Previous</button>
                    <button class="px-3 py-2 text-sm bg-beige text-white rounded-lg">1</button>
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                    <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function viewCustomer(customerId) {
            // Navigate to customer profile page
            window.location.href = 'customer-profile.php?id=' + customerId;
        }

        function contactCustomer(email) {
            // Open email client with customer's email
            window.location.href = 'mailto:' + email;
        }

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[placeholder="Search customers..."]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const customerCards = document.querySelectorAll('.grid > div:not(.col-span-full)');
                    
                    customerCards.forEach(card => {
                        const customerName = card.querySelector('h3').textContent.toLowerCase();
                        const customerEmail = card.querySelector('.text-gray-600').textContent.toLowerCase();
                        
                        if (customerName.includes(searchTerm) || customerEmail.includes(searchTerm)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>