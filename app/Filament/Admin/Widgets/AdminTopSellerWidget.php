<?php

namespace App\Filament\Admin\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminTopSellerWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = [
    'md' => 12,
    'lg' => 4,
    'xl' => 4,
];

    protected function getStats(): array
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return [
                Stat::make('Top Seller Today', 'No Data')
                    ->description('No stall assigned')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        // Get top selling product today
        $topSeller = OrderItem::whereHas('product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereHas('order', function ($query) {
            $query->whereDate('created_at', today())
                  ->where('status', 'completed');
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
        ->groupBy('product_id')
        ->orderByDesc('total_sold')
        ->with('product')
        ->first();

        if (!$topSeller) {
            return [
                Stat::make('Top Seller Today', 'No Sales')
                    ->description('No completed orders today')
                    ->descriptionIcon('heroicon-m-information-circle')
                    ->color('gray'),
            ];
        }

        $product = $topSeller->product;
        $quantitySold = $topSeller->total_sold;

        // Get sales for the last 7 days for chart
        $chartData = $this->getProductSalesChart($product->id);

        return [
            Stat::make('Top Seller Today', $product->name)
                ->description("{$quantitySold} units sold")
                ->descriptionIcon('heroicon-m-fire')
                ->color('success')
                ->chart($chartData),
        ];
    }

    private function getProductSalesChart($productId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $sales = OrderItem::where('product_id', $productId)
                ->whereHas('order', function ($query) use ($date) {
                    $query->whereDate('created_at', $date)
                          ->where('status', 'completed');
                })
                ->sum('quantity');

            $data[] = (int) $sales;
        }
        return $data;
    }
}