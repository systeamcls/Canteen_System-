<?php
// resources/views/filament/admin/pages/analytics-dashboard.blade.php
?>
<x-filament-panels::page>
    {{-- Quick Summary Cards Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <div class="lg:col-span-4">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">{{ now()->format('l, F j, Y') }}</h2>
                        <p class="text-blue-100">Real-time business insights at your fingertips</p>
                    </div>
                    <div class="hidden md:block">
                        <x-heroicon-o-chart-bar class="h-16 w-16 text-white opacity-80" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Widgets --}}
    <div class="mb-8">
        @foreach ($this->getHeaderWidgets() as $widget)
            @livewire($widget, ['lazy' => true])
        @endforeach
    </div>

    {{-- Quick Actions Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-800">
                    <x-heroicon-o-banknotes class="h-6 w-6 text-green-600 dark:text-green-300" />
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Today's Performance</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Track real-time metrics</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-800">
                    <x-heroicon-o-chart-bar-square class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Weekly Trends</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Monitor growth patterns</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-800">
                    <x-heroicon-o-document-chart-bar class="h-6 w-6 text-purple-600 dark:text-purple-300" />
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Monthly Reports</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Export detailed insights</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts and Analytics Widgets --}}
    <div class="space-y-8">
        @foreach ($this->getWidgets() as $widget)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                @livewire($widget, ['lazy' => true])
            </div>
        @endforeach
    </div>

    {{-- Additional Insights Section --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Key Highlights --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-light-bulb class="h-5 w-5 mr-2 text-yellow-500" />
                Key Insights
            </h3>
            
            @php
                $insights = $this->getQuickInsights();
                $bestDay = $insights['best_day'] ?? [];
                $cashFlow = $insights['cash_flow'] ?? [];
            @endphp
            
            <div class="space-y-4">
                @if(!empty($bestDay))
                <div class="flex items-start p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <x-heroicon-o-trophy class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-green-900 dark:text-green-100">Best Sales Day</p>
                        <p class="text-xs text-green-700 dark:text-green-300">
                            {{ $bestDay['date'] ?? 'N/A' }} - ₱{{ number_format($bestDay['sales'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
                @endif

                @if(!empty($cashFlow) && $cashFlow['net_cash_flow'] > 0)
                <div class="flex items-start p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <x-heroicon-o-arrow-trending-up class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Positive Cash Flow</p>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            ₱{{ number_format($cashFlow['net_cash_flow'], 2) }} net profit this month
                        </p>
                    </div>
                </div>
                @elseif(!empty($cashFlow) && $cashFlow['net_cash_flow'] < 0)
                <div class="flex items-start p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-600 dark:text-red-400 mt-0.5 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-red-900 dark:text-red-100">Cash Flow Alert</p>
                        <p class="text-xs text-red-700 dark:text-red-300">
                            ₱{{ number_format(abs($cashFlow['net_cash_flow']), 2) }} deficit this month
                        </p>
                    </div>
                </div>
                @endif

                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-900/20 rounded-lg">
                    <x-heroicon-o-clock class="h-5 w-5 text-gray-600 dark:text-gray-400 mt-0.5 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Real-time Updates</p>
                        <p class="text-xs text-gray-700 dark:text-gray-300">
                            Data refreshes automatically every 30 seconds
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Tips --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-academic-cap class="h-5 w-5 mr-2 text-blue-500" />
                Business Tips
            </h3>
            
            <div class="space-y-3">
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <p class="text-sm font-medium text-yellow-900 dark:text-yellow-100">💡 Peak Hours</p>
                    <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                        Monitor your busiest times to optimize staffing
                    </p>
                </div>
                
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-sm font-medium text-green-900 dark:text-green-100">📈 Best Sellers</p>
                    <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                        Stock up on your top-performing items
                    </p>
                </div>
                
                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <p class="text-sm font-medium text-purple-900 dark:text-purple-100">💳 Payment Methods</p>
                    <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">
                        Digital payments typically increase average order value
                    </p>
                </div>
                
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">🏠 Rental Income</p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                        Steady rental income provides financial stability
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Information --}}
    <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
        <p>Analytics Dashboard • Last updated: {{ now()->format('M j, Y g:i A') }}</p>
        <p class="mt-1">Data includes completed orders, confirmed payments, and processed transactions only</p>
    </div>

    {{-- Auto-refresh script --}}
    <script>
        // Auto refresh widgets every 30 seconds
        setInterval(function() {
            Livewire.dispatch('refreshWidgets');
        }, 30000);
    </script>
</x-filament-panels::page>