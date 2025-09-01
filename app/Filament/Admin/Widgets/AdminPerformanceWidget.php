<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminPerformanceWidget extends ChartWidget
{
    protected static ?string $heading = 'Performance Overview (30 Days)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 3,
    ];

    protected function getData(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Data Available',
                        'data' => array_fill(0, 30, 0),
                        'borderColor' => '#ef4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    ],
                ],
                'labels' => array_fill(0, 30, ''),
            ];
        }

        $data = $this->getPerformanceData($stallId);

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (PHP)',
                    'data' => $data['revenue'],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Orders Count',
                    'data' => $data['orders'],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
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
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'cornerRadius' => 8,
                    'callbacks' => [
                        'label' => "function(context) {
                            if (context.dataset.label === 'Revenue (PHP)') {
                                return 'Revenue: PHP ' + context.parsed.y.toLocaleString();
                            } else {
                                return 'Orders: ' + context.parsed.y;
                            }
                        }",
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'maxTicksLimit' => 7,
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue (PHP)',
                        'color' => '#10b981',
                    ],
                    'grid' => [
                        'color' => 'rgba(16, 185, 129, 0.1)',
                    ],
                    'ticks' => [
                        'callback' => "function(value) { return 'PHP ' + value.toLocaleString(); }",
                        'color' => '#10b981',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Orders Count',
                        'color' => '#3b82f6',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'ticks' => [
                        'color' => '#3b82f6',
                    ],
                ],
            ],
        ];
    }

    protected function getPerformanceData($stallId): array
    {
        $days = 30;
        $revenue = [];
        $orders = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $dailyRevenue = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');

            $dailyOrders = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->count();

            $revenue[] = (float) $dailyRevenue;
            $orders[] = $dailyOrders;
            
            // Show fewer labels for cleaner appearance
            if ($i % 5 === 0 || $i === 0) {
                $labels[] = $date->format('M j');
            } else {
                $labels[] = '';
            }
        }

        return [
            'revenue' => $revenue,
            'orders' => $orders,
            'labels' => $labels,
        ];
    }
}