<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tenant\Resources\TenantProductResource\Pages;

class TenantProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Products';
    protected static ?string $navigationLabel = 'My Products';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        if (!$stall) {
            // Return empty query if no stall assigned
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('stall_id', $stall->id)
            ->with(['category']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->description('Add details about your product')
                    ->schema([
                        Forms\Components\Hidden::make('stall_id')
                            ->default(function () {
                                return Auth::user()?->assignedStall?->id;
                            }),

                        Forms\Components\Hidden::make('created_by')
                            ->default(Auth::id()),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Chicken Adobo Rice')
                            ->live(onBlur: true),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(500),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->createOptionModalHeading('Create New Category'),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('Describe your product, ingredients, and what makes it special...')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->step(0.01)
                            ->minValue(0.01)
                            ->maxValue(9999.99)
                            ->placeholder('0.00')
                            ->helperText('Price stored in cents internally')
                            ->dehydrateStateUsing(function ($state) {
                                // Convert to cents for storage
                                return $state ? (int) ($state * 100) : 0;
                            })
                            ->formatStateUsing(function ($state) {
                                // Convert from cents for display
                                return $state ? $state / 100 : 0;
                            }),

                        Forms\Components\TextInput::make('preparation_time')
                            ->label('Prep Time (minutes)')
                            ->numeric()
                            ->suffix('mins')
                            ->default(15)
                            ->minValue(1)
                            ->maxValue(120)
                            ->helperText('How long to prepare this item?'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Product Image')
                    ->description('Upload an image of your product')
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
            ->collapsible(),

                Forms\Components\Section::make('Availability Settings')
                    ->description('Control product availability')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Available for Sale')
                            ->helperText('Customers can order this product')
                            ->default(true),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Published on Menu')
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
                    ->disk('public')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl(function () {
                        return url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNCIgZmlsbD0iI0YzRjRGNiIvPgo8cGF0aCBkPSJNMTIgMTZIMjhWMjRIMTJWMTZaIiBmaWxsPSIjOUNBM0FGIi8+CjxwYXRoIGQ9Ik0xNiAxMkgyNFYyOEgxNlYxMloiIGZpbGw9IiM2QjdGODAiLz4KPC9zdmc+');
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(30),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'appetizer' => 'success',
                        'main course', 'main_course' => 'primary',
                        'dessert' => 'warning',
                        'beverage' => 'info',
                        'snack' => 'gray',
                        'combo' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (?string $state) => $state ? str_replace('_', ' ', ucwords($state, '_')) : 'Uncategorized'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->getStateUsing(fn ($record) => 'PHP ' . number_format($record->price / 100, 2))
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('preparation_time')
                    ->label('Prep Time')
                    ->suffix(' mins')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->placeholder('All products')
                    ->trueLabel('Available only')
                    ->falseLabel('Unavailable only'),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published Status')
                    ->placeholder('All products')
                    ->trueLabel('Published only')
                    ->falseLabel('Unpublished only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('toggle_availability')
                        ->label(fn ($record) => $record->is_available ? 'Mark Unavailable' : 'Mark Available')
                        ->icon(fn ($record) => $record->is_available ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn ($record) => $record->is_available ? 'warning' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_available' => !$record->is_available]);
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('toggle_published')
                        ->label(fn ($record) => $record->is_published ? 'Unpublish' : 'Publish')
                        ->icon(fn ($record) => $record->is_published ? 'heroicon-o-archive-box-x-mark' : 'heroicon-o-archive-box')
                        ->color(fn ($record) => $record->is_published ? 'warning' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_published' => !$record->is_published]);
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical'),
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

                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-archive-box')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Unpublish')
                        ->icon('heroicon-o-archive-box-x-mark')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Start by adding your first product to your stall menu.')
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add First Product')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenantProducts::route('/'),
            'create' => Pages\CreateTenantProduct::route('/create'),
            'view' => Pages\ViewTenantProduct::route('/{record}'),
            'edit' => Pages\EditTenantProduct::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return !is_null($user?->assignedStall);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $stall = $user?->assignedStall;
        
        if (!$stall) {
            return null;
        }

        $total = static::getModel()::where('stall_id', $stall->id)->count();
        $available = static::getModel()::where('stall_id', $stall->id)
            ->where('is_available', true)
            ->count();
        
        return $total > 0 ? "{$available}/{$total}" : null;
    }
    public static function canViewAny(): bool
    {
    return Auth::user()?->assignedStall !== null;
}

public static function canView($record): bool
{
    $stallId = Auth::user()?->assignedStall?->id;
    return $stallId && $record->stall_id === $stallId;
}

public static function canEdit($record): bool
{
    $stallId = Auth::user()?->assignedStall?->id;
    return $stallId && $record->stall_id === $stallId;
}

public static function canDelete($record): bool
{
    $stallId = Auth::user()?->assignedStall?->id;
    return $stallId && $record->stall_id === $stallId;
}
}