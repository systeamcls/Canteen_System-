<x-filament-panels::page>
    {{-- Sales Analytics (Full Width) --}}
    <div class="grid grid-cols-1 gap-6">
        @livewire(\App\Filament\Admin\Widgets\SalesAnalyticsWidget::class)
    </div>

    {{-- Rental Analytics (Full Width) --}}
    <div class="grid grid-cols-1 gap-6 mt-6">
        @livewire(\App\Filament\Admin\Widgets\RentalAnalyticsWidget::class)
    </div>

    {{-- Inventory Analytics (Full Width) --}}
    <div class="grid grid-cols-1 gap-6 mt-6">
        @livewire(\App\Filament\Admin\Widgets\InventoryAnalyticsWidget::class)
    </div>

    {{-- Top Row: 4 KPI Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        @livewire(\App\Filament\Admin\Widgets\AdminRevenueWidget::class)
        @livewire(\App\Filament\Admin\Widgets\AdminOrdersWidget::class)
        @livewire(\App\Filament\Admin\Widgets\AdminMonthlyRevenueWidget::class)
        @livewire(\App\Filament\Admin\Widgets\AdminTopSellerWidget::class)
    </div>

    {{-- Middle Row: Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        @livewire(\App\Filament\Admin\Widgets\AdminSalesChartWidget::class)
        @livewire(\App\Filament\Admin\Widgets\AdminTrendingItemsWidget::class)
    </div>

    {{-- Bottom Row: Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        @livewire(\App\Filament\Admin\Widgets\AdminLatestOrdersWidget::class)
        @livewire(\App\Filament\Admin\Widgets\AdminRecentReviewsWidget::class)
    </div>
</x-filament-panels::page>
