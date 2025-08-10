<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Daily Sales Trend (7 Days)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $adminStall = Auth::user()->stall;
        $data = $this->getDailySalesData($adminStall);

        return [
            'datasets' => [
                [
                    'label' => 'Daily Revenue (PHP)',
                    'data' => $data['sales'],
                    'borderColor' => '#ef4444', // Red color for dark theme
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointBackgroundColor' => '#ef4444',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                ],
                [
                    'label' => 'Orders Count',
                    'data' => $data['orders'],
                    'borderColor' => '#ffffff', // White color for contrast
                    'backgroundColor' => 'rgba(255, 255, 255, 0.1)',
                    'tension' => 0.4,
                    'fill' => false,
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderColor' => '#000000',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue (PHP)',
                        'color' => '#ef4444',
                    ],
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.1)',
                    ],
                    'ticks' => [
                        'color' => '#ffffff',
                        'callback' => 'function(value) { return "₱" + value.toLocaleString(); }',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Orders Count',
                        'color' => '#ffffff',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'ticks' => [
                        'color' => '#ffffff',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.1)',
                    ],
                    'ticks' => [
                        'color' => '#ffffff',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => '#ffffff',
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'borderColor' => '#ef4444',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getDailySalesData($stall): array
    {
        $days = 7;
        $sales = [];
        $orders = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            if ($stall) {
                // Get revenue for admin's stall only
                $dailyRevenue = Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->whereHas('items.product', function ($query) use ($stall) {
                        $query->where('stall_id', $stall->id);
                    })
                    ->with('items.product')
                    ->get()
                    ->sum(function ($order) use ($stall) {
                        return $order->items->where('product.stall_id', $stall->id)->sum('subtotal');
                    });

                // Get order count for admin's stall only
                $dailyOrders = Order::whereDate('created_at', $date)
                    ->whereHas('items.product', function ($query) use ($stall) {
                        $query->where('stall_id', $stall->id);
                    })
                    ->distinct()
                    ->count();
            } else {
                $dailyRevenue = 0;
                $dailyOrders = 0;
            }

            $sales[] = $dailyRevenue;
            $orders[] = $dailyOrders;
            $labels[] = $date->format('M d');
        }

        return [
            'sales' => $sales,
            'orders' => $orders,
            'labels' => $labels,
        ];
    }
}