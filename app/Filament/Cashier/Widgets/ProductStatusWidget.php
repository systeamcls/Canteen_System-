<?php
// Enhanced ProductStatusWidget.php with Stock Management

namespace App\Filament\Cashier\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class ProductStatusWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $adminStallId = $this->getAdminStallId();
        
        return $table
            ->query(
                Product::query()
                    ->where('stall_id', $adminStallId)
                    ->where('is_published', true)
                    ->orderBy('stock_quantity', 'asc') // Show low stock first
                    ->orderBy('is_available', 'asc')
                    ->orderBy('name')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('/images/placeholder-food.png'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('PHP', divideBy: 100)
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(function ($record) {
                        if ($record->stock_quantity <= 0) return 'danger';
                        if ($record->stock_quantity <= $record->low_stock_alert) return 'warning';
                        return 'success';
                    })
                    ->formatStateUsing(function ($record) {
                        if ($record->stock_quantity <= 0) {
                            return 'Out of Stock';
                        } elseif ($record->stock_quantity <= $record->low_stock_alert) {
                            return "{$record->stock_quantity} (Low Stock)";
                        } else {
                            return $record->stock_quantity;
                        }
                    })
                    ->sortable(),
                    
                Tables\Columns\ToggleColumn::make('is_available')
                    ->label('Available')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->disabled(fn ($record) => $record->stock_quantity <= 0), // Disable toggle if out of stock
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Stock Status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'low_stock' => 'Low Stock',
                        'out_of_stock' => 'Out of Stock',
                    ])
                    ->query(function ($query, $data) {
                        return $query->when($data['value'], function ($query, $status) {
                            return match ($status) {
                                'out_of_stock' => $query->where('stock_quantity', '<=', 0),
                                'low_stock' => $query->whereRaw('stock_quantity <= low_stock_alert AND stock_quantity > 0'),
                                'in_stock' => $query->whereRaw('stock_quantity > low_stock_alert'),
                            };
                        });
                    }),
                    
                Tables\Filters\SelectFilter::make('is_available')
                    ->label('Availability')
                    ->options([
                        1 => 'Available',
                        0 => 'Unavailable',
                    ]),
                    
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Action::make('manage_stock')
                    ->label('Stock')
                    ->icon('heroicon-m-cube')
                    ->color('primary')
                    ->form([
                        Section::make('Stock Management')
                            ->description('Update stock levels and settings')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('current_stock')
                                            ->label('Current Stock')
                                            ->default(fn ($record) => $record->stock_quantity)
                                            ->disabled()
                                            ->dehydrated(false),
                                            
                                        TextInput::make('low_stock_alert')
                                            ->label('Low Stock Alert')
                                            ->default(fn ($record) => $record->low_stock_alert)
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->required(),
                                    ]),
                                    
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('add_stock')
                                            ->label('Add Stock')
                                            ->numeric()
                                            ->minValue(0)
                                            ->placeholder('Enter quantity to add'),
                                            
                                        TextInput::make('remove_stock')
                                            ->label('Remove Stock')
                                            ->numeric()
                                            ->minValue(0)
                                            ->placeholder('Enter quantity to remove'),
                                            
                                        TextInput::make('set_stock')
                                            ->label('Set Exact Stock')
                                            ->numeric()
                                            ->minValue(0)
                                            ->placeholder('Set exact quantity'),
                                    ]),
                            ]),
                    ])
                    ->action(function ($record, $data) {
                        // Update low stock alert
                        if (isset($data['low_stock_alert'])) {
                            $record->update(['low_stock_alert' => $data['low_stock_alert']]);
                        }
                        
                        // Handle stock changes
                        if (!empty($data['add_stock'])) {
                            $record->increaseStock($data['add_stock']);
                            $action = "Added {$data['add_stock']} units";
                        } elseif (!empty($data['remove_stock'])) {
                            $record->decreaseStock($data['remove_stock']);
                            $action = "Removed {$data['remove_stock']} units";
                        } elseif (isset($data['set_stock'])) {
                            $oldStock = $record->stock_quantity;
                            $record->update(['stock_quantity' => $data['set_stock']]);
                            
                            // Auto-enable/disable based on stock
                            if ($data['set_stock'] > 0 && !$record->is_available) {
                                $record->update(['is_available' => true]);
                            } elseif ($data['set_stock'] <= 0) {
                                $record->update(['is_available' => false]);
                            }
                            
                            $action = "Set stock to {$data['set_stock']} (was {$oldStock})";
                        } else {
                            $action = "Updated stock settings";
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Stock Updated')
                            ->body("{$record->name}: {$action}")
                            ->success()
                            ->send();
                            
                        $this->dispatch('$refresh');
                    }),
                    
                Action::make('quick_toggle')
                    ->label(fn ($record) => $record->is_available ? 'Disable' : 'Enable')
                    ->icon(fn ($record) => $record->is_available ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                    ->color(fn ($record) => $record->is_available ? 'danger' : 'success')
                    ->visible(fn ($record) => $record->stock_quantity > 0) // Only show if in stock
                    ->action(function ($record) {
                        $record->update(['is_available' => !$record->is_available]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Product Updated')
                            ->body($record->name . ' is now ' . ($record->is_available ? 'available' : 'unavailable'))
                            ->success()
                            ->send();
                            
                        $this->dispatch('$refresh');
                    }),
            ])
            ->heading('🍽️ Product Status - Stock Management')
            ->description('Monitor stock levels and toggle product availability')
            ->emptyStateHeading('No products found')
            ->emptyStateDescription('Add some products to get started')
            ->emptyStateIcon('heroicon-o-cube')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('30s');
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