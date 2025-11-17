<?php
// app/Filament/Cashier/Resources/CashierOrderResource.php

namespace App\Filament\Cashier\Resources;

use App\Models\Order;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Cashier\Resources\CashierOrderResource\Pages;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;

class CashierOrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $navigationGroup = '🎯 Operations';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'order_number';

    // Real-time updates
    protected static ?string $pollingInterval = '10s';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Status')
                    ->description('Update order progress and customer information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->disabled()
                                    ->prefixIcon('heroicon-m-hashtag'),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => '⏳ Pending',
                                        'processing' => '🔥 Processing', 
                                        'ready' => '✅ Ready',
                                        'completed' => '🏁 Completed',
                                        'cancelled' => '❌ Cancelled',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $record) {
                                        if ($record) {
                                            Notification::make()
                                                ->title('Order Status Updated')
                                                ->body("Order #{$record->order_number} is now {$state}")
                                                ->success()
                                                ->send();
                                        }
                                    }),

                                Forms\Components\TextInput::make('estimated_completion')
                                    ->label('Est. Ready (minutes)')
                                    ->numeric()
                                    ->suffix('min')
                                    ->placeholder('15')
                                    ->helperText('How long until ready?'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('customer_display')
                                    ->label('Customer')
                                    ->disabled()
                                    ->formatStateUsing(function ($record) {
                                        if ($record?->user) {
                                            return $record->user->name;
                                        }
                                        return $record?->customer_name ?: 'Walk-in Customer';
                                    }),

                                Forms\Components\TextInput::make('total_amount')
    ->label('Total Amount')
    ->disabled()
    ->prefix('₱')
    ->formatStateUsing(fn ($state) => number_format(($state ?? 0) / 100, 2)),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('Order Notes')
                            ->placeholder('Add any special notes or instructions...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Order Items')
                    ->description('Items in this order')
                    ->schema([
                        Forms\Components\Placeholder::make('order_items_display')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record || !$record->items) {
                                    return 'No items found';
                                }

                                $itemsHtml = '<div class="space-y-2">';
                                foreach ($record->items as $item) {
                                    $itemsHtml .= '
    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
        <div style="flex: 1;">
            <div class="text-sm text-gray-500">' . $item->quantity . '× @ ₱' . number_format(($item->unit_price ?? 0) / 100, 2) . '</div>
            <span class="font-medium">' . ($item->product->name ?? $item->product_name) . '</span>
        </div>
        <div class="text-right">
            <div class="font-semibold">₱' . number_format(($item->subtotal ?? 0) / 100, 2) . '</div>
            <div class="text-xs text-gray-500">₱' . number_format(($item->unit_price ?? 0) / 100, 2) . ' each</div>
        </div>
    </div>';
                                }
                                $itemsHtml .= '</div>';

                                return new \Illuminate\Support\HtmlString($itemsHtml);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Order number with status indicator
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->icon('heroicon-m-hashtag')
                    ->description(fn (Order $record): string => 
                        'Created ' . $record->created_at->diffForHumans()
                    ),

                // Customer info
                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Customer')
                    ->getStateUsing(function (Order $record): string {
                        if ($record->user) {
                            return $record->user->name;
                        }
                        return $record->customer_name ?: 'Walk-in Customer';
                    })
                    ->searchable(['customer_name'])
                    ->icon('heroicon-m-user')
                    ->description(fn (Order $record): ?string => $record->customer_phone),

                // Order items preview
                Tables\Columns\TextColumn::make('items_preview')
                    ->label('Items')
                    ->getStateUsing(function (Order $record): string {
                        $items = $record->items()->with('product')->get();
                        if ($items->isEmpty()) {
                            return 'No items';
                        }
                        
                        $preview = $items->map(function ($item) {
                            return $item->quantity . '× ' . ($item->product->name ?? $item->product_name);
                        })->take(2)->join(', ');
                        
                        if ($items->count() > 2) {
                            $preview .= ' +' . ($items->count() - 2) . ' more';
                        }
                        
                        return $preview;
                    })
                    ->wrap()
                    ->icon('heroicon-m-shopping-bag')
                    ->tooltip(function (Order $record): string {
                        return $record->items()->with('product')->get()->map(function ($item) {
                            return $item->quantity . '× ' . ($item->product->name ?? $item->product_name) 
                                . ' (₱' . number_format($item->subtotal, 2) . ')';
                        })->join("\n");
                    }),

                // Total amount
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('PHP')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                // Status with enhanced styling
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'ready' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-m-clock',
                        'processing' => 'heroicon-m-cog-6-tooth',
                        'ready' => 'heroicon-m-check-circle',
                        'completed' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => '⏳ Pending',
                        'processing' => '🔥 Processing',
                        'ready' => '✅ Ready',
                        'completed' => '🏁 Completed',
                        'cancelled' => '❌ Cancelled',
                        default => ucfirst($state),
                    }),

                // Order type and payment method
                Tables\Columns\TextColumn::make('order_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'primary',
                        'onsite' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'online' => '🛒 Takeout',
                        'onsite' => '🍽️ Dine-in',
                        default => '❓ Unknown',
                    }),

                // Time since order
                Tables\Columns\TextColumn::make('order_age')
                    ->label('Age')
                    ->getStateUsing(fn (Order $record): string => 
                        $record->created_at->diffForHumans(null, true, false, 1)
                    )
                    ->color(fn (Order $record): string => 
                        $record->created_at->diffInMinutes() > 30 ? 'danger' : 
                        ($record->created_at->diffInMinutes() > 15 ? 'warning' : 'gray')
                    )
                    ->icon('heroicon-m-clock')
                    ->sortable('created_at'),

                // Estimated completion time
                Tables\Columns\TextColumn::make('estimated_completion')
                    ->label('Est. Ready')
                    ->suffix(' min')
                    ->placeholder('Not set')
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => '⏳ Pending',
                        'processing' => '🔥 Processing', 
                        'ready' => '✅ Ready',
                        'completed' => '🏁 Completed',
                        'cancelled' => '❌ Cancelled',
                    ])
                    ->default('pending'),

                Tables\Filters\SelectFilter::make('order_type')
                    ->options([
                        'online' => '🛒 Takeout',
                        'onsite' => '🍽️ Dine-in',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => '💵 Cash',
                        'gcash' => '📱 GCash',
                        'paymaya' => '💳 PayMaya',
                        'card' => '💳 Card',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('📅 Today\'s Orders')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('urgent')
                    ->label('🚨 Urgent (>15 min)')
                    ->query(fn (Builder $query) => 
                        $query->whereIn('status', ['pending', 'processing'])
                              ->where('created_at', '<', now()->subMinutes(15))
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('👁️ View Details'),

                    Tables\Actions\EditAction::make()
                        ->label('✏️ Edit Status'),

                    Tables\Actions\Action::make('start_processing')
                        ->label('🔥 Start Processing')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Start Processing Order')
                        ->modalDescription('Mark this order as being prepared?')
                        ->action(function (Order $record) {
                            $record->update(['status' => 'processing']);
                            
                            Notification::make()
                                ->title('Order Processing Started')
                                ->body("Order #{$record->order_number} is now being prepared")
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Order $record): bool => $record->status === 'pending'),

                    Tables\Actions\Action::make('mark_ready')
                        ->label('✅ Mark Ready')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Mark Order Ready')
                        ->modalDescription('Is this order ready for pickup/serving?')
                        ->action(function (Order $record) {
                            $record->update(['status' => 'ready']);
                            
                            Notification::make()
                                ->title('Order Ready!')
                                ->body("Order #{$record->order_number} is ready for customer")
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Order $record): bool => $record->status === 'processing'),

                    Tables\Actions\Action::make('complete_order')
                        ->label('🏁 Complete')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Complete Order')
                        ->modalDescription('Mark this order as completed and served?')
                        ->action(function (Order $record) {
                            $record->update(['status' => 'completed']);
                            
                            Notification::make()
                                ->title('Order Completed!')
                                ->body("Order #{$record->order_number} has been completed")
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Order $record): bool => $record->status === 'ready'),

                    Tables\Actions\Action::make('print_receipt')
                        ->label('🖨️ Print Receipt')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->action(function (Order $record) {
                            // Add your print logic here
                            Notification::make()
                                ->title('Receipt Printed')
                                ->body("Receipt for order #{$record->order_number}")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('cancel_order')
                        ->label('❌ Cancel')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Order')
                        ->modalDescription('Are you sure you want to cancel this order?')
                        ->form([
                            Forms\Components\Textarea::make('cancellation_reason')
                                ->label('Reason for cancellation')
                                ->required()
                                ->placeholder('Please provide a reason for cancellation...')
                                ->rows(3)
                        ])
                        ->action(function (Order $record, array $data) {
                            $record->update([
                                'status' => 'cancelled',
                                'notes' => ($record->notes ? $record->notes . "\n\n" : '') 
                                    . 'Cancelled by cashier: ' . $data['cancellation_reason']
                            ]);
                            
                            Notification::make()
                                ->title('Order Cancelled')
                                ->body("Order #{$record->order_number} has been cancelled")
                                ->warning()
                                ->send();
                        })
                        ->visible(fn (Order $record): bool => 
                            in_array($record->status, ['pending', 'processing', 'ready'])
                        ),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_start_processing')
                        ->label('🔥 Start Processing All')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update(['status' => 'processing']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Action Completed')
                                ->body("Started processing {$count} orders")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('bulk_mark_ready')
                        ->label('✅ Mark All Ready')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'processing') {
                                    $record->update(['status' => 'ready']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Action Completed')
                                ->body("Marked {$count} orders as ready")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('bulk_complete')
                        ->label('🏁 Complete All')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'ready') {
                                    $record->update(['status' => 'completed']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Action Completed')
                                ->body("Completed {$count} orders")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s') // Real-time updates
            ->emptyStateHeading('🎉 No orders to manage')
            ->emptyStateDescription('All orders are completed or no new orders yet.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->persistFiltersInSession();
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['pending', 'processing', 'ready'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $urgentCount = static::getModel()::whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<', now()->subMinutes(15))
            ->count();
        
        return $urgentCount > 0 ? 'danger' : 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashierOrders::route('/'),
            'view' => Pages\ViewCashierOrder::route('/{record}'),
            'edit' => Pages\EditCashierOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Orders are created through POS or customer orders
    }

    
}