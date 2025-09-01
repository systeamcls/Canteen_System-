<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminQuickStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 2,
        'lg' => 3,
        'xl' => 4,
        '2xl' => 6,
    ];

    protected function getStats(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                Stat::make('No Stall Assigned', 'Contact Admin')
                    ->description('You need to be assigned to a stall to view analytics')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Today's metrics
        $todayRevenue = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_amount');

        $todayOrders = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereDate('created_at', today())
        ->count();

        $pendingOrders = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->where('status', 'pending')
        ->count();

        // This week's revenue
        $weekRevenue = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->where('status', 'completed')
        ->sum('total_amount');

        // Product metrics
        $totalProducts = Product::where('stall_id', $stallId)->count();
        $availableProducts = Product::where('stall_id', $stallId)
            ->where('is_available', true)
            ->count();

        // Average order value today
        $avgOrderValue = $todayOrders > 0 ? $todayRevenue / $todayOrders : 0;

        return [
            Stat::make('Today\'s Revenue', 'PHP ' . number_format($todayRevenue, 2))
                ->description('Total sales today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getRevenueChart($stallId, 7)),

            Stat::make('Today\'s Orders', number_format($todayOrders))
                ->description('New orders received')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary')
                ->chart($this->getOrdersChart($stallId, 7)),

            Stat::make('Pending Orders', number_format($pendingOrders))
                ->description('Awaiting fulfillment')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 5 ? 'warning' : 'gray'),

            Stat::make('Week Revenue', 'PHP ' . number_format($weekRevenue, 2))
                ->description('This week\'s total')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Products', $availableProducts . ' / ' . $totalProducts)
                ->description('Available products')
                ->descriptionIcon('heroicon-m-cube')
                ->color($availableProducts < $totalProducts ? 'warning' : 'success'),

            Stat::make('Avg Order Value', 'PHP ' . number_format($avgOrderValue, 2))
                ->description('Per order today')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('gray'),
        ];
    }

    private function getRevenueChart($stallId, $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
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

    private function getOrdersChart($stallId, $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
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