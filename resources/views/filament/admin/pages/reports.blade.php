<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Date Range Form -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        @if(isset($data['revenue']))
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Revenue Card -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Total Revenue</h3>
                        <p class="text-3xl font-bold">₱{{ number_format($data['revenue']['total'], 2) }}</p>
                        <p class="text-green-100">{{ $data['revenue']['order_count'] }} orders</p>
                    </div>
                    <div class="text-green-100">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Expenses Card -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Total Expenses</h3>
                        <p class="text-3xl font-bold">₱{{ number_format($data['expenses']['total'], 2) }}</p>
                        <p class="text-red-100">{{ $data['expenses']['count'] }} transactions</p>
                    </div>
                    <div class="text-red-100">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Profit Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Net Profit</h3>
                        <p class="text-3xl font-bold">₱{{ number_format($data['profit'], 2) }}</p>
                        <p class="text-blue-100">
                            Avg Order: ₱{{ number_format($data['revenue']['average_order_value'], 2) }}
                        </p>
                    </div>
                    <div class="text-blue-100">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 001.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Daily Trends Chart -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Daily Revenue vs Expenses</h3>
                <canvas id="dailyTrendsChart" width="400" height="200"></canvas>
            </div>

            <!-- Top Products -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Top Selling Products</h3>
                <div class="space-y-3">
                    @foreach($data['top_products'] as $index => $product)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $product['product_name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $product['quantity_sold'] }} sold</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-white">₱{{ number_format($product['revenue'], 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        @if(isset($data['expenses']['by_category']) && $data['expenses']['by_category']->count() > 0)
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Expense Breakdown by Category</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($data['expenses']['by_category'] as $category => $details)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $category) }}</h4>
                    <p class="text-2xl font-bold text-primary-600">₱{{ number_format($details['total'], 2) }}</p>
                    <p class="text-sm text-gray-500">{{ $details['count'] }} transactions</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @endif
    </div>

    @if(isset($data['daily_trends']))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('dailyTrendsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($data['daily_trends']['dates']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($data['daily_trends']['revenues']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Expenses',
                        data: @json($data['daily_trends']['expenses']),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</x-filament-panels::page>