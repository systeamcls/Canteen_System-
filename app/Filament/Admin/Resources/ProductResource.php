<?php

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



class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Canteen Management';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Products';
    
    protected static ?string $modelLabel = 'Product';
    
    protected static ?string $pluralModelLabel = 'Products';


    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return parent::getEloquentQuery()->whereRaw('1 = 0'); // Return empty query
        }

        // Only show products from admin's stall
        return parent::getEloquentQuery()->where('stall_id', $stallId);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\Hidden::make('stall_id')
                            ->default(fn () => Auth::user()->admin_stall_id),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->maxValue(999999.99)
                            ->step(0.01),
                        Forms\Components\Select::make('category')
                            ->options([
                                'appetizer' => 'Appetizer',
                                'main_course' => 'Main Course',
                                'dessert' => 'Dessert',
                                'beverage' => 'Beverage',
                                'snack' => 'Snack',
                                'combo' => 'Combo Meal',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_available')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2)
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl(function () {
                        return asset('images/default-product.png');
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'appetizer' => 'success',
                        'main_course' => 'primary',
                        'dessert' => 'warning',
                        'beverage' => 'info',
                        'snack' => 'gray',
                        'combo' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucwords($state, '_'))),
                Tables\Columns\TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                     ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stall')
                    ->options([
                        'appetizer' => 'Appetizer',
                        'main_course' => 'Main Course',
                        'dessert' => 'Dessert',
                        'beverage' => 'Beverage',
                        'snack' => 'Snack',
                        'combo' => 'Combo Meal',
                    ]),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability'),
            ])
            
            ->actions([
                /** @var \App\Models\Product $record */
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('toggle_availability')
                    ->icon(fn (Product $record) => $record->is_available ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Product $record) => $record->is_available ? 'warning' : 'success')
                    ->action(function (Product $record) {
                        $record->update(['is_available' => !$record->is_available]);
                    })
                    ->label(fn (Product $record) => $record->is_available ? 'Hide' : 'Show'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggle_availability')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['is_available' => $data['is_available']]);
                            });
                        })
                        ->form([
                            Forms\Components\Toggle::make('is_available')
                                ->label('Available')
                                ->required(),
                        ]),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        if (!$stallId) {
            return null;
        }

        return static::getModel()::where('stall_id', $stallId)->count();
    }
}
    
