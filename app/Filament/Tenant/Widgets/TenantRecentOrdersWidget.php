<?php
// app/Filament/Tenant/Widgets/TenantRecentOrders.php
namespace App\Filament\Tenant\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class TenantRecentOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Orders';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        return $table
            ->query(function () use ($stall) {
                if (!$stall) {
                    return Order::query()->whereRaw('1 = 0');
                }

                return Order::query()
                    ->whereHas('items.product', function (Builder $query) use ($stall) {
                        $query->where('stall_id', $stall->id);
                    })
                    ->with(['user'])
                    ->latest()
                    ->limit(5);
            })
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->weight('medium')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->getStateUsing(function (Order $record): string {
                        if ($record->user) {
                            return $record->user->name;
                        }
                        return $record->customer_name ?: 'Guest';
                    })
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record): string => 
                        route('filament.tenant.resources.tenant-orders.view', $record)
                    )
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No recent orders')
            ->emptyStateDescription('Orders will appear here as customers place them.')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->paginated(false);
    }
}