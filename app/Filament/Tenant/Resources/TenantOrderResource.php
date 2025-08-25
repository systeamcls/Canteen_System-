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

class TenantOrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Orders & Sales';
    protected static ?string $navigationLabel = 'My Orders';
    protected static ?int $navigationSort = 1;

    // Tenant sees ONLY orders for their stall's products
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        return parent::getEloquentQuery()
            ->whereHas('items.product.stall', function (Builder $q) use ($user) {
                $q->where('tenant_id', $user->id);
            })
            ->with(['items.product', 'user']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_reference')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'preparing' => 'Preparing',
                                'ready' => 'Ready for Pickup',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->live()
                            ->helperText('Update to notify customer of order progress')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('customer_name')
                            ->disabled()
                            ->formatStateUsing(fn ($record) => 
                                $record?->user ? $record->user->name : $record?->customer_name
                            )
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('customer_phone')
                            ->disabled()
                            ->formatStateUsing(fn ($record) => 
                                $record?->user ? $record->user->phone : $record?->customer_phone
                            )
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total_amount')
                            ->disabled()
                            ->prefix('₱')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('estimated_completion')
                            ->label('Est. Completion (minutes)')
                            ->numeric()
                            ->suffix('min')
                            ->helperText('How long until ready?')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label('Order Notes')
                            ->placeholder('Add any special instructions or notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Order Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('product.name')
                                    ->disabled()
                                    ->label('Product'),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('price')
                                    ->prefix('₱')
                                    ->disabled()
                                    ->label('Unit Price'),
                                Forms\Components\TextInput::make('subtotal')
                                    ->prefix('₱')
                                    ->disabled()
                                    ->formatStateUsing(fn ($state, $record) => 
                                        number_format($record?->quantity * $record?->price, 2)
                                    ),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_reference')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Order reference copied!'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->formatStateUsing(fn ($record) => 
                        $record->user ? $record->user->name : $record->customer_name
                    )
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'primary',
                        'preparing' => 'info',
                        'ready' => 'success',
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
                    ->placeholder('Not set')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'preparing' => 'Preparing',
                        'ready' => 'Ready',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('order_type')
                    ->options([
                        'online' => 'Online',
                        'onsite' => 'Onsite',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('Today\'s Orders')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('active_orders')
                    ->label('Active Orders')
                    ->query(fn (Builder $query) => 
                        $query->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                // Quick status change actions
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('confirm')
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->visible(fn ($record) => $record->status === 'pending')
                        ->action(fn ($record) => $record->update(['status' => 'confirmed'])),

                    Tables\Actions\Action::make('start_preparing')
                        ->label('Start Prep')
                        ->icon('heroicon-o-clock')
                        ->color('info')
                        ->visible(fn ($record) => $record->status === 'confirmed')
                        ->action(fn ($record) => $record->update(['status' => 'preparing'])),

                    Tables\Actions\Action::make('mark_ready')
                        ->label('Ready')
                        ->icon('heroicon-o-bell-alert')
                        ->color('success')
                        ->visible(fn ($record) => $record->status === 'preparing')
                        ->action(fn ($record) => $record->update(['status' => 'ready'])),

                    Tables\Actions\Action::make('complete')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn ($record) => $record->status === 'ready')
                        ->action(fn ($record) => $record->update(['status' => 'completed'])),
                ])
                ->label('Update Status')
                ->color('primary')
                ->icon('heroicon-o-arrow-path'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_orders')
                        ->label('Confirm Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->action(fn ($records) => 
                            $records->each(fn ($record) => 
                                $record->status === 'pending' ? $record->update(['status' => 'confirmed']) : null
                            )
                        ),

                    Tables\Actions\BulkAction::make('start_preparing')
                        ->label('Start Preparing')
                        ->icon('heroicon-o-clock')
                        ->color('info')
                        ->action(fn ($records) => 
                            $records->each(fn ($record) => 
                                $record->status === 'confirmed' ? $record->update(['status' => 'preparing']) : null
                            )
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Auto-refresh every 30 seconds
            ->emptyStateHeading('No Orders Yet')
            ->emptyStateDescription('Orders for your stall will appear here once customers start ordering.')
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

    // Tenants cannot create orders (customers do that)
    public static function canCreate(): bool
    {
        return false;
    }

    // Tenants cannot delete orders (for record keeping)
    public static function canDelete($record): bool
    {
        return false;
    }
}