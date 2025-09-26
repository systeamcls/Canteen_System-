<?php

namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashFlowWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $analytics = new AnalyticsService();
        $cashFlow = $analytics->getCashFlowSummary();
        $bestDay = $analytics->getBestPerformingDay();

        return [
            Stat::make('Monthly Income', '₱' . number_format($cashFlow['income']['total'], 2))
                ->description('Sales + Rentals')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Monthly Expenses', '₱' . number_format($cashFlow['expenses']['total'], 2))
                ->description('Operations + Payroll')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Cash Flow', '₱' . number_format($cashFlow['net_cash_flow'], 2))
                ->description('This month')
                ->descriptionIcon($cashFlow['net_cash_flow'] >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($cashFlow['net_cash_flow'] >= 0 ? 'success' : 'danger'),

            Stat::make('Best Day', !empty($bestDay) ? '₱' . number_format($bestDay['sales'], 2) : 'No data')
                ->description(!empty($bestDay) ? $bestDay['date'] . ' (' . $bestDay['orders'] . ' orders)' : 'Last 30 days')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
}