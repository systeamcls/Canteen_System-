<?php
// ========================================
// FIXED ORDERRESOURCE.PHP - Main Issue Fix
// ========================================

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Canteen Management';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
{
    $user = Auth::user();
    $stallId = $user->admin_stall_id;

    return parent::getEloquentQuery()
        ->with(['user', 'items.product'])
        ->when($stallId, function (Builder $query) use ($stallId) {
            // ONLY use items-based filtering (preserves multi-vendor orders)
            $query->whereHas('items', function (Builder $itemQuery) use ($stallId) {
                $itemQuery->whereHas('product', function (Builder $productQuery) use ($stallId) {
                    $productQuery->where('stall_id', $stallId);
                });
            });
        })
        ->when(!$stallId, function (Builder $query) {
            $query->whereRaw('1 = 0');
        })
        ->latest();
}

     public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->native(false),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('customer_name')
                                    ->label('Customer Name')
                                    ->disabled()
                                    ->dehydrated(false),
                                
                                Forms\Components\TextInput::make('customer_phone')
                                    ->label('Customer Phone')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'cash' => 'Cash',
                                        'gcash' => 'GCash',
                                        'paymaya' => 'PayMaya',
                                        'card' => 'Credit/Debit Card',
                                    ])
                                    ->native(false),
                                
                                Forms\Components\Select::make('payment_status')
                                    ->required()
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                    ])
                                    ->native(false),
                                
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->prefix('₱')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('special_instructions')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('semibold')
                    ->color('primary'),

               
                Tables\Columns\TextColumn::make('customer_info')
                    ->label('Customer')
                    ->getStateUsing(function (Order $record): string {
                        if ($record->user_id && $record->user) {
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

                
                Tables\Columns\TextColumn::make('my_stall_items')
                    ->label('My Stall Items')
                    ->getStateUsing(function (Order $record): string {
                        $stallId = Auth::user()->admin_stall_id;
                        if (!$stallId) return 'No stall assigned';

                        $stallItems = $record->items()
                            ->whereHas('product', function (Builder $query) use ($stallId) {
                                $query->where('stall_id', $stallId);
                            })
                            ->with('product')
                            ->get();

                        if ($stallItems->isEmpty()) {
                            return 'No items from your stall';
                        }

                        return $stallItems->map(function ($item) {
                            return $item->quantity . '× ' . $item->product->name;
                        })->take(3)->join(', ') . ($stallItems->count() > 3 ? '...' : '');
                    })
                    ->wrap()
                    ->tooltip(function (Order $record): ?string {
                        $stallId = Auth::user()->admin_stall_id;
                        if (!$stallId) return null;

                        $stallItems = $record->items()
                            ->whereHas('product', function (Builder $query) use ($stallId) {
                                $query->where('stall_id', $stallId);
                            })
                            ->with('product')
                            ->get();

                        return $stallItems->map(function ($item) {
                            return $item->quantity . '× ' . $item->product->name . ' (₱' . number_format($item->subtotal, 2) . ')';
                        })->join("\n");
                    }),

                // ✅ FIXED: Handle both price storage formats
                Tables\Columns\TextColumn::make('my_stall_revenue')
                    ->label('My Revenue')
                    ->getStateUsing(function (Order $record): float {
                        $stallId = Auth::user()->admin_stall_id;
                        if (!$stallId) return 0;

                        return $record->items()
                            ->whereHas('product', function (Builder $query) use ($stallId) {
                                $query->where('stall_id', $stallId);
                            })
                            ->sum('subtotal');
                    })
                    ->money('PHP')
                    ->weight('semibold')
                    ->alignEnd()
                    ->color('success'),

                // ✅ FIXED: Handle both total amount fields
                Tables\Columns\TextColumn::make('order_total')
                    ->label('Total Order')
                    ->getStateUsing(function (Order $record): float {
                        // Handle both decimal and int storage
                        return $record->total_amount ?? ($record->amount_total / 100);
                    })
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd()
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

                // ✅ FIXED: Better payment method display
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'Not Set')
                    ->color(fn (?string $state): string => match ($state) {
                        'cash' => 'success',
                        'gcash' => 'info',
                        'card' => 'warning',
                        'paymaya' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing', 
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),

                // ✅ NEW: More useful filters
                Tables\Filters\SelectFilter::make('order_type')
                    ->options([
                        'online' => 'Online Order',
                        'onsite' => 'Walk-in Order',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('Today\'s Orders')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    
                    // ✅ IMPROVED: Better quick actions
                    Tables\Actions\Action::make('mark_processing')
                        ->label('Start Processing')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info')
                        ->action(fn (Order $record) => $record->update(['status' => 'processing']))
                        ->requiresConfirmation()
                        ->visible(fn (Order $record): bool => $record->status === 'pending'),

                    Tables\Actions\Action::make('mark_completed')
                        ->label('Mark Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Order $record) => $record->update(['status' => 'completed']))
                        ->requiresConfirmation()
                        ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'processing'])),

                    Tables\Actions\EditAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Order Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_processing')
                        ->label('Mark as Processing')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info')
                        ->action(fn ($records) => $records->each->update(['status' => 'processing']))
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'completed']))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Auto refresh
            ->emptyStateHeading('No orders found')
            ->emptyStateDescription('Orders from your stall will appear here.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }

    // ✅ FIXED: Debug navigation badge
    public static function getNavigationBadge(): ?string
{
    $user = Auth::user();
    $stallId = $user->admin_stall_id;
    
    if (!$stallId) return null;

    $count = static::getModel()::whereHas('items.product', function ($q) use ($stallId) {
        $q->where('stall_id', $stallId);
    })
    ->whereIn('status', ['pending', 'processing'])
    ->count();

    return $count > 0 ? (string) $count : null;
}

    // ✅ NEW: Add header actions for debugging
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}