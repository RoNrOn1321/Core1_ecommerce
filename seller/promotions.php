<?php
$page_title = "Promotions";

require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/promotion.php';
require_once 'includes/layout.php';

$auth = new SellerAuth($pdo);
$auth->requireWebLogin();

$sellerId = $_SESSION['seller_id'];
$promotionManager = new PromotionManager($pdo);

// Get promotion statistics
$statsResult = $promotionManager->getPromotionStats($sellerId);
$stats = $statsResult['success'] ? $statsResult['stats'] : [
    'total_promotions' => 0,
    'active_promotions' => 0,
    'scheduled_promotions' => 0,
    'expired_promotions' => 0,
    'total_discount_given' => 0,
    'total_uses' => 0
];

// Get active promotions
$promotionsResult = $promotionManager->getPromotions($sellerId, ['status' => 'active', 'limit' => 10]);
$activePromotions = $promotionsResult['success'] ? $promotionsResult['promotions'] : [];

// Get top performing promotions (dummy data for now - could be enhanced)
$topPromotions = array_slice($activePromotions, 0, 3);

startLayout('Promotions & Discounts');
?>

        <div class="p-6">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Promotions & Discounts</h1>
                    <p class="text-gray-600">Create and manage promotional campaigns to boost your sales</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button id="createPromoBtn" class="btn-beige">
                        <i class="fas fa-plus mr-2"></i>Create Promotion
                    </button>
                </div>
            </div>

            <!-- Promotion Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Active Promotions</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['active_promotions']; ?></p>
                            <p class="text-sm text-green-600"><?php echo $stats['scheduled_promotions']; ?> scheduled</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Discount Given</p>
                            <p class="text-2xl font-bold text-gray-900">₱<?php echo number_format($stats['total_discount_given'], 2); ?></p>
                            <p class="text-sm text-blue-600">All time</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Uses</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_uses']; ?></p>
                            <p class="text-sm text-purple-600">All promotions</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-percentage text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Promotions</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_promotions']; ?></p>
                            <p class="text-sm text-yellow-600"><?php echo $stats['expired_promotions']; ?> expired</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Promotion Templates -->
            <div class="bg-white rounded-lg shadow-md mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Quick Start Templates</h3>
                    <p class="text-sm text-gray-600 mt-1">Choose from popular promotion types</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-beige transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-percent text-red-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Percentage Discount</h4>
                            <p class="text-sm text-gray-600">Give customers a percentage off their purchase</p>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-beige transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Fixed Amount Off</h4>
                            <p class="text-sm text-gray-600">Offer a fixed dollar amount discount</p>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-beige transition-colors cursor-pointer">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-gift text-blue-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Buy One Get One</h4>
                            <p class="text-sm text-gray-600">BOGO deals and bundle offers</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Promotions -->
            <div class="bg-white rounded-lg shadow-md mb-8">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Active Promotions</h3>
                        <div class="flex space-x-2">
                            <button class="filter-btn bg-beige text-white">All</button>
                            <button class="filter-btn">Percentage</button>
                            <button class="filter-btn">Fixed Amount</button>
                            <button class="filter-btn">BOGO</button>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (empty($activePromotions)): ?>
                        <div class="p-8 text-center text-gray-500">
                            <i class="fas fa-percent text-4xl text-gray-400 mb-4"></i>
                            <p>No active promotions</p>
                            <p class="text-sm text-gray-400 mt-2">Create your first promotion to start boosting sales</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activePromotions as $promo): 
                            $iconColors = [
                                'percentage' => ['bg-red-100', 'text-red-600', 'fas fa-percent'],
                                'fixed_amount' => ['bg-green-100', 'text-green-600', 'fas fa-dollar-sign'],
                                'free_shipping' => ['bg-blue-100', 'text-blue-600', 'fas fa-shipping-fast']
                            ];
                            $icon = $iconColors[$promo['type']] ?? ['bg-gray-100', 'text-gray-600', 'fas fa-tag'];
                            
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'scheduled' => 'bg-blue-100 text-blue-800',
                                'expired' => 'bg-red-100 text-red-800',
                                'inactive' => 'bg-gray-100 text-gray-800'
                            ];
                            $statusColor = $statusColors[$promo['computed_status']] ?? 'bg-gray-100 text-gray-800';
                            
                            $usagePercent = 0;
                            if ($promo['usage_limit'] && $promo['actual_usage_count']) {
                                $usagePercent = ($promo['actual_usage_count'] / $promo['usage_limit']) * 100;
                            }
                        ?>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 <?php echo $icon[0]; ?> rounded-lg flex items-center justify-center">
                                        <i class="<?php echo $icon[2]; ?> <?php echo $icon[1]; ?> text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($promo['code']); ?></h4>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($promo['description'] ?: 'No description'); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?php echo $statusColor; ?>">
                                        <?php echo ucfirst($promo['computed_status']); ?>
                                    </span>
                                    <div class="flex space-x-2">
                                        <button class="text-beige hover:text-beige-dark edit-promo-btn" data-id="<?php echo $promo['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 delete-promo-btn" data-id="<?php echo $promo['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Discount</p>
                                    <p class="font-semibold text-gray-900">
                                        <?php 
                                        if ($promo['type'] === 'percentage') {
                                            echo $promo['value'] . '%';
                                        } elseif ($promo['type'] === 'fixed_amount') {
                                            echo '₱' . number_format($promo['value'], 2);
                                        } else {
                                            echo 'Free Shipping';
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Valid Until</p>
                                    <p class="font-semibold text-gray-900">
                                        <?php echo $promo['expires_at'] ? date('M j, Y', strtotime($promo['expires_at'])) : 'No expiry'; ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Uses</p>
                                    <p class="font-semibold text-gray-900">
                                        <?php echo $promo['actual_usage_count']; ?>
                                        <?php if ($promo['usage_limit']): ?>/ <?php echo $promo['usage_limit']; ?><?php endif; ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Discount Given</p>
                                    <p class="font-semibold text-beige">₱<?php echo number_format($promo['total_discount_given'], 2); ?></p>
                                </div>
                            </div>
                            <?php if ($promo['usage_limit'] && $usagePercent > 0): ?>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-beige h-2 rounded-full" style="width: <?php echo min($usagePercent, 100); ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-600 mt-1"><?php echo number_format($usagePercent, 1); ?>% of usage limit reached</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Performance -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Performance Insights</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">Top Performing Promotions</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-trophy text-green-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">Summer Sale 2024</p>
                                            <p class="text-sm text-gray-600">$1,856 revenue</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 font-semibold">+43%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-medal text-yellow-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">Buy 2 Get 1 Free</p>
                                            <p class="text-sm text-gray-600">$945 revenue</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 font-semibold">+28%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-award text-orange-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">Flash Sale Monday</p>
                                            <p class="text-sm text-gray-600">$687 revenue</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 font-semibold">+19%</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">Promotion Tips</h4>
                            <div class="space-y-3">
                                <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                    <p class="text-sm font-medium text-blue-800">Limited Time Offers</p>
                                    <p class="text-sm text-blue-700">Create urgency with time-limited promotions to boost conversions.</p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg border-l-4 border-green-400">
                                    <p class="text-sm font-medium text-green-800">Minimum Order Value</p>
                                    <p class="text-sm text-green-700">Set minimum order requirements to increase average order value.</p>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg border-l-4 border-purple-400">
                                    <p class="text-sm font-medium text-purple-800">Target Specific Products</p>
                                    <p class="text-sm text-purple-700">Focus promotions on slow-moving inventory to clear stock.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Create Promotion Modal -->
    <div id="promotionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Create New Promotion</h3>
                    <button id="closePromoModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form>
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Promotion Code</label>
                                    <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="e.g., SUMMER2024">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Promotion Type</label>
                                    <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                        <option value="percentage">Percentage Discount</option>
                                        <option value="fixed_amount">Fixed Amount Off</option>
                                        <option value="free_shipping">Free Shipping</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date (Optional)</label>
                                    <input type="datetime-local" name="starts_at" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date (Optional)</label>
                                    <input type="datetime-local" name="expires_at" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label>
                                    <input type="number" name="value" required step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="20">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Usage Limit (Optional)</label>
                                    <input type="number" name="usage_limit" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Order Amount</label>
                                    <input type="number" name="minimum_order_amount" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="0" value="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige" placeholder="Describe your promotion..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" id="cancelPromoBtn" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="btn-beige">Create Promotion</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Promotion modal functionality
        const createPromoBtn = document.getElementById('createPromoBtn');
        const promotionModal = document.getElementById('promotionModal');
        const closePromoModal = document.getElementById('closePromoModal');
        const cancelPromoBtn = document.getElementById('cancelPromoBtn');
        const promotionForm = promotionModal.querySelector('form');

        createPromoBtn.addEventListener('click', () => {
            promotionModal.classList.remove('hidden');
        });

        closePromoModal.addEventListener('click', () => {
            promotionModal.classList.add('hidden');
        });

        cancelPromoBtn.addEventListener('click', () => {
            promotionModal.classList.add('hidden');
        });

        promotionModal.addEventListener('click', (e) => {
            if (e.target === promotionModal) {
                promotionModal.classList.add('hidden');
            }
        });

        // Handle form submission
        promotionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(promotionForm);
            const data = {
                code: formData.get('code'),
                description: formData.get('description'),
                type: formData.get('type'),
                value: parseFloat(formData.get('value')),
                minimum_order_amount: parseFloat(formData.get('minimum_order_amount')) || 0,
                usage_limit: parseInt(formData.get('usage_limit')) || null,
                starts_at: formData.get('starts_at') || null,
                expires_at: formData.get('expires_at') || null,
                is_active: 1
            };
            
            try {
                const response = await fetch('/Core1_ecommerce/seller/api/promotions/', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Promotion created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while creating the promotion.');
            }
        });

        // Delete promotion functionality
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.delete-promo-btn')) {
                const btn = e.target.closest('.delete-promo-btn');
                const promoId = btn.getAttribute('data-id');
                
                if (confirm('Are you sure you want to delete this promotion?')) {
                    try {
                        const response = await fetch(`/Core1_ecommerce/seller/api/promotions/manage.php/${promoId}`, {
                            method: 'DELETE'
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the promotion.');
                    }
                }
            }
        });

        // Filter buttons functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', async () => {
                filterBtns.forEach(b => b.classList.remove('bg-beige', 'text-white'));
                btn.classList.add('bg-beige', 'text-white');
                
                const filterType = btn.textContent.toLowerCase();
                await filterPromotions(filterType);
            });
        });

        // Filter promotions function
        async function filterPromotions(filterType) {
            try {
                let apiFilter = '';
                if (filterType !== 'all') {
                    if (filterType === 'percentage') apiFilter = 'percentage';
                    else if (filterType === 'fixed amount') apiFilter = 'fixed_amount';
                    else if (filterType === 'bogo') return; // Not implemented yet
                }
                
                const url = `/Core1_ecommerce/seller/api/promotions/${apiFilter ? `?type=${apiFilter}` : ''}`;
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success) {
                    updatePromotionsList(result.promotions);
                } else {
                    console.error('Filter error:', result.message);
                }
            } catch (error) {
                console.error('Error filtering promotions:', error);
            }
        }

        // Update promotions list in DOM
        function updatePromotionsList(promotions) {
            const container = document.querySelector('.divide-y.divide-gray-200');
            
            if (promotions.length === 0) {
                container.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-percent text-4xl text-gray-400 mb-4"></i>
                        <p>No promotions found for this filter</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = promotions.map(promo => {
                const iconColors = {
                    'percentage': ['bg-red-100', 'text-red-600', 'fas fa-percent'],
                    'fixed_amount': ['bg-green-100', 'text-green-600', 'fas fa-dollar-sign'],
                    'free_shipping': ['bg-blue-100', 'text-blue-600', 'fas fa-shipping-fast']
                };
                const icon = iconColors[promo.type] || ['bg-gray-100', 'text-gray-600', 'fas fa-tag'];
                
                const statusColors = {
                    'active': 'bg-green-100 text-green-800',
                    'scheduled': 'bg-blue-100 text-blue-800',
                    'expired': 'bg-red-100 text-red-800',
                    'inactive': 'bg-gray-100 text-gray-800'
                };
                const statusColor = statusColors[promo.computed_status] || 'bg-gray-100 text-gray-800';
                
                let discountDisplay = '';
                if (promo.type === 'percentage') {
                    discountDisplay = promo.value + '%';
                } else if (promo.type === 'fixed_amount') {
                    discountDisplay = '₱' + parseFloat(promo.value).toFixed(2);
                } else {
                    discountDisplay = 'Free Shipping';
                }
                
                const usagePercent = promo.usage_limit && promo.actual_usage_count ? 
                    (promo.actual_usage_count / promo.usage_limit) * 100 : 0;
                
                const progressBar = promo.usage_limit && usagePercent > 0 ? `
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-beige h-2 rounded-full" style="width: ${Math.min(usagePercent, 100)}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">${usagePercent.toFixed(1)}% of usage limit reached</p>
                    </div>
                ` : '';
                
                return `
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 ${icon[0]} rounded-lg flex items-center justify-center">
                                    <i class="${icon[2]} ${icon[1]} text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-semibold text-gray-900">${promo.code}</h4>
                                    <p class="text-sm text-gray-600">${promo.description || 'No description'}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full ${statusColor}">
                                    ${promo.computed_status.charAt(0).toUpperCase() + promo.computed_status.slice(1)}
                                </span>
                                <div class="flex space-x-2">
                                    <button class="text-beige hover:text-beige-dark edit-promo-btn" data-id="${promo.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800 delete-promo-btn" data-id="${promo.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Discount</p>
                                <p class="font-semibold text-gray-900">${discountDisplay}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Valid Until</p>
                                <p class="font-semibold text-gray-900">
                                    ${promo.expires_at ? new Date(promo.expires_at).toLocaleDateString() : 'No expiry'}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Uses</p>
                                <p class="font-semibold text-gray-900">
                                    ${promo.actual_usage_count}${promo.usage_limit ? ' / ' + promo.usage_limit : ''}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Discount Given</p>
                                <p class="font-semibold text-beige">₱${parseFloat(promo.total_discount_given).toFixed(2)}</p>
                            </div>
                        </div>
                        ${progressBar}
                    </div>
                `;
            }).join('');
        }
    </script>

<?php endLayout(); ?>