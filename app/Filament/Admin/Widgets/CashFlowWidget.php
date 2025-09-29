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
            Stat::make('Cash In', '₱' . number_format($cashFlow['cash_in'], 2))
                ->description('Revenue this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Cash Out', '₱' . number_format($cashFlow['cash_out'], 2))
                ->description('Expenses this month')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Cash Flow', '₱' . number_format($cashFlow['net_cash_flow'], 2))
                ->description($cashFlow['is_positive'] ? 'Profitable' : 'Loss')
                ->descriptionIcon($cashFlow['is_positive'] ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($cashFlow['is_positive'] ? 'success' : 'danger'),

            Stat::make('Best Day', !empty($bestDay['sales']) ? '₱' . number_format($bestDay['sales'], 2) : 'No data')
                ->description(!empty($bestDay['date']) ? $bestDay['date'] : 'Last 30 days')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
}