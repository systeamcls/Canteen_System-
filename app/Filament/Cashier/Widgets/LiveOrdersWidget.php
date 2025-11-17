<?php
// Fixed LiveOrdersWidget.php - REPLACE YOUR FILE

namespace App\Filament\Cashier\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Enums\MaxWidth;

class LiveOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '3s';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $adminStallId = $this->getAdminStallId();
        
        return $table
            ->query(
                Order::query()
                    ->with(['items', 'items.product', 'user'])
                    ->whereHas('items.product', function ($query) use ($adminStallId) {
                        $query->where('stall_id', $adminStallId);
                    })
                    ->whereDate('created_at', today())
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest('created_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->default('Walk-in')
                    ->searchable()
                    ->limit(15),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                    
                Tables\Columns\TextColumn::make('order_type')
                    ->badge()
                    ->colors([
                        'success' => 'onsite',
                        'primary' => 'online',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('admin_items')
                    ->label('Your Items')
                    ->formatStateUsing(function ($record) use ($adminStallId) {
                        $adminItems = $record->items->filter(function ($item) use ($adminStallId) {
                            return $item->product && $item->product->stall_id == $adminStallId;
                        });
                        
                        $totalQty = $adminItems->sum('quantity');
                        $itemNames = $adminItems->pluck('product_name')->take(2)->implode(', ');
                        $moreCount = $adminItems->count() - 2;
                        
                        $result = "{$totalQty} items";
                        if ($itemNames) {
                            $result .= " ({$itemNames}";
                            if ($moreCount > 0) {
                                $result .= " +{$moreCount} more";
                            }
                            $result .= ")";
                        }
                        return $result;
                    })
                    ->limit(40),
            ])
            ->actions([
                Action::make('view_details')
    ->label('Details')
    ->icon('heroicon-m-eye')
    ->color('gray')
    ->size('sm')
    ->modalHeading(fn ($record) => "Order Details - {$record->order_number}")
    ->modalWidth(MaxWidth::FourExtraLarge)
    ->fillForm(function ($record) {
        // Load the order items eagerly
        if (!$record) {
            return [];
        }
        
        $orderItems = $record->items()->with('product')->get();
        
        // Return form data
        return [
            'customer_name' => $record->customer_name,
            'customer_phone' => $record->customer_phone,
            'customer_email' => $record->customer_email,
            'order_number' => $record->order_number,
            'status' => $record->status,
            'order_type' => $record->order_type,
            'payment_method' => $record->payment_method,
            'total_amount' => $record->total_amount,
            'created_at' => $record->created_at,
            'items_list' => $orderItems,
        ];
    })
    ->form([
        Grid::make(2)
            ->schema([
                Section::make('Customer Information')
                    ->schema([
                        Placeholder::make('customer_name')
                            ->label('Customer Name')
                            ->content(fn ($state) => $state ?: 'Walk-in Customer'),
                            
                        Placeholder::make('customer_phone')
                            ->label('Phone')
                            ->content(fn ($state) => $state ?: 'Not provided'),
                            
                        Placeholder::make('customer_email')
                            ->label('Email')
                            ->content(fn ($state) => $state ?: 'Not provided'),
                    ]),
                    
                Section::make('Order Information')
                    ->schema([
                        Placeholder::make('order_number')
                            ->label('Order Number')
                            ->content(fn ($state) => $state),
                            
                        Placeholder::make('status')
                            ->label('Status')
                            ->content(fn ($state) => ucfirst($state)),
                            
                        Placeholder::make('order_type')
                            ->label('Order Type')
                            ->content(fn ($state) => ucfirst($state)),
                            
                        Placeholder::make('payment_method')
                            ->label('Payment Method')
                            ->content(fn ($state) => ucfirst($state ?? 'Not set')),
                            
                        Placeholder::make('total_amount')
                            ->label('Total Amount')
                            ->content(fn ($state) => '₱' . number_format($state, 2)),
                            
                        Placeholder::make('created_at')
                            ->label('Order Date')
                            ->content(fn ($state) => $state ? $state->format('M d, Y h:i A') : ''),
                    ]),
            ]),
            
        Section::make('Order Items')
            ->schema([
                Placeholder::make('items_list')
    ->label('')
    ->content(function ($state) {
        $orderItems = $state;
        
        if (!$orderItems || $orderItems->isEmpty()) {
            return 'No items found';
        }
        
        $html = '<div class="space-y-2">';
        foreach ($orderItems as $item) {
            $productName = $item->product_name ?? $item->product?->name ?? 'Unknown Product';
            
            // Convert centavos to pesos by dividing by 100
            $unitPrice = $item->unit_price / 100;
            $subtotal = $item->subtotal / 100;
            
            $html .= '
                <div class="grid grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Product</div>
                        <div class="font-medium">' . htmlspecialchars($productName) . '</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Quantity</div>
                        <div class="font-medium">' . $item->quantity . '</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Unit Price</div>
                        <div class="font-medium">₱' . number_format($unitPrice, 2) . '</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Subtotal</div>
                        <div class="font-semibold text-green-600 dark:text-green-400">₱' . number_format($subtotal, 2) . '</div>
                    </div>
                </div>
            ';
        }
        $html .= '</div>';
        
        return new \Illuminate\Support\HtmlString($html);
    }),
            ]),
    ])
    ->modalActions(function ($record) {
        return [
            Action::make('start_processing')
                ->label('Start Processing')
                ->icon('heroicon-m-play')
                ->color('primary')
                ->visible($record->status === 'pending')
                ->action(function () use ($record) {
                    $record->update(['status' => 'processing']);
                    $this->dispatch('$refresh');
                }),
                
            Action::make('complete')
                ->label('Mark Completed')
                ->icon('heroicon-m-check')
                ->color('success')
                ->visible($record->status === 'processing')
                ->action(function () use ($record) {
                    $record->update(['status' => 'completed']);
                    $this->dispatch('$refresh');
                }),
                
            Action::make('close')
                ->label('Close')
                ->color('gray')
                ->close(),
        ];
    }),
                    
                Action::make('start_processing')
                    ->label('Start')
                    ->icon('heroicon-m-play')
                    ->color('primary')
                    ->size('sm')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'processing']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Order Started')
                            ->body("Order #{$record->order_number} is now being processed")
                            ->success()
                            ->send();
                    }),
                    
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->size('sm')
                    ->visible(fn ($record) => $record->status === 'processing')
                    ->requiresConfirmation()
                    ->modalHeading('Complete Order')
                    ->modalDescription('Mark this order as completed and ready for customer?')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'completed',
                            'payment_status' => 'paid'
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Order Completed! 🎉')
                            ->body("Order #{$record->order_number} is ready for pickup")
                            ->success()
                            ->duration(5000)
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('mark_processing')
                    ->label('Start Processing')
                    ->icon('heroicon-m-play')
                    ->color('primary')
                    ->action(function (Collection $records) {
                        $count = $records->where('status', 'pending')->count();
                        
                        $records->where('status', 'pending')->each(function ($record) {
                            $record->update(['status' => 'processing']);
                        });
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Bulk Action Completed')
                            ->body("{$count} orders marked as processing")
                            ->success()
                            ->send();
                    }),
                    
                BulkAction::make('mark_completed')
                    ->label('Mark Completed')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = $records->whereIn('status', ['pending', 'processing'])->count();
                        
                        $records->whereIn('status', ['pending', 'processing'])->each(function ($record) {
                            $record->update([
                                'status' => 'completed',
                                'payment_status' => 'paid'
                            ]);
                        });
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Bulk Action Completed')
                            ->body("{$count} orders marked as completed")
                            ->success()
                            ->send();
                    }),
            ])
            ->heading('🔴 Live Orders - Today Only')
            ->description('Real-time updates every 3 seconds • Today\'s orders containing your products')
            ->emptyStateHeading('No pending orders today')
            ->emptyStateDescription('All caught up! Great work! 🎉')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultSort('created_at', 'desc')
            ->poll('3s');
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
}