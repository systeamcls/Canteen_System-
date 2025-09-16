<?php
// Fix for your QuickActionsWidget.php - REPLACE the existing file

namespace App\Filament\Cashier\Widgets;

use Filament\Widgets\Widget;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static ?int $sort = 3; // Changed from 2 to avoid conflict
    protected static string $view = 'filament.cashier.widgets.quick-actions';
    
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 1,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 2,
    ];

    public function getViewData(): array
    {
        $adminStallId = $this->getAdminStallId();
        
        return [
            // Filter by admin's stall only
            'pendingOrders' => Order::whereHas('orderItems.product', function ($query) use ($adminStallId) {
                $query->where('stall_id', $adminStallId);
            })->whereIn('status', ['pending', 'processing'])->count(),
            
            'lowStockItems' => Product::where('stall_id', $adminStallId)
                ->where('is_available', false)->count(),
                
            'todayOrdersCount' => Order::whereHas('orderItems.product', function ($query) use ($adminStallId) {
                $query->where('stall_id', $adminStallId);
            })->whereDate('created_at', today())->count(),
            
            'completedTodayCount' => Order::whereHas('orderItems.product', function ($query) use ($adminStallId) {
                $query->where('stall_id', $adminStallId);
            })->whereDate('created_at', today())
                ->where('status', 'completed')
                ->count(),
        ];
    }

    private function getAdminStallId(): ?int
    {
        $user = Auth::user();
        
        if ($user->admin_stall_id) {
            return $user->admin_stall_id;
        }
        
        $stall = \App\Models\Stall::where('owner_id', $user->id)->first();
        if ($stall) {
            return $stall->id;
        }
        
        return 1;
    }

    // These methods are for the view buttons - keep them simple
    public function refreshData(): void
    {
        $this->dispatch('$refresh');
        
        \Filament\Notifications\Notification::make()
            ->title('Data refreshed')
            ->success()
            ->send();
    }
}