<?php

// ========================================
// IMPROVED PRODUCTRESOURCE.PHP
// ========================================

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Canteen Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Product Information')
                ->description('Add your product details and pricing')
                ->schema([
                    Forms\Components\Hidden::make('stall_id')
                        ->default(fn () => Auth::user()->admin_stall_id),
                        
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Chicken Adobo Rice')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                            if (empty($state)) return;
                            
                            // Auto-suggest category based on product name
                            $state = strtolower($state);
                            
                            // Define keyword-to-category mappings
                            $categoryMap = [
                                'main_course' => ['rice', 'chicken', 'pork', 'beef', 'fish', 'meal', 'viand', 'ulam'],
                                'dessert' => ['cake', 'ice cream', 'dessert', 'sweet', 'halo-halo', 'leche flan'],
                                'beverage' => ['coffee', 'tea', 'juice', 'shake', 'smoothie', 'drink', 'soda'],
                                'snack' => ['chips', 'fries', 'nachos', 'sandwich', 'burger', 'pizza'],
                                'appetizer' => ['salad', 'soup', 'appetizer', 'starter']
                            ];
                            
                            // Check for keyword matches
                            foreach ($categoryMap as $category => $keywords) {
                                foreach ($keywords as $keyword) {
                                    if (str_contains($state, $keyword)) {
                                        // Find the category ID by name
                                        $categoryId = \App\Models\Category::where('name', $category)->first()?->id;
                                        if ($categoryId) {
                                            $set('category_id', $categoryId);
                                        }
                                        return; // Stop at first match
                                    }
                                }
                            }
                        }),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->prefix('₱')
                                ->step(0.01)
                                ->minValue(1)
                                ->maxValue(9999.99)
                                ->placeholder('0.00')
                                ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                    if (is_numeric($state) && $state > 100) {
                                        $component->state($state / 100);
                                    }
                                })
                                ->dehydrateStateUsing(function ($state) {
                                    return $state ? (int) ($state * 100) : 0;
                                }),

                            Forms\Components\TextInput::make('preparation_time')
                                ->label('Prep Time (minutes)')
                                ->numeric()
                                ->suffix('mins')
                                ->default(15)
                                ->minValue(1)
                                ->maxValue(120)
                                ->helperText('How long to prepare this item?'),
                        ]),

                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\Textarea::make('description')->rows(2),
                            Forms\Components\Toggle::make('is_active')->default(true),
                            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                        ])
                        ->createOptionModalHeading('Create New Category'),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->maxLength(500)
                        ->rows(3)
                        ->placeholder('Describe your product ingredients, taste, or special features...')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Product Image')
                ->description('Upload an attractive photo of your product')
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->directory('products')
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(2048)  // 2MB limit
                        ->acceptedFileTypes(['image/jpeg', 'image/png'])
                        ->helperText('Max 2MB. JPEG or PNG format.')
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(fn ($record) => $record && $record->image),

            Forms\Components\Section::make('Availability Settings')
                ->description('Control when this product is available')
                ->schema([
                    Forms\Components\Toggle::make('is_available')
                        ->label('Available Now')
                        ->helperText('Customers can order this product')
                        ->default(true),
                        
                    Forms\Components\Toggle::make('is_published')
                        ->label('Published')
                        ->helperText('Show this product to customers')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl(asset('images/default-product.png')),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'appetizer' => 'success',
                        'main_course', 'main course' => 'primary',
                        'dessert' => 'warning',
                        'beverage' => 'info',
                        'snack' => 'gray',
                        'combo' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (?string $state) => $state ? str_replace('_', ' ', ucwords($state, '_')) : 'Uncategorized'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->getStateUsing(fn ($record) => '₱' . number_format($record->price / 100, 2))
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('preparation_time')
                    ->label('Prep Time')
                    ->suffix(' mins')
                    ->alignCenter()
                    ->color('gray'),

                Tables\Columns\ViewColumn::make('availability_status')
                    ->label('Status')
                    ->view('components.product-status'),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Orders')
                    ->getStateUsing(fn ($record) => $record->orderItems()->count())
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator('Category'),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->placeholder('All products')
                    ->trueLabel('Available only')
                    ->falseLabel('Unavailable only'),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price_from')
                                    ->label('Min Price')
                                    ->numeric()
                                    ->prefix('₱'),
                                Forms\Components\TextInput::make('price_to')
                                    ->label('Max Price')
                                    ->numeric()
                                    ->prefix('₱'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['price_from'], fn ($q, $price) => 
                                $q->where('price', '>=', $price * 100))
                            ->when($data['price_to'], fn ($q, $price) => 
                                $q->where('price', '<=', $price * 100));
                    })
                    ->indicator('Price Range'),

                Tables\Filters\Filter::make('popular')
                    ->label('Popular Items')
                    ->query(fn (Builder $query) => 
                        $query->whereHas('orderItems', fn ($q) => 
                            $q->selectRaw('COUNT(*) as order_count')
                              ->groupBy('product_id')
                              ->havingRaw('COUNT(*) >= 10')
                        )
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('quick_toggle')
                        ->label(fn (Product $record) => $record->is_available ? 'Mark Unavailable' : 'Mark Available')
                        ->icon(fn (Product $record) => $record->is_available ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn (Product $record) => $record->is_available ? 'warning' : 'success')
                        ->action(function (Product $record) {
                            $record->update(['is_available' => !$record->is_available]);
                            $status = $record->is_available ? 'available' : 'unavailable';
                            Notification::make()->title("Product marked as {$status}")->success()->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->action(function (Product $record) {
                            $new = $record->replicate();
                            $new->name = $record->name . ' (Copy)';
                            $new->save();
                            Notification::make()->title('Product duplicated successfully')->success()->send();
                        }),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('Mark Available')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_available' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('mark_unavailable')
                        ->label('Mark Unavailable')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_available' => false]))
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('update_category')
                        ->label('Change Category')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('category_id')
                                ->label('New Category')
                                ->relationship('category', 'name')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['category_id' => $data['category_id']]);
                            Notification::make()->title('Categories updated successfully')->success()->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Start by adding your first product to your stall menu.')
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Add First Product')
                    ->icon('heroicon-o-plus')
                    ->url(static::getUrl('create')),
                ])
            ->striped()
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $stallId = Auth::user()->admin_stall_id;
        if (!$stallId) return null;

        $total = static::getModel()::where('stall_id', $stallId)->count();
        $available = static::getModel()::where('stall_id', $stallId)->where('is_available', true)->count();
        
        return $total > 0 ? "{$available}/{$total}" : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $stallId = Auth::user()->admin_stall_id;
        if (!$stallId) return null;

        $total = static::getModel()::where('stall_id', $stallId)->count();
        $available = static::getModel()::where('stall_id', $stallId)->where('is_available', true)->count();
        
        if ($total === 0) return 'gray';
        $ratio = $available / $total;
        
        return match(true) {
            $ratio >= 0.8 => 'success',
            $ratio >= 0.5 => 'warning',
            default => 'danger',
        };
    }

    public static function getPages(): array
{
    return [
        'index' => Pages\ListProducts::route('/'),
        'create' => Pages\CreateProduct::route('/create'),
        'view' => Pages\ViewProduct::route('/{record}'),
        'edit' => Pages\EditProduct::route('/{record}/edit'),
    ];
}
}