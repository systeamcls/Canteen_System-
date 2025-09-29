<?php

namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AnalyticsKPIWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $analytics = new AnalyticsService();
        
        // Today's data
        $today = $analytics->getAdminStallSales(now()->startOfDay(), now());
        $todayRentals = $analytics->getRentalIncome(now()->startOfDay(), now());
        
        // This month data
        $thisMonth = $analytics->getFinancialSummary(now()->startOfMonth(), now());
        $lastMonth = $analytics->getFinancialSummary(
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        );

        // Calculate monthly growth
        $revenueGrowth = $lastMonth['revenue']['total'] > 0
            ? round((($thisMonth['revenue']['total'] - $lastMonth['revenue']['total']) / $lastMonth['revenue']['total']) * 100, 1)
            : 0;

        // Rental status
        $rentalStatus = $analytics->getRentalPaymentStatus();

        return [
            Stat::make('Your Stall Sales (Today)', '₱' . number_format($today['total_sales'], 2))
                ->description($today['total_orders'] . ' orders completed')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->chart($this->getWeekTrend()),

            Stat::make('Rental Income (This Month)', '₱' . number_format($thisMonth['revenue']['rental_income'], 2))
                ->description($rentalStatus['compliance_rate'] . '% compliance rate')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info')
                ->extraAttributes([
                    'title' => $rentalStatus['overdue'] . ' overdue payments'
                ]),

            Stat::make('Total Revenue (This Month)', '₱' . number_format($thisMonth['revenue']['total'], 2))
                ->description(($revenueGrowth >= 0 ? '+' : '') . $revenueGrowth . '% vs last month')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthTrend()),

            Stat::make('Net Profit (This Month)', '₱' . number_format($thisMonth['net_profit'], 2))
                ->description($thisMonth['profit_margin'] . '% profit margin')
                ->descriptionIcon($thisMonth['is_profitable'] ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($thisMonth['is_profitable'] ? 'success' : 'danger'),
        ];
    }

    private function getWeekTrend(): array
    {
        $analytics = new AnalyticsService();
        return collect($analytics->getStallSalesTrend(7))
            ->pluck('sales')
            ->toArray();
    }

    private function getMonthTrend(): array
    {
        $analytics = new AnalyticsService();
        return collect($analytics->getStallSalesTrend(30))
            ->pluck('sales')
            ->toArray();
    }
}