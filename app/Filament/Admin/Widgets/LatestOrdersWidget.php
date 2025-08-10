<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Latest Orders';
    protected static ?string $pollingInterval = '5s'; // Real-time updates every 5 seconds

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $adminStall = Auth::user()->stall;
                
                if (!$adminStall) {
                    return Order::query()->whereRaw('1 = 0'); // Return empty query
                }

                // Show only orders containing products from admin's stall
                return Order::query()
                    ->whereHas('items.product', function (Builder $query) use ($adminStall) {
                        $query->where('stall_id', $adminStall->id);
                    })
                    ->with(['user', 'items.product'])
                    ->latest()
                    ->limit(10);
            })
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->copyable()
                    ->weight('semibold')
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->getStateUsing(function (Order $record): string {
                        if ($record->user) {
                            return $record->user->name;
                        }
                        
                        // For guest orders
                        $guestDetails = $record->guest_details;
                        return $guestDetails['name'] ?? 'Guest Customer';
                    })
                    ->searchable()
                    ->icon('heroicon-m-user'),
                
                Tables\Columns\TextColumn::make('stall_items')
                    ->label('Items')
                    ->getStateUsing(function (Order $record): string {
                        $adminStall = Auth::user()->stall;
                        
                        $stallItems = $record->items
                            ->where('product.stall_id', $adminStall->id)
                            ->map(function ($item) {
                                return $item->quantity . 'x ' . $item->product->name;
                            })
                            ->join(', ');
                            
                        return $stallItems ?: 'No items from your stall';
                    })
                    ->wrap()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('stall_total')
                    ->label('Stall Total')
                    ->getStateUsing(function (Order $record): float {
                        $adminStall = Auth::user()->stall;
                        
                        return $record->items
                            ->where('product.stall_id', $adminStall->id)
                            ->sum('subtotal');
                    })
                    ->money('PHP')
                    ->weight('semibold')
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'preparing' => 'primary',
                        'ready' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-m-clock',
                        'processing' => 'heroicon-m-cog-6-tooth',
                        'preparing' => 'heroicon-m-fire',
                        'ready' => 'heroicon-m-check-badge',
                        'completed' => 'heroicon-m-check-circle',
                        'cancelled' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    }),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'gcash' => 'info',
                        'card' => 'warning',
                        'online' => 'primary',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ordered')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn (Order $record): string => $record->created_at->format('F j, Y \a\t g:i A')),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No Recent Orders')
            ->emptyStateDescription('Orders containing your stall\'s products will appear here.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}