<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\AdminQuickStatsWidget;
use App\Filament\Admin\Widgets\AdminSalesChartWidget;
use App\Filament\Admin\Widgets\AdminPerformanceWidget;
use App\Filament\Admin\Widgets\AdminOrderStatusWidget;
use App\Filament\Admin\Widgets\AdminTopSellerWidget;
use App\Filament\Admin\Widgets\AdminPopularHoursWidget;
use App\Filament\Admin\Widgets\AdminLatestOrdersWidget;
use App\Filament\Admin\Widgets\AdminTrendingItemsWidget;
use App\Filament\Admin\Widgets\AdminRecentReviewsWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $title = 'Dashboard Overview';

    protected function getHeaderWidgets(): array
    {
        return [
            // Top row - Quick stats overview
            AdminQuickStatsWidget::class,
            
            // Second row - Main charts
            AdminSalesChartWidget::class,
            AdminPerformanceWidget::class,
            
            // Third row - Status and metrics
            AdminOrderStatusWidget::class,
            AdminTopSellerWidget::class,
            AdminPopularHoursWidget::class,
            
            // Fourth row - Data tables
            AdminLatestOrdersWidget::class,
            AdminTrendingItemsWidget::class,
            
            // Fifth row - Reviews
            AdminRecentReviewsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 3,
            '2xl' => 4,
        ];
    }
}