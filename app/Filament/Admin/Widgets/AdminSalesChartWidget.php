<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Stall;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Support\RawJs;

class AdminSalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Trend';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
    'md' => 12,
    'lg' => 6,
    'xl' => 6,
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
                        'data' => array_fill(0, 7, 0),
                        'borderColor' => '#ef4444',
                    ],
                ],
                'labels' => array_fill(0, 7, ''),
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