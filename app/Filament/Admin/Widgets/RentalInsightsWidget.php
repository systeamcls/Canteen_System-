<?php

namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RentalInsightsWidget extends BaseWidget
{
    protected static ?int $sort = 5;    

    protected function getStats(): array
    {
        $analytics = new AnalyticsService();
        $insights = $analytics->getRentalInsights();

        return [
            Stat::make('Rentals Today', '₱' . number_format($insights['today_collected'], 2))
                ->description('Collected today')
                ->descriptionIcon('heroicon-m-home')
                ->color('success'),

            Stat::make('Monthly Rentals', '₱' . number_format($insights['monthly_collected'], 2))
                ->description('This month total')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Payment Compliance', $insights['compliance_rate'] . '%')
                ->description('This month')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($insights['compliance_rate'] >= 80 ? 'success' : ($insights['compliance_rate'] >= 60 ? 'warning' : 'danger')),

            Stat::make('Overdue Payments', $insights['overdue_count'])
                ->description('Requires attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($insights['overdue_count'] > 0 ? 'danger' : 'success'),
        ];
    }
}
