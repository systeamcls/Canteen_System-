<?php
// app/Filament/Cashier/Widgets/CashierStatsWidget.php

namespace App\Filament\Cashier\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CashierStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $adminStallId = $this->getAdminStallId();
        
        return [
            // Orders to process (operational focus, not revenue)
            Stat::make('🔔 Orders Today', $this->getTodayOrdersCount($adminStallId))
                ->description('Orders to fulfill')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),
                
            Stat::make('⏳ Pending Orders', $this->getPendingOrdersCount($adminStallId))
                ->description('Need attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
                
            Stat::make('✅ Completed Today', $this->getCompletedTodayCount($adminStallId))
                ->description('Orders fulfilled')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('⚠️ Stock Alerts', $this->getStockAlertsCount($adminStallId))
                ->description('Items need attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($this->getStockAlertsCount($adminStallId) > 0 ? 'warning' : 'success'),
        ];
    }

    private function getAdminStallId(): ?int
    {
        $user = Auth::user();
        
        // Get admin stall ID using same logic as POS
        if ($user->admin_stall_id) {
            return $user->admin_stall_id;
        }
        
        // Fallback to stall owned by user
        $stall = \App\Models\Stall::where('owner_id', $user->id)->first();
        if ($stall) {
            return $stall->id;
        }
        
        // Fallback to stall_id = 1
        return 1;
    }

    private function getTodayOrdersCount(int $stallId): int
    {
        return Order::whereHas('orderItems.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereDate('created_at', today())
        ->count();
    }

    private function getPendingOrdersCount(int $stallId): int
    {
        return Order::whereHas('orderItems.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->whereIn('status', ['pending', 'processing'])
        ->count();
    }

    private function getCompletedTodayCount(int $stallId): int
    {
        return Order::whereHas('orderItems.product', function ($query) use ($stallId) {
            $query->where('stall_id', $stallId);
        })
        ->where('status', 'completed')
        ->whereDate('updated_at', today())
        ->count();
    }

    private function getStockAlertsCount(int $stallId): int
    {
        // Count unavailable products (simple stock alert)
        return Product::where('stall_id', $stallId)
            ->where('is_published', true)
            ->where('is_available', false)
            ->count();
    }
}
