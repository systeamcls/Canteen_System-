<?php
// app/Filament/Cashier/Pages/Dashboard.php (COMPLETE REPLACEMENT)

namespace App\Filament\Cashier\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Cashier\Widgets\CashierStatsWidget;
use App\Filament\Cashier\Widgets\LiveOrdersWidget;
use App\Filament\Cashier\Widgets\ProductStatusWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $title = '💰 Cashier Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;

    protected static string|array $routeMiddleware = 'auth';
    
    public function getWidgets(): array
    {
        return [
            // Row 1: Stats - 4 columns across
            CashierStatsWidget::class,
            
            // Row 2: Live orders - Full width
            LiveOrdersWidget::class,
            
            // Row 3: Product management - Full width
            ProductStatusWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 4, // Changed to 4 for stats, then full width for others
            'xl' => 4,
            '2xl' => 4,
        ];
    }

    public function getHeaderActions(): array
{
    return [
        \Filament\Actions\Action::make('open_pos')
            ->label('🛒 Open POS')
            ->icon('heroicon-o-calculator')
            ->color('success')
            ->size('lg')
            ->url('/cashier/pos-system')  // This should work
            ->openUrlInNewTab(false)  // Try without opening new tab
            ->tooltip('Open Point of Sale system'),
            
        \Filament\Actions\Action::make('view_orders')
            ->label('📋 All Orders')
            ->icon('heroicon-o-shopping-bag')
            ->color('primary')
            ->size('lg')
            ->url(route('filament.cashier.resources.cashier-orders.index'))  // Use route helper
            ->tooltip('View all orders'),
            
        \Filament\Actions\Action::make('refresh_data')
            ->label('🔄 Refresh')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->action('refreshData')
            ->tooltip('Refresh dashboard data'),
    ];
}

    public function refreshData(): void
    {
        $this->dispatch('$refresh');
        
        \Filament\Notifications\Notification::make()
            ->title('Dashboard refreshed')
            ->icon('heroicon-o-check-circle')
            ->success()
            ->send();
    }

    protected function getPollingInterval(): ?string
    {
        return '5s'; // Dashboard-wide polling
    }

    public function getHeading(): string
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $greeting = $this->getTimeBasedGreeting();
        
        return "{$greeting}, {$user->name}! 👋";
    }

    public function getSubheading(): ?string
    {
        return 'Ready to serve customers today? Let\'s make some sales! 💪';
    }

    private function getTimeBasedGreeting(): string
{
    // Use Philippines timezone (or your timezone)
    $hour = now()->timezone('Asia/Manila')->hour;
    
    return match (true) {
        $hour >= 5 && $hour < 12 => 'Good Morning',
        $hour >= 12 && $hour < 17 => 'Good Afternoon',
        $hour >= 17 && $hour < 21 => 'Good Evening',
        default => 'Good Night'
    };
}
}