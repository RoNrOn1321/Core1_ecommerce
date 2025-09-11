<?php
$page_title = "Finances";
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Financial Overview</h1>
                    <p class="text-gray-600">Track your earnings, expenses, and financial performance</p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                        <option>Last 30 days</option>
                        <option>Last 7 days</option>
                        <option>Last 3 months</option>
                        <option>This year</option>
                    </select>
                    <button class="btn-beige">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </button>
                </div>
            </div>

            <!-- Financial Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">$12,426</p>
                            <p class="text-sm text-green-600 flex items-center mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +12.5% from last month
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Net Profit</p>
                            <p class="text-2xl font-bold text-gray-900">$8,642</p>
                            <p class="text-sm text-green-600 flex items-center mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +8.2% from last month
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-beige rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Expenses</p>
                            <p class="text-2xl font-bold text-gray-900">$3,784</p>
                            <p class="text-sm text-red-600 flex items-center mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +5.1% from last month
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-credit-card text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Payouts</p>
                            <p class="text-2xl font-bold text-gray-900">$2,156</p>
                            <p class="text-sm text-blue-600 flex items-center mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                Next payout in 3 days
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Revenue vs Expenses</h3>
                    </div>
                    <div class="p-6">
                        <canvas id="revenueExpenseChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Profit Margin Chart -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Profit Margin Trend</h3>
                    </div>
                    <div class="p-6">
                        <canvas id="profitChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Financial Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
                            <a href="#" class="text-beige hover:text-beige-dark text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Order Payment</p>
                                        <p class="text-sm text-gray-600">Order #12345</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">+$89.99</p>
                                    <p class="text-sm text-gray-600">Jan 15, 2024</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-minus text-red-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Shipping Fee</p>
                                        <p class="text-sm text-gray-600">Order #12345</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-red-600">-$5.99</p>
                                    <p class="text-sm text-gray-600">Jan 15, 2024</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-plus text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Order Payment</p>
                                        <p class="text-sm text-gray-600">Order #12344</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">+$45.50</p>
                                    <p class="text-sm text-gray-600">Jan 14, 2024</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-minus text-red-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Platform Fee</p>
                                        <p class="text-sm text-gray-600">Commission 3%</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-red-600">-$2.70</p>
                                    <p class="text-sm text-gray-600">Jan 14, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expense Breakdown -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Expense Breakdown</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                    <span class="text-gray-700">Platform Fees</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-gray-900">$1,245</span>
                                    <span class="text-sm text-gray-600 ml-2">32.9%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    <span class="text-gray-700">Shipping Costs</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-gray-900">$892</span>
                                    <span class="text-sm text-gray-600 ml-2">23.6%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                    <span class="text-gray-700">Marketing</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-gray-900">$567</span>
                                    <span class="text-sm text-gray-600 ml-2">15.0%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-gray-700">Product Costs</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-gray-900">$756</span>
                                    <span class="text-sm text-gray-600 ml-2">20.0%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                    <span class="text-gray-700">Other</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-gray-900">$324</span>
                                    <span class="text-sm text-gray-600 ml-2">8.5%</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <canvas id="expenseChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout Information -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Payout Schedule</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-6 bg-blue-50 rounded-lg">
                            <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar text-white text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Next Payout</h4>
                            <p class="text-2xl font-bold text-blue-600 mb-1">$2,156</p>
                            <p class="text-sm text-gray-600">January 18, 2024</p>
                        </div>
                        <div class="text-center p-6 bg-green-50 rounded-lg">
                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-university text-white text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Bank Account</h4>
                            <p class="text-lg font-bold text-gray-900 mb-1">****1234</p>
                            <p class="text-sm text-gray-600">Chase Bank</p>
                        </div>
                        <div class="text-center p-6 bg-purple-50 rounded-lg">
                            <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-history text-white text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Last Payout</h4>
                            <p class="text-lg font-bold text-gray-900 mb-1">$1,894</p>
                            <p class="text-sm text-gray-600">January 11, 2024</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue vs Expenses Chart
        const revenueExpenseCtx = document.getElementById('revenueExpenseChart').getContext('2d');
        new Chart(revenueExpenseCtx, {
            type: 'line',
            data: {
                labels: ['Jan 1', 'Jan 5', 'Jan 10', 'Jan 15', 'Jan 20', 'Jan 25', 'Jan 30'],
                datasets: [{
                    label: 'Revenue',
                    data: [1200, 1900, 1500, 2100, 1800, 2400, 2200],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Expenses',
                    data: [400, 500, 450, 600, 520, 680, 630],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });

        // Profit Margin Chart
        const profitCtx = document.getElementById('profitChart').getContext('2d');
        new Chart(profitCtx, {
            type: 'line',
            data: {
                labels: ['Jan 1', 'Jan 5', 'Jan 10', 'Jan 15', 'Jan 20', 'Jan 25', 'Jan 30'],
                datasets: [{
                    label: 'Profit Margin %',
                    data: [66.7, 73.7, 70.0, 71.4, 71.1, 71.7, 71.4],
                    borderColor: '#b48d6b',
                    backgroundColor: 'rgba(180, 141, 107, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Expense Breakdown Chart
        const expenseCtx = document.getElementById('expenseChart').getContext('2d');
        new Chart(expenseCtx, {
            type: 'doughnut',
            data: {
                labels: ['Platform Fees', 'Shipping', 'Marketing', 'Product Costs', 'Other'],
                datasets: [{
                    data: [32.9, 23.6, 15.0, 20.0, 8.5],
                    backgroundColor: [
                        '#ef4444',
                        '#3b82f6',
                        '#f59e0b',
                        '#10b981',
                        '#8b5cf6'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>