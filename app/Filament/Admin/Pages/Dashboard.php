<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\FinancialOverviewWidget;
use App\Filament\Admin\Widgets\AdminQuickStatsWidget;
use App\Filament\Admin\Widgets\AdminSalesChartWidget;
use App\Filament\Admin\Widgets\AdminPerformanceWidget;
use App\Filament\Admin\Widgets\AdminOrderStatusWidget;
use App\Filament\Admin\Widgets\AdminTopSellerWidget;
use App\Filament\Admin\Widgets\AdminPopularHoursWidget;
use App\Filament\Admin\Widgets\AdminLatestOrdersWidget;
use App\Filament\Admin\Widgets\AdminTrendingItemsWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $title = 'Dashboard Overview';

    public function getWidgets(): array
    {
        return []; // Disable global widgets
    }

    protected function getHeaderWidgets(): array
    {
        return [

            FinancialOverviewWidget::class,
            // Top row - Quick stats overview (full width)
            AdminQuickStatsWidget::class,
            
            // Second row - Main charts (50% each)
            AdminSalesChartWidget::class,
            AdminPerformanceWidget::class,
            
            // Third row - Status and metrics (33% each)
            AdminOrderStatusWidget::class,
            AdminTopSellerWidget::class,
            AdminPopularHoursWidget::class,
            
            // Fourth row - Data tables (full width)
            AdminLatestOrdersWidget::class,
            
            // Fifth row - Trending items (full width)
            AdminTrendingItemsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 12; // Use 12-column grid for better flexibility
    }
}