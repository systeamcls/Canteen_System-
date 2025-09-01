<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminSalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Trend Analysis';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 3,
    ];

    public ?string $filter = '7days';

    protected function getData(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Data Available',
                        'data' => [0, 0, 0, 0, 0, 0, 0],
                        'borderColor' => '#ef4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => ['6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday', 'Today'],
            ];
        }

        $days = match ($this->filter) {
            '7days' => 7,
            '14days' => 14,
            '30days' => 30,
            default => 7,
        };

        $data = $this->getDailySalesData($stallId, $days);

        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales (PHP)',
                    'data' => $data['sales'],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '7days' => 'Last 7 Days',
            '14days' => 'Last 14 Days', 
            '30days' => 'Last 30 Days',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'cornerRadius' => 8,
                    'callbacks' => [
                        'label' => "function(context) { 
                            return 'Sales: PHP ' + context.parsed.y.toLocaleString(); 
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
                ],
                'y' => [
                    'display' => true,
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                    'ticks' => [
                        'callback' => "function(value) { return 'PHP ' + value.toLocaleString(); }",
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }

    protected function getDailySalesData($stallId, $days): array
    {
        $sales = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');

            $sales[] = (float) $dailySales;
            
            // Format labels based on period
            if ($days <= 7) {
                $labels[] = $date->format('M j');
            } elseif ($days <= 14) {
                $labels[] = $i % 2 === 0 ? $date->format('M j') : '';
            } else {
                $labels[] = $i % 3 === 0 ? $date->format('M j') : '';
            }
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }
}