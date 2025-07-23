<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stall;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales Today', 'PHP ' . number_format(
                Order::whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->sum('total_amount'), 2
            ))
                ->description('From completed orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('success'),

            Stat::make('Active Stalls', Stall::where('is_active', true)->count())
                ->description('Out of ' . Stall::count() . ' total stalls')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),

            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}