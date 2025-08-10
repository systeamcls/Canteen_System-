<?php

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

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Orders';

    public static function getEloquentQuery(): Builder
    {
        $adminStall = Auth::user()->stall;
        
        // Admin can only see orders that contain items from their stall
        return parent::getEloquentQuery()
            ->when($adminStall, function (Builder $query) use ($adminStall) {
                $query->whereHas('items.product', function (Builder $subQuery) use ($adminStall) {
                    $subQuery->where('stall_id', $adminStall->id);
                });
            })
            ->when(!$adminStall, function (Builder $query) {
                // If admin has no stall, show no orders
                $query->whereRaw('1 = 0');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'preparing' => 'Preparing',
                                'ready' => 'Ready for Pickup',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->native(false),
                        
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'gcash' => 'GCash',
                                'card' => 'Credit/Debit Card',
                                'online' => 'Online Payment',
                            ])
                            ->required()
                            ->native(false),
                        
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Guest Order'),
                        
                        Forms\Components\KeyValue::make('guest_details')
                            ->label('Guest Details')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->visible(fn (Forms\Get $get): bool => !$get('user_id')),
                        
                        Forms\Components\Textarea::make('special_instructions')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->getStateUsing(function (Order $record): string {
                        if ($record->user) {
                            return $record->user->name;
                        }
                        
                        $guestDetails = $record->guest_details;
                        return $guestDetails['name'] ?? 'Guest Customer';
                    })
                    ->searchable()
                    ->icon('heroicon-m-user'),
                
                Tables\Columns\TextColumn::make('stall_items')
                    ->label('My Stall Items')
                    ->getStateUsing(function (Order $record): string {
                        $adminStall = Auth::user()->stall;
                        
                        if (!$adminStall) {
                            return 'No stall assigned';
                        }
                        
                        $stallItems = $record->items()
                            ->whereHas('product', function (Builder $query) use ($adminStall) {
                                $query->where('stall_id', $adminStall->id);
                            })
                            ->with('product')
                            ->get()
                            ->map(function ($item) {
                                return $item->quantity . 'x ' . $item->product->name;
                            })
                            ->join(', ');
                            
                        return $stallItems ?: 'No items from your stall';
                    })
                    ->wrap()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('stall_total')
                    ->label('My Stall Revenue')
                    ->getStateUsing(function (Order $record): float {
                        $adminStall = Auth::user()->stall;
                        
                        if (!$adminStall) {
                            return 0;
                        }
                        
                        return $record->items()
                            ->whereHas('product', function (Builder $query) use ($adminStall) {
                                $query->where('stall_id', $adminStall->id);
                            })
                            ->sum('subtotal');
                    })
                    ->money('PHP')
                    ->weight('semibold')
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Order')
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd()
                    ->color('gray'),
                
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
                    }),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'gcash' => 'info',
                        'card' => 'warning',
                        'online' => 'primary',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'preparing' => 'Preparing',
                        'ready' => 'Ready for Pickup',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'gcash' => 'GCash',
                        'card' => 'Credit/Debit Card',
                        'online' => 'Online Payment',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('mark_ready')
                    ->label('Mark Ready')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->action(fn (Order $record) => $record->update(['status' => 'ready']))
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'processing', 'preparing'])),
                
                Tables\Actions\Action::make('mark_completed')
                    ->label('Complete')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->action(fn (Order $record) => $record->update(['status' => 'completed']))
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->status === 'ready'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $adminStall = Auth::user()->stall;
        
        if (!$adminStall) {
            return null;
        }
        
        return static::getModel()::whereHas('items.product', function (Builder $query) use ($adminStall) {
            $query->where('stall_id', $adminStall->id);
        })
        ->whereIn('status', ['pending', 'processing', 'preparing'])
        ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}