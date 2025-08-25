<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Product;
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

    // Tenant sees only products from their assigned stall
    //public static function getEloquentQuery(): Builder
    //{
    //    $user = Auth::user(); 
    //    
    //    return parent::getEloquentQuery()
    //        ->whereHas('stall', function (Builder $q) use ($user) {
    //           $q->where('tenant_id', $user->id);
    //        });
    //}
     
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\Hidden::make('stall_id')
                            ->default(function () {
                               return Auth::user()?->assignedStall?->id;
                            }),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Describe your product to attract customers'),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->maxValue(9999.99)
                            ->step(0.01),
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name') // 'category' is the relationship in Product model
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Available for Sale')
                            ->default(true)
                            ->helperText('Toggle to temporarily stop selling this item'),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Only published products appear on the public menu'),

                        Forms\Components\TextInput::make('preparation_time')
                            ->label('Preparation Time (minutes)')
                            ->numeric()
                            ->suffix('min')
                            ->default(15)
                            ->helperText('How long does it take to prepare this item?'),
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
                    ->label('Available')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('preparation_time')
                    ->label('Prep Time')
                    ->suffix(' min')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
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
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                // Quick toggle actions
                Tables\Actions\Action::make('toggle_availability')
                    ->label(fn ($record) => $record->is_available ? 'Mark Unavailable' : 'Mark Available')
                    ->icon(fn ($record) => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_available ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_available' => !$record->is_available]);
                    }),
                    
                Tables\Actions\Action::make('toggle_published')
                    ->label(fn ($record) => $record->is_published ? 'Unpublish' : 'Publish')
                    ->icon(fn ($record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->is_published ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_published' => !$record->is_published]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('Mark Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_available' => true])),
                        
                    Tables\Actions\BulkAction::make('mark_unavailable')
                        ->label('Mark Unavailable')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_available' => false])),
                        
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_published' => true])),
                        
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Unpublish')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_published' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Products Yet')
            ->emptyStateDescription('Start adding products to your stall to begin selling.')
            ->emptyStateIcon('heroicon-o-cube');
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

    // Check if tenant has an assigned stall before allowing product creation
    public static function canCreate(): bool
    {
        return !is_null(Auth::user()?->assignedStall);
    }
}