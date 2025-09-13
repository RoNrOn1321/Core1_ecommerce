<?php
$page_title = "Analytics";
?>
<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-20 min-h-screen">
        <div class="p-6">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Analytics</h1>
                    <p class="text-gray-600">Track your store performance and insights</p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-2">
                    <select id="periodSelector" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-beige">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 3 months</option>
                        <option value="365">This year</option>
                    </select>
                    <button class="btn-beige" onclick="exportReport()">
                        <i class="fas fa-download mr-2"></i>Export Report
                    </button>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="keyMetrics">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalRevenue">Loading...</p>
                            <p class="text-sm text-gray-500 flex items-center mt-1" id="revenueChange">
                                <i class="fas fa-chart-line mr-1"></i>
                                Current period
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-beige rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Orders</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalOrders">Loading...</p>
                            <p class="text-sm text-gray-500 flex items-center mt-1" id="ordersChange">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                Current period
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                            <p class="text-2xl font-bold text-gray-900" id="avgOrderValue">Loading...</p>
                            <p class="text-sm text-gray-500 flex items-center mt-1" id="aovChange">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Current period
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Customers</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalCustomers">Loading...</p>
                            <p class="text-sm text-gray-500 flex items-center mt-1" id="customersChange">
                                <i class="fas fa-users mr-1"></i>
                                Current period
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Revenue Trend</h3>
                            <div class="flex space-x-2">
                                <button class="text-sm px-3 py-1 bg-beige text-white rounded-full">Revenue</button>
                                <button class="text-sm px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-full">Orders</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="revenueChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Top Selling Products</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4" id="topProducts">
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p>Loading top products...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Category Performance -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Sales by Category</h3>
                    </div>
                    <div class="p-6">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Customer Insights -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Customer Insights</h3>
                    </div>
                    <div class="p-6" id="customerInsights">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading customer insights...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Table -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Daily Performance</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Sold</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Order Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="performanceTable">
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                    <br>Loading performance data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Page-specific scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let revenueChart = null;
        let categoryChart = null;
        let currentPeriod = '30';

        // Fetch analytics data from API
        async function loadAnalytics(period = '30') {
            try {
                const response = await fetch(`api/analytics/?period=${period}`, {
                    method: 'GET',
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch analytics data');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    updateDashboard(result.data);
                } else {
                    console.error('Analytics API error:', result.message);
                    showError('Failed to load analytics data');
                }
            } catch (error) {
                console.error('Analytics fetch error:', error);
                showError('Failed to load analytics data');
            }
        }

        // Update dashboard with fetched data
        function updateDashboard(analytics) {
            updateKeyMetrics(analytics);
            updateTopProducts(analytics.product_performance || []);
            updateCustomerInsights(analytics.customer_insights);
            updatePerformanceTable(analytics.sales_over_time || []);
            updateCharts(analytics);
        }

        // Update key metrics cards
        function updateKeyMetrics(analytics) {
            const salesData = analytics.sales_over_time || [];
            const customerData = analytics.customer_insights || {};
            
            let totalRevenue = 0;
            let totalOrders = 0;
            let totalItems = 0;
            
            salesData.forEach(day => {
                totalRevenue += parseFloat(day.revenue || 0);
                totalOrders += parseInt(day.orders || 0);
                totalItems += parseInt(day.items_sold || 0);
            });
            
            const avgOrderValue = totalOrders > 0 ? (totalRevenue / totalOrders) : 0;
            const uniqueCustomers = customerData.unique_customers || 0;
            
            document.getElementById('totalRevenue').textContent = formatCurrency(totalRevenue);
            document.getElementById('totalOrders').textContent = totalOrders.toLocaleString();
            document.getElementById('avgOrderValue').textContent = formatCurrency(avgOrderValue);
            document.getElementById('totalCustomers').textContent = uniqueCustomers.toLocaleString();
        }

        // Update top products section
        function updateTopProducts(products) {
            const container = document.getElementById('topProducts');
            
            if (!products || products.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-2xl mb-2"></i>
                        <p>No product sales data available</p>
                    </div>
                `;
                return;
            }
            
            // Get max revenue for progress bar calculation
            const maxRevenue = Math.max(...products.map(p => parseFloat(p.revenue || 0)));
            
            container.innerHTML = products.slice(0, 5).map(product => {
                const revenue = parseFloat(product.revenue || 0);
                const percentage = maxRevenue > 0 ? (revenue / maxRevenue) * 100 : 0;
                
                return `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-beige rounded flex items-center justify-center">
                                <i class="fas fa-box text-white"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900">${escapeHtml(product.name || 'Unknown Product')}</p>
                                <p class="text-sm text-gray-600">${product.total_sold || 0} sold</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-beige">${formatCurrency(revenue)}</p>
                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-beige h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Update customer insights
        function updateCustomerInsights(insights) {
            const container = document.getElementById('customerInsights');
            
            if (!insights) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users text-2xl mb-2"></i>
                        <p>No customer data available</p>
                    </div>
                `;
                return;
            }
            
            const avgOrderValue = parseFloat(insights.avg_order_value || 0);
            const avgOrdersPerCustomer = parseFloat(insights.avg_orders_per_customer || 0);
            const uniqueCustomers = parseInt(insights.unique_customers || 0);
            
            container.innerHTML = `
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-beige rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-white">${uniqueCustomers}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Unique Customers</p>
                        <p class="text-xs text-gray-600 mt-1">In selected period</p>
                    </div>
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Average Order Value</span>
                            <span class="font-semibold text-beige">${formatCurrency(avgOrderValue)}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Avg. Orders per Customer</span>
                            <span class="font-semibold text-gray-900">${avgOrdersPerCustomer.toFixed(1)}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        // Update performance table
        function updatePerformanceTable(salesData) {
            const tbody = document.getElementById('performanceTable');
            
            if (!salesData || salesData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-chart-line text-2xl mb-2"></i>
                            <br>No sales data available for the selected period
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = salesData.map(day => {
                const revenue = parseFloat(day.revenue || 0);
                const orders = parseInt(day.orders || 0);
                const itemsSold = parseInt(day.items_sold || 0);
                const avgOrderValue = orders > 0 ? (revenue / orders) : 0;
                const date = new Date(day.date).toLocaleDateString();
                
                return `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${date}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatCurrency(revenue)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${orders}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${itemsSold}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatCurrency(avgOrderValue)}</td>
                    </tr>
                `;
            }).join('');
        }

        // Update charts
        function updateCharts(analytics) {
            updateRevenueChart(analytics.sales_over_time || []);
            updateCategoryChart(analytics.product_performance || []);
        }

        // Update revenue chart
        function updateRevenueChart(salesData) {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            if (revenueChart) {
                revenueChart.destroy();
            }
            
            const labels = salesData.map(day => new Date(day.date).toLocaleDateString());
            const revenues = salesData.map(day => parseFloat(day.revenue || 0));
            
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue',
                        data: revenues,
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
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update category chart (using top products as categories)
        function updateCategoryChart(products) {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            if (categoryChart) {
                categoryChart.destroy();
            }
            
            const topProducts = products.slice(0, 5);
            const labels = topProducts.map(p => p.name || 'Unknown');
            const data = topProducts.map(p => parseFloat(p.revenue || 0));
            const colors = ['#b48d6b', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'];
            
            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, data.length)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Utility functions
        function formatCurrency(amount) {
            return '₱' + parseFloat(amount || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showError(message) {
            // Simple error display - you could make this more sophisticated
            alert(message);
        }

        function exportReport() {
            const period = document.getElementById('periodSelector').value;
            window.open(`api/analytics/export.php?period=${period}`, '_blank');
        }

        // Event listeners
        document.getElementById('periodSelector').addEventListener('change', function() {
            currentPeriod = this.value;
            loadAnalytics(currentPeriod);
        });

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadAnalytics(currentPeriod);
        });
    </script>

<?php include 'includes/footer.php'; ?>