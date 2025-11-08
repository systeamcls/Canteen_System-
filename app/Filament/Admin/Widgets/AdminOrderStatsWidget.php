<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOrderStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    public ?string $filter = 'today';

    protected function getStats(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                Stat::make('No Stall Assigned', 'Please assign a stall')
                    ->color('danger'),
            ];
        }

        $dateRange = $this->getDateRange();
        
        return [
            Stat::make('Total Orders', $this->getTotalOrders($stallId, $dateRange))
                ->description($this->getOrdersChange($stallId, $dateRange))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info')
                ->chart($this->getOrdersChart($stallId)),
                
            Stat::make('Completed Orders', $this->getCompletedOrders($stallId, $dateRange))
                ->description($this->getCompletionRate($stallId, $dateRange) . '% completion rate')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Average Order Value', $this->formatCurrency($this->getAverageOrderValue($stallId, $dateRange)))
                ->description('Per transaction')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
                
            Stat::make('Peak Hour', $this->getPeakHour($stallId, $dateRange))
                ->description('Most orders received')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getDateRange(): array
    {
        return match($this->filter) {
            'today' => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            ],
            'week' => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ],
            'month' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ],
            'year' => [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            ],
            default => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            ],
        };
    }

    protected function getTotalOrders($stallId, array $dateRange): int
    {
        return Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->count();
    }

    protected function getCompletedOrders($stallId, array $dateRange): int
    {
        return Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->where('status', 'completed')
        ->count();
    }

    protected function getAverageOrderValue($stallId, array $dateRange): float
    {
        $avg = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->where('status', 'completed')
        ->avg('total_amount');

        return $avg ?? 0;
    }

    protected function getPeakHour($stallId, array $dateRange): string
    {
        $result = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $dateRange)
        ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
        ->groupBy('hour')
        ->orderByDesc('count')
        ->first();
        
        if (!$result) {
            return 'N/A';
        }
        
        $hour = $result->hour;
        $nextHour = ($hour + 1) % 24;
        
        return sprintf('%02d:00-%02d:00', $hour, $nextHour);
    }

    protected function getCompletionRate($stallId, array $dateRange): string
    {
        $total = $this->getTotalOrders($stallId, $dateRange);
        
        if ($total == 0) {
            return '0';
        }
        
        $completed = $this->getCompletedOrders($stallId, $dateRange);
        
        return number_format(($completed / $total) * 100, 1);
    }

    protected function getOrdersChange($stallId, array $dateRange): string
    {
        $current = $this->getTotalOrders($stallId, $dateRange);
        $previous = $this->getPreviousPeriodOrders($stallId);
        
        if ($previous == 0) {
            return $current > 0 ? 'First orders!' : 'No orders yet';
        }
        
        $change = (($current - $previous) / $previous) * 100;
        
        return ($change >= 0 ? '+' : '') . number_format($change, 1) . '% from previous period';
    }

    protected function getPreviousPeriodOrders($stallId): int
    {
        $previousRange = $this->getPreviousPeriodRange();
        
        return Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereBetween('created_at', $previousRange)
        ->count();
    }

    protected function getPreviousPeriodRange(): array
    {
        return match($this->filter) {
            'today' => [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ],
            'week' => [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ],
            'month' => [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ],
            'year' => [
                Carbon::now()->subYear()->startOfYear(),
                Carbon::now()->subYear()->endOfYear()
            ],
            default => [
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay()
            ],
        };
    }

    protected function getOrdersChart($stallId): array
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $orders = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->count();
            
            $data[] = $orders;
        }
        
        return $data;
    }

    protected function formatCurrency(float $amount): string
    {
        return '₱' . number_format($amount, 2);
    }
}