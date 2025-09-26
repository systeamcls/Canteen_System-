<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalesAnalyticsWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.sales-analytics';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public function getSalesMetrics(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return [
                'today' => 0,
                'week' => 0,
                'month' => 0,
                'average_order_value' => 0
            ];
        }

        $baseQuery = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.stall_id', $adminStallId)
            ->where('orders.payment_status', 'paid');

        return [
            'today' => (clone $baseQuery)
                ->whereDate('orders.created_at', Carbon::today())
                ->sum(DB::raw('order_items.quantity * order_items.unit_price')),
            
            'week' => (clone $baseQuery)
                ->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum(DB::raw('order_items.quantity * order_items.unit_price')),
            
            'month' => (clone $baseQuery)
                ->whereMonth('orders.created_at', Carbon::now()->month)
                ->whereYear('orders.created_at', Carbon::now()->year)
                ->sum(DB::raw('order_items.quantity * order_items.unit_price')),
            
            'average_order_value' => round((clone $baseQuery)
                ->whereMonth('orders.created_at', Carbon::now()->month)
                ->avg('orders.total_amount'), 2)
        ];
    }

    public function getSalesTrend(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return ['labels' => [], 'data' => []];
        }

        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sales = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.stall_id', $adminStallId)
                ->where('orders.payment_status', 'paid')
                ->whereDate('orders.created_at', $date)
                ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

            $last7Days->push([
                'date' => $date->format('M j'),
                'sales' => $sales ?? 0
            ]);
        }

        return [
            'labels' => $last7Days->pluck('date')->toArray(),
            'data' => $last7Days->pluck('sales')->toArray()
        ];
    }

    public function getTopSellingItems(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return ['labels' => [], 'data' => []];
        }

        $topItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('products.stall_id', $adminStallId)
            ->where('orders.payment_status', 'paid')
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return [
            'labels' => $topItems->pluck('name')->toArray(),
            'data' => $topItems->pluck('total_sold')->toArray()
        ];
    }

    public function getPaymentBreakdown(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return ['labels' => [], 'data' => []];
        }

        $paymentData = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.stall_id', $adminStallId)
            ->where('orders.payment_status', 'paid')
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->select('orders.payment_method', DB::raw('SUM(order_items.quantity * order_items.unit_price) as total'))
            ->groupBy('orders.payment_method')
            ->get();

        $labels = $paymentData->pluck('payment_method')->map(function($method) {
            return $method === 'cash' ? 'Cash' : ucfirst($method ?? 'Online');
        })->toArray();

        return [
            'labels' => $labels,
            'data' => $paymentData->pluck('total')->toArray()
        ];
    }
}