<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use App\Filament\Admin\Widgets\LatestOrdersWidget;
use App\Filament\Admin\Widgets\SalesChartWidget;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            LatestOrdersWidget::class,
            SalesChartWidget::class,
        ];
    }
}
