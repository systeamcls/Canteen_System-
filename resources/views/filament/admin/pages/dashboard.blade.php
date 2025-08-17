<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Top Row: 4 KPI Stats -->
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminRevenueWidget::class)
        </div>
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminOrdersWidget::class)
        </div>
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminMonthlyRevenueWidget::class)
        </div>
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminTopSellerWidget::class)
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Middle Row: 2 columns -->
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminSalesChartWidget::class)
        </div>
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminTrendingItemsWidget::class)
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Bottom Row: 2 columns -->
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminLatestOrdersWidget::class)
        </div>
        <div class="lg:col-span-1">
            @livewire(\App\Filament\Admin\Widgets\AdminRecentReviewsWidget::class)
        </div>
    </div>
</x-filament-panels::page>