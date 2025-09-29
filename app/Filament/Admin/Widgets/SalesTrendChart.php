<?php

namespace App\Filament\Admin\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;

class SalesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Trend (Last 30 Days)';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $analytics = new AnalyticsService();
        $data = $analytics->getSalesTrend('daily', 30);

        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales (₱)',
                    'data' => collect($data)->pluck('sales')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => collect($data)->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '₱' + value.toLocaleString(); }",
                    ],
                ],
            ],
        ];
    }
}