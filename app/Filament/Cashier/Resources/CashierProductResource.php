<?php
// app/Filament/Cashier/Resources/CashierProductResource.php

namespace App\Filament\Cashier\Resources;

use App\Models\Product;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Cashier\Resources\CashierProductResource\Pages;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;

class CashierProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Products';
    protected static ?string $navigationGroup = '🎯 Operations';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    // Real-time updates for stock changes
    protected static ?string $pollingInterval = '30s';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->description('View product details and manage availability')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->disabled()
                                    ->dehydrated(false),
                                    
                                Forms\Components\TextInput::make('category.name')
                                    ->label('Category')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($record) => $record?->category?->name ?? 'No Category'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price_display')
                                    ->label('Price')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('₱')
                                    ->formatStateUsing(fn ($record) => 
                                        $record ? number_format($record->price / 100, 2) : '0.00'
                                    ),
                                    
                                Forms\Components\TextInput::make('preparation_time')
                                    ->label('Prep Time (minutes)')
                                    ->numeric()
                                    ->suffix('min')
                                    ->helperText('How long to prepare this item?')
                                    ->rules(['min:1', 'max:120']),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Stock Management')
                    ->description('Manage stock levels and alerts')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('stock_quantity')
                                    ->label('Current Stock')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->live()
                                    ->helperText('Available quantity in inventory'),
                                    
                                Forms\Components\TextInput::make('low_stock_alert')
                                    ->label('Low Stock Alert Level')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(10)
                                    ->helperText('Get notified when stock falls below this number'),
                            ]),
                    ]),

                Forms\Components\Section::make('Operational Settings')
                    ->description('Manage daily availability and operational notes')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_available')
                                    ->label('Available Today')
                                    ->helperText('Turn off if item is out of stock or unavailable')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $record) {
                                        if ($record) {
                                            $status = $state ? 'available' : 'unavailable';
                                            Notification::make()
                                                ->title('Stock Updated')
                                                ->body("{$record->name} is now {$status}")
                                                ->success()
                                                ->send();
                                        }
                                    }),
                                    
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Show on Menu')
                                    ->helperText('Hide from customer menu if needed')
                                    ->disabled() // Only admin can publish/unpublish
                                    ->dehydrated(false),
                            ]),

                        Forms\Components\Textarea::make('operational_notes')
                            ->label('Today\'s Notes')
                            ->placeholder('Add any special notes for today (e.g., "Limited quantity", "Special preparation")')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('These notes are for staff reference only'),
                    ]),

                Forms\Components\Section::make('Quick Stats')
                    ->description('Today\'s performance')
                    ->schema([
                        Forms\Components\Placeholder::make('today_stats')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record) return 'No data available';

                                // Get today's order count for this product
                                $todayOrders = $record->orderItems()
                                    ->whereHas('order', function ($query) {
                                        $query->whereDate('created_at', today())
                                              ->where('status', '!=', 'cancelled');
                                    })
                                    ->sum('quantity');

                                // Get today's revenue
                                $todayRevenue = $record->orderItems()
                                    ->whereHas('order', function ($query) {
                                        $query->whereDate('created_at', today())
                                              ->where('status', 'completed');
                                    })
                                    ->sum('subtotal');

                                return new \Illuminate\Support\HtmlString('
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                                            <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Orders Today</div>
                                            <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">' . $todayOrders . '</div>
                                        </div>
                                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                                            <div class="text-sm font-medium text-green-600 dark:text-green-400">Revenue Today</div>
                                            <div class="text-2xl font-bold text-green-900 dark:text-green-100">₱' . number_format($todayRevenue / 100, 2) . '</div>
                                        </div>
                                    </div>
                                ');
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
                // Product image
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl(url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNCIgZmlsbD0iI0YzRjRGNiIvPgo8cGF0aCBkPSJNMTIgMTZIMjhWMjRIMTJWMTZaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xNiAxMkgyNFYyOEgxNlYxMloiIGZpbGw9IiM2QjdGODAiLz4KPC9zdmc+')),

                // Product name and description
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record) => \Illuminate\Support\Str::limit($record->description, 50)),

                // Category
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'appetizer' => 'success',
                        'main course', 'main_course' => 'primary',
                        'dessert' => 'warning',
                        'beverage' => 'info',
                        'snack' => 'gray',
                        default => 'secondary',
                    }),

                // Price (read-only)
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->getStateUsing(fn ($record) => '₱' . number_format($record->price / 100, 2))
                    ->weight(FontWeight::SemiBold)
                    ->color('success'),

                // Stock Quantity Column with color-coded badges
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->sortable()
                    ->color(function ($record) {
                        if ($record->stock_quantity <= 0) return 'danger';
                        if ($record->stock_quantity <= ($record->low_stock_alert ?? 10)) return 'warning';
                        return 'success';
                    })
                    ->formatStateUsing(function ($record) {
                        if ($record->stock_quantity <= 0) {
                            return '🚫 Out of Stock';
                        } elseif ($record->stock_quantity <= ($record->low_stock_alert ?? 10)) {
                            return "⚠️ {$record->stock_quantity} (Low)";
                        } else {
                            return "✅ {$record->stock_quantity}";
                        }
                    })
                    ->icon(fn ($record) => match(true) {
                        $record->stock_quantity <= 0 => 'heroicon-m-x-circle',
                        $record->stock_quantity <= ($record->low_stock_alert ?? 10) => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-check-circle',
                    }),

                // Availability status (editable)
                Tables\Columns\ToggleColumn::make('is_available')
                    ->label('Available')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->disabled(fn ($record) => $record->stock_quantity <= 0)
                    ->afterStateUpdated(function ($record, $state) {
                        $status = $state ? 'available' : 'unavailable';
                        Notification::make()
                            ->title('Stock Updated')
                            ->body("{$record->name} is now {$status}")
                            ->success()
                            ->send();
                    }),

                // Today's stats
                Tables\Columns\TextColumn::make('today_orders')
                    ->label('Today\'s Orders')
                    ->getStateUsing(function ($record) {
                        return $record->orderItems()
                            ->whereHas('order', function ($query) {
                                $query->whereDate('created_at', today())
                                      ->where('status', '!=', 'cancelled');
                            })
                            ->sum('quantity');
                    })
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-shopping-cart'),

                // Revenue today
                Tables\Columns\TextColumn::make('today_revenue')
                    ->label('Today\'s Revenue')
                    ->getStateUsing(function ($record) {
                        $revenue = $record->orderItems()
                            ->whereHas('order', function ($query) {
                                $query->whereDate('created_at', today())
                                      ->where('status', 'completed');
                            })
                            ->sum('subtotal');
                        return '₱' . number_format($revenue / 100, 2);
                    })
                    ->color('success')
                    ->weight(FontWeight::SemiBold),

                // Prep time
                Tables\Columns\TextColumn::make('preparation_time')
                    ->label('Prep Time')
                    ->suffix(' min')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->placeholder('All items')
                    ->trueLabel('Available only')
                    ->falseLabel('Out of stock only'),

                // Stock Status Filter
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Stock Level')
                    ->options([
                        'in_stock' => '✅ In Stock',
                        'low_stock' => '⚠️ Low Stock',
                        'out_of_stock' => '🚫 Out of Stock',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['value'], function ($query, $status) {
                            return match ($status) {
                                'out_of_stock' => $query->where('stock_quantity', '<=', 0),
                                'low_stock' => $query->whereColumn('stock_quantity', '<=', 'low_stock_alert')
                                                     ->where('stock_quantity', '>', 0),
                                'in_stock' => $query->whereColumn('stock_quantity', '>', 'low_stock_alert'),
                            };
                        });
                    }),

                Tables\Filters\Filter::make('popular_today')
                    ->label('🔥 Popular Today')
                    ->query(function (Builder $query) {
                        $query->whereHas('orderItems', function ($q) {
                            $q->whereHas('order', function ($orderQuery) {
                                $orderQuery->whereDate('created_at', today())
                                          ->where('status', '!=', 'cancelled');
                            });
                        }, '>=', 5); // 5+ orders today
                    })
                    ->toggle(),

                Tables\Filters\Filter::make('out_of_stock')
                    ->label('⚠️ Out of Stock')
                    ->query(fn (Builder $query) => $query->where('is_available', false))
                    ->toggle(),
            ])
            ->actions([
                // Quick toggle availability
                Tables\Actions\Action::make('toggle_availability')
                    ->label(fn ($record) => $record->is_available ? '❌ Mark Unavailable' : '✅ Mark Available')
                    ->icon(fn ($record) => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_available ? 'danger' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_available' => !$record->is_available]);
                        $status = $record->is_available ? 'available' : 'unavailable';
                        
                        Notification::make()
                            ->title('Stock Updated')
                            ->body("{$record->name} is now {$status}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Update Stock Status')
                    ->modalDescription(fn ($record) => 
                        $record->is_available 
                            ? 'Mark this item as out of stock/unavailable?' 
                            : 'Mark this item as available for orders?'
                    ),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    // Stock Management Action
                    Tables\Actions\Action::make('manage_stock')
                        ->label('📦 Manage Stock')
                        ->icon('heroicon-o-cube')
                        ->color('primary')
                        ->form([
                            Forms\Components\Section::make('Stock Management')
                                ->description('Update stock levels for this product')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('current_stock')
                                                ->label('Current Stock')
                                                ->default(fn ($record) => $record->stock_quantity)
                                                ->disabled()
                                                ->dehydrated(false),
                                                
                                            Forms\Components\TextInput::make('low_stock_alert')
                                                ->label('Low Stock Alert Level')
                                                ->default(fn ($record) => $record->low_stock_alert ?? 10)
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(100)
                                                ->helperText('Get notified when stock falls below this number'),
                                        ]),
                                        
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\TextInput::make('add_stock')
                                                ->label('➕ Add Stock')
                                                ->numeric()
                                                ->minValue(0)
                                                ->placeholder('e.g., 50')
                                                ->helperText('Increase stock quantity'),
                                                
                                            Forms\Components\TextInput::make('remove_stock')
                                                ->label('➖ Remove Stock')
                                                ->numeric()
                                                ->minValue(0)
                                                ->placeholder('e.g., 10')
                                                ->helperText('Decrease stock quantity'),
                                                
                                            Forms\Components\TextInput::make('set_stock')
                                                ->label('🎯 Set Exact Stock')
                                                ->numeric()
                                                ->minValue(0)
                                                ->placeholder('e.g., 100')
                                                ->helperText('Override current stock'),
                                        ]),
                                ]),
                        ])
                        ->action(function ($record, $data) {
                            $originalStock = $record->stock_quantity;
                            
                            // Update low stock alert
                            if (isset($data['low_stock_alert'])) {
                                $record->update(['low_stock_alert' => $data['low_stock_alert']]);
                            }
                            
                            // Handle stock changes
                            if (!empty($data['add_stock'])) {
                                $newStock = $originalStock + $data['add_stock'];
                                $record->update(['stock_quantity' => $newStock]);
                                $action = "Added {$data['add_stock']} units";
                            } elseif (!empty($data['remove_stock'])) {
                                $newStock = max(0, $originalStock - $data['remove_stock']);
                                $record->update(['stock_quantity' => $newStock]);
                                $action = "Removed {$data['remove_stock']} units";
                            } elseif (isset($data['set_stock'])) {
                                $record->update(['stock_quantity' => $data['set_stock']]);
                                
                                // Auto-enable/disable based on stock
                                if ($data['set_stock'] > 0 && !$record->is_available) {
                                    $record->update(['is_available' => true]);
                                } elseif ($data['set_stock'] <= 0) {
                                    $record->update(['is_available' => false]);
                                }
                                
                                $action = "Set stock to {$data['set_stock']} (was {$originalStock})";
                            } else {
                                $action = "Updated stock settings";
                            }
                            
                            Notification::make()
                                ->title('Stock Updated Successfully! 📦')
                                ->body("{$record->name}: {$action}. Current stock: {$record->fresh()->stock_quantity}")
                                ->success()
                                ->duration(5000)
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('view_orders')
                        ->label('📋 View Orders')
                        ->icon('heroicon-o-list-bullet')
                        ->color('info')
                        ->url(fn ($record) => "/cashier/cashier-orders?tableFilters[product_id][value]={$record->id}"),

                    Tables\Actions\Action::make('quick_note')
                        ->label('📝 Add Note')
                        ->icon('heroicon-o-pencil')
                        ->color('gray')
                        ->form([
                            Forms\Components\Textarea::make('note')
                                ->label('Quick Note')
                                ->placeholder('Add a note about this item (e.g., "Low stock", "Special prep needed")')
                                ->required()
                        ])
                        ->action(function ($record, array $data) {
                            Notification::make()
                                ->title('Note Added')
                                ->body("Note added for {$record->name}")
                                ->success()
                                ->send();
                        }),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('✅ Mark Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_available' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_unavailable')
                        ->label('❌ Mark Unavailable')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_available' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('name')
            ->poll('30s') // Auto-refresh for stock changes
            ->emptyStateHeading('No products found')
            ->emptyStateDescription('Products will appear here once added by the administrator.')
            ->emptyStateIcon('heroicon-o-cube')
            ->striped()
            ->persistFiltersInSession();
    }

    public static function getNavigationBadge(): ?string
    {
        $outOfStock = static::getModel()::where('is_available', false)->count();
        return $outOfStock > 0 ? (string) $outOfStock : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashierProducts::route('/'),
            'view' => Pages\ViewCashierProduct::route('/{record}'),
            'edit' => Pages\EditCashierProduct::route('/{record}/edit'),
        ];
    }

    // Cashiers can't create or delete products
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
    
    public static function canEdit($record): bool
    {
        return true; // Allow cashiers to edit products
    }

    public static function canView($record): bool
    {
        return true;
    }
}