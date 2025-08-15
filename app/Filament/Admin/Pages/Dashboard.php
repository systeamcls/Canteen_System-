<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\AdminRevenueWidget;
use App\Filament\Admin\Widgets\AdminOrdersWidget;
use App\Filament\Admin\Widgets\AdminMonthlyRevenueWidget;
use App\Filament\Admin\Widgets\AdminTopSellerWidget;
use App\Filament\Admin\Widgets\AdminSalesChartWidget;
use App\Filament\Admin\Widgets\AdminTrendingItemsWidget;
use App\Filament\Admin\Widgets\AdminLatestOrdersWidget;
use App\Filament\Admin\Widgets\AdminRecentReviewsWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            // Top Row (4 KPI Stats)
            AdminRevenueWidget::class,
            AdminOrdersWidget::class,
            AdminMonthlyRevenueWidget::class,
            AdminTopSellerWidget::class,
            
            // Middle Row (2 columns)
            AdminSalesChartWidget::class,
            AdminTrendingItemsWidget::class,
            
            // Bottom Row (2 columns) 
            AdminLatestOrdersWidget::class,
            AdminRecentReviewsWidget::class,
        ];
    }

    protected function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }
}
