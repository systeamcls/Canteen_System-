<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;

class AdminPopularHoursWidget extends ChartWidget
{
    protected static ?string $heading = 'Peak Order Hours';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = [
    'md' => 12,
    'lg' => 4,
    'xl' => 4,
];

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Data',
                        'data' => array_fill(0, 24, 0),
                        'backgroundColor' => '#ef4444',
                    ],
                ],
                'labels' => range(0, 23),
            ];
        }

        $query = Order::whereHas('items.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        });

        // Apply date filter
        match ($this->filter) {
            'today' => $query->whereDate('created_at', today()),
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month),
            default => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
        };

        $hourlyData = $query->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('hour')
            ->pluck('order_count', 'hour')
            ->toArray();

        // Fill missing hours with 0
        $data = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $data[] = $hourlyData[$hour] ?? 0;
        }

        // Create gradient colors based on order volume
        $maxOrders = max($data) ?: 1;
        $backgroundColor = array_map(function ($count) use ($maxOrders) {
            $intensity = $count / $maxOrders;
            $alpha = 0.3 + ($intensity * 0.7); // 0.3 to 1.0
            return "rgba(59, 130, 246, {$alpha})";
        }, $data);

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_map(function ($hour) {
                return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            }, range(0, 23)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
        ];
    }

    protected function getOptions(): array
{
    return [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => false,
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
                    'stepSize' => 1,
                ],
            ],
        ],
    ];
}
}