<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                Stat::make('Daily Revenue', 'PHP 0.00')
                    ->description('No stall assigned')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Get today's revenue
        $todayRevenue = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_amount');

        // Get yesterday's revenue for comparison
        $yesterdayRevenue = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereDate('created_at', today()->subDay())
        ->where('status', 'completed')
        ->sum('total_amount');

        // Calculate percentage change
        $percentageChange = 0;
        if ($yesterdayRevenue > 0) {
            $percentageChange = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        }

        $description = $percentageChange >= 0 
            ? sprintf('↗ %.1f%% from yesterday', $percentageChange)
            : sprintf('↘ %.1f%% from yesterday', abs($percentageChange));

        $color = $percentageChange >= 0 ? 'success' : 'danger';
        $icon = $percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        return [
            Stat::make('Daily Revenue', 'PHP ' . number_format($todayRevenue, 2))
                ->description($description)
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($this->getRevenueChart($stallId)),
        ];
    }

    private function getRevenueChart($stallId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');
            
            $data[] = (float) $revenue;
        }
        return $data;
    }
}