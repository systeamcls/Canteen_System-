<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminMonthlyRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                Stat::make('Monthly Revenue', 'PHP 0.00')
                    ->description('No stall assigned')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Get this month's revenue
        $thisMonthRevenue = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])
        ->where('status', 'completed')
        ->sum('total_amount');

        // Get last month's revenue for comparison
        $lastMonthRevenue = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])
        ->where('status', 'completed')
        ->sum('total_amount');

        // Calculate percentage change
        $percentageChange = 0;
        if ($lastMonthRevenue > 0) {
            $percentageChange = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        $description = $percentageChange >= 0 
            ? sprintf('↗ %.1f%% vs last month', $percentageChange)
            : sprintf('↘ %.1f%% vs last month', abs($percentageChange));

        $color = $percentageChange >= 0 ? 'success' : 'danger';
        $icon = $percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        return [
            Stat::make('Monthly Revenue', 'PHP ' . number_format($thisMonthRevenue, 2))
                ->description($description)
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($this->getMonthlyChart($stallId)),
        ];
    }

    private function getMonthlyChart($stallId): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereBetween('created_at', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth()
            ])
            ->where('status', 'completed')
            ->sum('total_amount');
            
            $data[] = (float) $revenue;
        }
        return $data;
    }
}