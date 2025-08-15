<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                Stat::make('Daily Orders', '0')
                    ->description('No stall assigned')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Get today's orders
        $todayOrders = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereDate('created_at', today())
        ->count();

        // Get yesterday's orders for comparison
        $yesterdayOrders = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereDate('created_at', today()->subDay())
        ->count();

        // Calculate trend
        $trend = $todayOrders - $yesterdayOrders;
        $description = match (true) {
            $trend > 0 => "↗ +{$trend} from yesterday",
            $trend < 0 => "↘ {$trend} from yesterday", 
            default => "→ Same as yesterday"
        };

        $color = match (true) {
            $trend > 0 => 'success',
            $trend < 0 => 'warning',
            default => 'gray'
        };

        $icon = match (true) {
            $trend > 0 => 'heroicon-m-arrow-trending-up',
            $trend < 0 => 'heroicon-m-arrow-trending-down',
            default => 'heroicon-m-minus'
        };

        return [
            Stat::make('Daily Orders', number_format($todayOrders))
                ->description($description)
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($this->getOrdersChart($stallId)),
        ];
    }

    private function getOrdersChart($stallId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $orders = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->count();
            
            $data[] = $orders;
        }
        return $data;
    }
}