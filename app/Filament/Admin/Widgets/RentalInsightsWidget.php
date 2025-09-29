<?php

namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RentalInsightsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $analytics = new AnalyticsService();
        
        // This month rental data
        $rental = $analytics->getRentalIncome(now()->startOfMonth(), now());
        $status = $analytics->getRentalPaymentStatus();

        return [
            Stat::make('Collected This Month', '₱' . number_format($rental['collected'], 2))
                ->description('From tenant rental payments')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pending Payments', '₱' . number_format($rental['pending'], 2))
                ->description($status['pending'] . ' tenants haven\'t paid yet')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Overdue Payments', '₱' . number_format($rental['overdue_amount'], 2))
                ->description($rental['overdue_count'] . ' late payments')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Compliance Rate', $status['compliance_rate'] . '%')
                ->description('Tenants paid on time')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($status['compliance_rate'] >= 80 ? 'success' : 'warning'),
        ];
    }
}