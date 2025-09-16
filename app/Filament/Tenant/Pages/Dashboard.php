<?php

namespace App\Filament\Tenant\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Tenant\Widgets\TenantStatsOverviewWidget;
use App\Filament\Tenant\Widgets\TenantSalesChartWidget;
use App\Filament\Tenant\Widgets\TenantRecentOrdersWidget;
use App\Filament\Tenant\Widgets\TenantTopProductsWidget;
use App\Filament\Tenant\Widgets\TenantRentalStatusWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = null; // Remove group
    protected static ?string $title = 'Dashboard';
    protected static ?int $navigationSort = 1;

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
            '2xl' => 4,
        ];
    }

    public function getWidgets(): array
    {
        return [
            TenantStatsOverviewWidget::class,
            TenantSalesChartWidget::class,
            TenantRentalStatusWidget::class,
            TenantRecentOrdersWidget::class,
            TenantTopProductsWidget::class,
        ];
    }
}