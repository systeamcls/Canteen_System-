<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class AdminLatestOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Orders';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    #[On('orderCreated')]
    public function refreshWidget(): void
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        return $table
            ->query(function () use ($stallId) {
                if (!$stallId) {
                    return Order::query()->whereRaw('1 = 0');
                }

                return Order::query()
                    ->whereHas('items.product', function ($query) use ($stallId) {
                        $query->where('stall_id', $stallId);
                    })
                    ->with(['user', 'items.product'])
                    ->latest()
                    ->limit(8);
            })
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('order_number')
                                ->label('Order #')
                                ->weight('semibold')
                                ->color('primary')
                                ->copyable()
                                ->copyableState(fn($record) => $record->order_number)
                                ->copyMessage('Order number copied!'),
                                
                            Tables\Columns\TextColumn::make('customer_display')
                                ->label('Customer')
                                ->getStateUsing(function (Order $record): string {
                                    if ($record->user) {
                                        return $record->user->name;
                                    }
                                    if ($record->customer_name) {
                                        return $record->customer_name;
                                    }
                                    return 'Guest Customer';
                                })
                                ->icon('heroicon-m-user')
                                ->color('gray'),
                        ])->space(1),
                        
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('total_amount')
                                ->money('PHP')
                                ->color('success')
                                ->weight('semibold')
                                ->alignEnd(),
                                
                            Tables\Columns\TextColumn::make('created_at')
                                ->since()
                                ->color('gray')
                                ->alignEnd(),
                        ])->space(1),
                    ]),
                    
                    Tables\Columns\Layout\Panel::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('order_items')
                                ->label('Items')
                                ->getStateUsing(function (Order $record) use ($stallId): string {
                                    $stallItems = $record->items()
                                        ->whereHas('product', function ($query) use ($stallId) {
                                            $query->where('stall_id', $stallId);
                                        })
                                        ->with('product')
                                        ->get();

                                    if ($stallItems->isEmpty()) {
                                        return 'No items from your stall';
                                    }

                                    return $stallItems->map(function ($item) {
                                        return $item->quantity . '× ' . $item->product->name;
                                    })->take(2)->join(', ') . ($stallItems->count() > 2 ? '...' : '');
                                })
                                ->icon('heroicon-m-shopping-cart')
                                ->color('info')
                                ->limit(50),
                                
                            Tables\Columns\TextColumn::make('status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'gray',
                                })
                                ->icon(fn (string $state): string => match ($state) {
                                    'pending' => 'heroicon-m-clock',
                                    'processing' => 'heroicon-m-cog-6-tooth',
                                    'completed' => 'heroicon-m-check-circle',
                                    'cancelled' => 'heroicon-m-x-circle',
                                    default => 'heroicon-m-question-mark-circle',
                                }),
                        ]),
                    ])->collapsible(),
                ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('View Details')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
                        ->openUrlInNewTab(),
                        
                    Tables\Actions\Action::make('mark_processing')
                        ->label('Start Processing')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->visible(fn (Order $record): bool => $record->status === 'pending')
                        ->action(function (Order $record) {
                            $record->update(['status' => 'processing']);
                            $this->dispatch('$refresh');
                        })
                        ->requiresConfirmation(),
                        
                    Tables\Actions\Action::make('mark_completed')
                        ->label('Complete')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'processing']))
                        ->action(function (Order $record) {
                            $record->update(['status' => 'completed']);
                            $this->dispatch('$refresh');
                        })
                        ->requiresConfirmation(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->button(),
            ])
            ->contentGrid([
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
            ])
            ->emptyStateHeading('No recent orders')
            ->emptyStateDescription('New orders will appear here when customers place them.')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->paginated(false);
    }
}