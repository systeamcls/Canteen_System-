<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stall;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Get admin's stall data only (security requirement)
        $adminStall = Auth::user()->stall;
        
        return [
            $this->getRevenueToday($adminStall),
            $this->getRevenueThisMonth($adminStall),
            $this->getTotalOrdersToday($adminStall),
            $this->getTopSellerToday($adminStall),
        ];
    }

    protected function getRevenueToday($adminStall): Stat
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayRevenue = $this->getStallRevenue($adminStall, $today, $today);
        $yesterdayRevenue = $this->getStallRevenue($adminStall, $yesterday, $yesterday);

        $percentageChange = $this->calculatePercentageChange($yesterdayRevenue, $todayRevenue);
        $trend = $this->getRevenueChart($adminStall, 7);

        return Stat::make('Revenue (Today)', 'PHP ' . number_format($todayRevenue, 2))
            ->description($this->formatPercentageChange($percentageChange, 'from yesterday'))
            ->descriptionIcon($percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->chart($trend)
            ->color($percentageChange >= 0 ? 'success' : 'danger');
    }

    protected function getRevenueThisMonth($adminStall): Stat
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $thisMonthRevenue = $this->getStallRevenue($adminStall, $currentMonth, Carbon::now());
        $lastMonthRevenue = $this->getStallRevenue($adminStall, $lastMonth, $lastMonthEnd);

        $percentageChange = $this->calculatePercentageChange($lastMonthRevenue, $thisMonthRevenue);

        return Stat::make('Revenue (Month)', 'PHP ' . number_format($thisMonthRevenue, 2))
            ->description($this->formatPercentageChange($percentageChange, 'from last month'))
            ->descriptionIcon($percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($percentageChange >= 0 ? 'success' : 'danger');
    }

    protected function getTotalOrdersToday($adminStall): Stat
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayOrders = $this->getStallOrdersCount($adminStall, $today, $today);
        $yesterdayOrders = $this->getStallOrdersCount($adminStall, $yesterday, $yesterday);

        $percentageChange = $this->calculatePercentageChange($yesterdayOrders, $todayOrders);

        return Stat::make('Total Orders (Today)', number_format($todayOrders))
            ->description($this->formatPercentageChange($percentageChange, 'from yesterday'))
            ->descriptionIcon($percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($percentageChange >= 0 ? 'success' : 'danger');
    }

    protected function getTopSellerToday($adminStall): Stat
    {
        $topProduct = $this->getTopSellingProduct($adminStall);
        
        if (!$topProduct) {
            return Stat::make('Top Seller Today', 'No sales yet')
                ->description('No products sold today')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('gray');
        }

        return Stat::make('Top Seller Today', $topProduct['name'])
            ->description($topProduct['quantity'] . ' sold • PHP ' . number_format($topProduct['revenue'], 2))
            ->descriptionIcon('heroicon-m-fire')
            ->color('warning');
    }

    protected function getStallRevenue($stall, $startDate, $endDate): float
    {
        if (!$stall) {
            return 0;
        }

        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereHas('items.product', function ($query) use ($stall) {
                $query->where('stall_id', $stall->id);
            })
            ->with('items.product')
            ->get()
            ->sum(function ($order) use ($stall) {
                return $order->items->where('product.stall_id', $stall->id)->sum('subtotal');
            });
    }

    protected function getStallOrdersCount($stall, $startDate, $endDate): int
    {
        if (!$stall) {
            return 0;
        }

        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('items.product', function ($query) use ($stall) {
                $query->where('stall_id', $stall->id);
            })
            ->distinct()
            ->count();
    }

    protected function getTopSellingProduct($stall): ?array
    {
        if (!$stall) {
            return null;
        }

        $topItem = OrderItem::whereDate('created_at', Carbon::today())
            ->whereHas('product', function ($query) use ($stall) {
                $query->where('stall_id', $stall->id);
            })
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->first();

        if (!$topItem) {
            return null;
        }

        return [
            'name' => $topItem->product->name,
            'quantity' => $topItem->total_quantity,
            'revenue' => $topItem->total_revenue,
        ];
    }

    protected function getRevenueChart($stall, $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = $this->getStallRevenue($stall, $date->startOfDay(), $date->endOfDay());
            $data[] = $revenue;
        }
        return $data;
    }

    protected function calculatePercentageChange($oldValue, $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return (($newValue - $oldValue) / $oldValue) * 100;
    }

    protected function formatPercentageChange($percentage, $period): string
    {
        $sign = $percentage >= 0 ? '+' : '';
        return $sign . number_format($percentage, 1) . '% ' . $period;
    }
}