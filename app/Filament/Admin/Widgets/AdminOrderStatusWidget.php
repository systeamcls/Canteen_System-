<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminOrderStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Order Status Distribution';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    public ?string $filter = 'today';

    protected function getData(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#ef4444'],
                    ],
                ],
                'labels' => ['No Data'],
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
            default => $query->whereDate('created_at', today()),
        };

        $statusCounts = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $colors = [
            'pending' => '#f59e0b',
            'processing' => '#3b82f6',
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
        ];

        $labels = [];
        $data = [];
        $backgroundColor = [];

        foreach ($statusCounts as $status => $count) {
            $labels[] = ucfirst($status) . " ({$count})";
            $data[] = $count;
            $backgroundColor[] = $colors[$status] ?? '#6b7280';
        }

        if (empty($data)) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e5e7eb'],
                    ],
                ],
                'labels' => ['No Orders'],
            ];
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
                    'position' => 'bottom',
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
                ],
            ],
            'cutout' => '60%',
            'elements' => [
                'arc' => [
                    'borderWidth' => 2,
                ],
            ],
        ];
    }
}