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
    protected int | string | array $columnSpan = 'full';


    public ?string $filter = 'week';

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

        $data = match ($this->filter) {
            'today' => $this->getTodayData($stallId),
            'week' => $this->getWeekData($stallId),
            'month' => $this->getMonthData($stallId),
            'year' => $this->getYearData($stallId),
            '7days' => $this->getDailySalesData($stallId, 7),
            '14days' => $this->getDailySalesData($stallId, 14),
            '30days' => $this->getDailySalesData($stallId, 30),
            default => $this->getWeekData($stallId),
        };

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
            'today' => 'Today (Hourly)',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
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

    protected function getTodayData($stallId): array
    {
        $sales = [];
        $labels = [];

        // Get sales by hour for today
        for ($hour = 0; $hour < 24; $hour++) {
            $hourlySales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', Carbon::today())
            ->whereRaw('HOUR(created_at) = ?', [$hour])
            ->where('status', 'completed')
            ->sum('total_amount');

            $sales[] = (float) $hourlySales;
            $labels[] = sprintf('%02d:00', $hour);
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }

    protected function getWeekData($stallId): array
    {
        $sales = [];
        $labels = [];

        // Get sales for each day this week (Monday to Sunday)
        $startOfWeek = Carbon::now()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            
            $dailySales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');

            $sales[] = (float) $dailySales;
            $labels[] = $date->format('D, M j'); // Mon, Jan 1
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }

    protected function getMonthData($stallId): array
    {
        $sales = [];
        $labels = [];

        // Get sales for each day this month
        $startOfMonth = Carbon::now()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->addDays($day - 1);
            
            $dailySales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');

            $sales[] = (float) $dailySales;
            
            // Show every 3rd day label to avoid crowding
            $labels[] = ($day % 3 === 1 || $day === 1) ? $date->format('M j') : '';
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }

    protected function getYearData($stallId): array
    {
        $sales = [];
        $labels = [];

        // Get sales for each month this year
        for ($month = 1; $month <= 12; $month++) {
            $monthlySales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', $month)
            ->where('status', 'completed')
            ->sum('total_amount');

            $sales[] = (float) $monthlySales;
            $labels[] = Carbon::create()->month($month)->format('M'); // Jan, Feb, etc
        }

        return [
            'sales' => $sales,
            'labels' => $labels,
        ];
    }

    
}