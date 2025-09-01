<?php

// app/Filament/Tenant/Widgets/TenantSalesChart.php
namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TenantSalesChartWidget extends ChartWidget
{
    protected static ?string $heading = '7-Day Sales Trend';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'lg' => 2,
        'xl' => 3,
    ];

    protected function getData(): array
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        if (!$stall) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Data',
                        'data' => [0, 0, 0, 0, 0, 0, 0],
                        'borderColor' => '#ef4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    ],
                ],
                'labels' => ['6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday', 'Today'],
            ];
        }

        $data = $this->getDailySalesData($stall->id);

        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales (PHP)',
                    'data' => $data['sales'],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getDailySalesData(int $stallId): array
    {
        $sales = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailySales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');

            $sales[] = (float) $dailySales;
            $labels[] = $date->format('M j');
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }
}