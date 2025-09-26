<?php
// app/Filament/Widgets/AnalyticsKPIWidget.php
namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsKPIWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $analytics = new AnalyticsService();
        $kpis = $analytics->getTodayKPIs();

        return [
            Stat::make('Today\'s Sales', '₱' . number_format($kpis['sales']['amount'], 2))
                ->description(
                    abs($kpis['sales']['change']) > 0 
                        ? abs(round($kpis['sales']['change'], 1)) . '% ' . ($kpis['sales']['change'] >= 0 ? 'up' : 'down') . ' from yesterday'
                        : 'No change from yesterday'
                )
                ->descriptionIcon($kpis['sales']['change'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($kpis['sales']['change'] >= 0 ? 'success' : 'danger')
                ->chart([7, 12, 8, 15, 10, 18, $kpis['sales']['amount'] / 1000]), // Simple trend line

            Stat::make('Net Revenue Today', '₱' . number_format($kpis['net_revenue'], 2))
                ->description('Sales + Rentals - Expenses - Payroll')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($kpis['net_revenue'] >= 0 ? 'success' : 'danger'),

            Stat::make('Orders Today', $kpis['sales']['orders_count'])
                ->description('Completed orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Stalls Active', $kpis['active_stalls'])
                ->description('Currently operating')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning'),

            Stat::make('Today\'s Expenses', '₱' . number_format($kpis['expenses']['amount'], 2))
                ->description('Operational costs')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Rentals Collected', '₱' . number_format($kpis['rentals']['amount'], 2))
                ->description('Today\'s rental payments')
                ->descriptionIcon('heroicon-m-home')
                ->color('success'),
        ];
    }
}
