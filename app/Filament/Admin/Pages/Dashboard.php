<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use App\Filament\Admin\Widgets\LatestOrdersWidget;
use App\Filament\Admin\Widgets\SalesChartWidget;
use App\Filament\Admin\Widgets\TrendingItemsWidget;
use App\Filament\Admin\Widgets\RecentReviewsWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Admin Dashboard';
    protected static ?string $navigationLabel = 'Overview';
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            SalesChartWidget::class,
            LatestOrdersWidget::class,
            TrendingItemsWidget::class,
            RecentReviewsWidget::class,
        ];
    }

    protected function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
            'xl' => 4,
            '2xl' => 4,
        ];
    }
}
