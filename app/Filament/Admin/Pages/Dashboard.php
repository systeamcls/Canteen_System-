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

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $title = 'Dashboard Overview';

    public function getWidgets(): array
    {
        return [

            AdminQuickStatsWidget::class,
            AdminSalesChartWidget::class,
            AdminPerformanceWidget::class,
            AdminLatestOrdersWidget::class,
            AdminTrendingItemsWidget::class,
        ];

    }

    protected function getHeaderWidgets(): array
    {
        return [
           
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1; // Single column layout - let widgets control their own grids
    }
}