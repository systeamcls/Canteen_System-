<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StallStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $stall = auth()->user()->stall;

        $todaySales = Order::whereHas('items.product', function ($query) use ($stall) {
            $query->where('stall_id', $stall->id);
        })
        ->whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_amount');

        $averageRating = Review::whereHas('product', function ($query) use ($stall) {
            $query->where('stall_id', $stall->id);
        })->avg('rating');

        $pendingOrders = Order::whereHas('items.product', function ($query) use ($stall) {
            $query->where('stall_id', $stall->id);
        })
        ->where('status', 'pending')
        ->count();

        return [
            Stat::make('Today\'s Sales', '₱' . number_format($todaySales, 2))
                ->description('From completed orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('success'),

            Stat::make('Average Rating', number_format($averageRating, 1) . ' / 5.0')
                ->description('Based on customer reviews')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Pending Orders', $pendingOrders)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}