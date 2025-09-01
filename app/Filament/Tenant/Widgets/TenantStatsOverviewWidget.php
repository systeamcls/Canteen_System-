<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 2,
        'lg' => 4,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Get tenant's assigned stall
        $stall = $user->assignedStall;
        
        if (!$stall) {
            return [
                Stat::make('No Stall Assigned', 'Contact Admin')
                    ->description('You need to be assigned to a stall')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Today's sales from completed orders
        $todaySales = Order::whereHas('items.product', function ($query) use ($stall) {
            $query->where('stall_id', $stall->id);
        })
        ->whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_amount');

        // Pending orders count
        $pendingOrders = Order::whereHas('items.product', function ($query) use ($stall) {
            $query->where('stall_id', $stall->id);
        })
        ->whereIn('status', ['pending', 'processing'])
        ->count();

        // Total products count
        $totalProducts = Product::where('stall_id', $stall->id)->count();
        
        // Available products count
        $availableProducts = Product::where('stall_id', $stall->id)
            ->where('is_available', true)
            ->count();

        // Average rating from reviews
        $avgRating = Review::whereHas('product', function ($query) use ($stall) {
            $query->where('stall_id', $stall->id);
        })
        ->avg('rating');

        // This week's sales
        $weekSales = Order::whereHas('items.product', function ($query) use ($stall) {
            $query->where('stall_id', $stall->id);
        })
        ->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])
        ->where('status', 'completed')
        ->sum('total_amount');

        return [
            Stat::make('Today\'s Sales', 'PHP ' . number_format($todaySales, 2))
                ->description('Revenue from completed orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getDailySalesChart($stall->id, 7)),

            Stat::make('Pending Orders', $pendingOrders)
                ->description('Orders awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 5 ? 'warning' : 'info'),

            Stat::make('Products', $availableProducts . '/' . $totalProducts)
                ->description('Available products')
                ->descriptionIcon('heroicon-m-cube')
                ->color($availableProducts === $totalProducts ? 'success' : 'warning'),

            Stat::make('Rating', $avgRating ? number_format($avgRating, 1) . '/5' : 'No reviews')
                ->description('Average customer rating')
                ->descriptionIcon('heroicon-m-star')
                ->color($avgRating >= 4 ? 'success' : ($avgRating >= 3 ? 'warning' : 'danger')),

            Stat::make('Week Sales', 'PHP ' . number_format($weekSales, 2))
                ->description('This week\'s total revenue')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Stall Status', $stall->is_active ? 'Active' : 'Inactive')
                ->description($stall->is_active ? 'Accepting orders' : 'Temporarily closed')
                ->descriptionIcon($stall->is_active ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($stall->is_active ? 'success' : 'danger'),
        ];
    }

    private function getDailySalesChart(int $stallId, int $days): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Order::whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->sum('total_amount');

            $data[] = (float) $sales;
        }
        return $data;
    }
}