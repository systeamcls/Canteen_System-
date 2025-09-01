<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tenant\Resources\TenantOrderResource\Pages;
use Filament\Notifications\Notification;

class TenantOrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Orders & Sales';
    protected static ?string $navigationLabel = 'My Orders';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        if (!$stall) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->whereHas('items.product', function (Builder $query) use ($stall) {
                $query->where('stall_id', $stall->id);
            })
            ->with(['user', 'items.product']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->helperText('Update order status to notify customer')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('customer_display')
                            ->label('Customer')
                            ->disabled()
                            ->formatStateUsing(function ($record) {
                                if ($record?->user) {
                                    return $record->user->name;
                                }
                                if ($record?->customer_name) {
                                    return $record->customer_name;
                                }
                                if ($record?->guest_details) {
                                    $details = is_string($record->guest_details) 
                                        ? json_decode($record->guest_details, true) 
                                        : $record->guest_details;
                                    if (is_array($details) && isset($details['name'])) {
                                        return $details['name'];
                                    }
                                }
                                return 'Guest Customer';
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('order_type')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => ucfirst($state ?? 'Unknown'))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total_amount')
                            ->disabled()
                            ->prefix('PHP ')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('estimated_completion')
                            ->label('Est. Completion (minutes)')
                            ->numeric()
                            ->suffix('min')
                            ->helperText('How long until ready?')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('special_instructions')
                            ->label('Customer Instructions')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Internal Notes')
                            ->placeholder('Add notes about this order...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Order Items from Your Stall')
                    ->schema([
                        Forms\Components\Repeater::make('stall_items')
                            ->label('')
                            ->relationship(false)
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->disabled()
                                    ->label('Product'),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->prefix('PHP ')
                                    ->disabled()
                                    ->label('Unit Price')
                                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2)),
                                Forms\Components\TextInput::make('subtotal')
                                    ->prefix('PHP ')
                                    ->disabled()
                                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2)),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->default(function ($record) {
                                if (!$record) return [];
                                
                                $user = Auth::user();
                                $stall = $user->assignedStall;
                                
                                if (!$stall) return [];
                                
                                return $record->items()
                                    ->whereHas('product', function ($query) use ($stall) {
                                        $query->where('stall_id', $stall->id);
                                    })
                                    ->with('product')
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            'product_name' => $item->product_name,
                                            'quantity' => $item->quantity,
                                            'unit_price' => $item->unit_price,
                                            'subtotal' => $item->subtotal,
                                        ];
                                    })
                                    ->toArray();
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->copyable()
                    ->weight('medium')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('customer_display')
                    ->label('Customer')
                    ->getStateUsing(function (Order $record): string {
                        if ($record->user) {
                            return $record->user->name;
                        }
                        if ($record->customer_name) {
                            return $record->customer_name;
                        }
                        if ($record->guest_details) {
                            $details = is_string($record->guest_details) 
                                ? json_decode($record->guest_details, true) 
                                : $record->guest_details;
                            if (is_array($details) && isset($details['name'])) {
                                return $details['name'];
                            }
                        }
                        return 'Guest Customer';
                    })
                    ->searchable(['customer_name'])
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('stall_items_preview')
                    ->label('My Items')
                    ->getStateUsing(function (Order $record): string {
                        $user = Auth::user();
                        $stall = $user->assignedStall;
                        
                        if (!$stall) return 'No stall assigned';

                        $stallItems = $record->items()
                            ->whereHas('product', function ($query) use ($stall) {
                                $query->where('stall_id', $stall->id);
                            })
                            ->with('product')
                            ->get();

                        if ($stallItems->isEmpty()) {
                            return 'No items from your stall';
                        }

                        return $stallItems->map(function ($item) {
                            return $item->quantity . '× ' . $item->product_name;
                        })->take(2)->join(', ') . ($stallItems->count() > 2 ? '...' : '');
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('stall_revenue')
                    ->label('My Revenue')
                    ->getStateUsing(function (Order $record): float {
                        $user = Auth::user();
                        $stall = $user->assignedStall;
                        
                        if (!$stall) return 0;

                        return $record->items()
                            ->whereHas('product', function ($query) use ($stall) {
                                $query->where('stall_id', $stall->id);
                            })
                            ->sum('subtotal');
                    })
                    ->money('PHP')
                    ->weight('semibold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Order Total')
                    ->money('PHP')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('order_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'primary',
                        'onsite' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Time')
                    ->dateTime('M d, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimated_completion')
                    ->label('Est. Ready')
                    ->suffix(' min')
                    ->placeholder('Not set'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('order_type')
                    ->options([
                        'online' => 'Online',
                        'onsite' => 'Walk-in',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('Today\'s Orders')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('active_orders')
                    ->label('Active Orders')
                    ->query(fn (Builder $query) => 
                        $query->whereIn('status', ['pending', 'processing'])
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('start_processing')
                        ->label('Start Processing')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->visible(fn ($record) => $record->status === 'pending')
                        ->action(function ($record) {
                            $record->status = 'processing';
                            $record->save();
                            
                            Notification::make()
                                ->title('Order processing started')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('mark_completed')
                        ->label('Mark Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'processing']))
                        ->action(function ($record) {
                            $record->status = 'completed';
                            $record->save();
                            
                            Notification::make()
                                ->title('Order marked as completed')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('cancel_order')
                        ->label('Cancel Order')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'processing']))
                        ->form([
                            Forms\Components\Textarea::make('cancellation_reason')
                                ->label('Cancellation Reason')
                                ->required()
                                ->placeholder('Please provide a reason for cancellation...'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->status = 'cancelled';
                            $record->notes = ($record->notes ? $record->notes . "\n\n" : '') 
                                . 'Cancelled: ' . $data['cancellation_reason'];
                            $record->save();
                            
                            Notification::make()
                                ->title('Order cancelled')
                                ->warning()
                                ->send();
                        }),
                ])
                ->label('Update Status')
                ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('start_processing_bulk')
                        ->label('Start Processing')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->action(function ($records) {
                            $processed = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->status = 'processing';
                                    $record->save();
                                    $processed++;
                                }
                            }
                            
                            Notification::make()
                                ->title("{$processed} orders set to processing")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_completed_bulk')
                        ->label('Mark Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $completed = 0;
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'processing'])) {
                                    $record->status = 'completed';
                                    $record->save();
                                    $completed++;
                                }
                            }
                            
                            Notification::make()
                                ->title("{$completed} orders marked as completed")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->emptyStateHeading('No orders yet')
            ->emptyStateDescription('Orders for your stall will appear here when customers place them.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }

    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenantOrders::route('/'),
            'view' => Pages\ViewTenantOrder::route('/{record}'),
            'edit' => Pages\EditTenantOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $stall = $user?->assignedStall;
        
        if (!$stall) return null;

        $pending = static::getEloquentQuery()
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
    public static function canViewAny(): bool
    {
    return Auth::user()?->assignedStall !== null;
    }
}