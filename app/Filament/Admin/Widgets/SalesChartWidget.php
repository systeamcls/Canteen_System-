<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Chart';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = $this->getDailySalesData();

        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales',
                    'data' => $data['sales'],
                    'borderColor' => '#9061F9',
                    'fill' => false,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getDailySalesData(): array
    {
        $days = 7;
        $sales = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');

            $sales[] = $dailySales;
            $labels[] = $date->format('M d');
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }
}