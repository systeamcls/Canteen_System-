<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminSalesChartWidget extends ChartWidget
{
    protected static ?string $heading = '7-Day Sales Trend';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                'datasets' => [
                    [
                        'label' => 'Daily Sales',
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

        $data = $this->getDailySalesData($stallId);

        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales (PHP)',
                    'data' => $data['sales'],
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#ef4444',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'labels' => [
                        'color' => '#e2e8f0',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'color' => '#94a3b8',
                    ],
                    'grid' => [
                        'color' => '#334155',
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'color' => '#94a3b8',
                        'callback' => "function(value) { return 'PHP ' + value.toLocaleString(); }",
                    ],
                    'grid' => [
                        'color' => '#334155',
                    ],
                ],
            ],
        ];
    }

    protected function getDailySalesData($stallId): array
    {
        $days = 7;
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
            $labels[] = $date->format('M d');
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }
}