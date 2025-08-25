<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Stall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tenant\Resources\TenantStallResource\Pages;

class TenantStallResource extends Resource
{
    protected static ?string $model = Stall::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'My Stall';
    protected static ?string $navigationLabel = 'Stall Details';
    protected static ?int $navigationSort = 1;


    public static function shouldRegisterNavigation(): bool
    {
    \Log::info('TenantStallResource - User: ' . Auth::id() . ', Role: ' . Auth::user()->getRoleNames()->implode(','));
    return true;
    }

    // Tenant sees only their assigned stall
    //public static function getEloquentQuery(): Builder
    //{
    //    return parent::getEloquentQuery()
    //        ->where('tenant_id', Auth::id());
    //}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Stall Information')
                    ->description('Manage your stall details and settings')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Your stall name as it appears to customers'),

                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255)
                            ->disabled() // Tenant cannot change location
                            ->helperText('Location is managed by admin'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Describe your stall and what makes it special'),

                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('stalls')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300'),

                        Forms\Components\TextInput::make('contact_number')
                            ->tel()
                            ->maxLength(20)
                            ->helperText('Customer contact number for your stall'),

                        Forms\Components\TimePicker::make('opening_time')
                            ->seconds(false)
                            ->helperText('When do you start serving?'),

                        Forms\Components\TimePicker::make('closing_time')
                            ->seconds(false)
                            ->helperText('When do you stop serving?'),

                        Forms\Components\TextInput::make('rental_fee')
                            ->disabled() // Tenant cannot change rental fee
                            ->numeric()
                            ->prefix('₱')
                            ->helperText('Monthly rental fee (managed by admin)'),

                        Forms\Components\Toggle::make('is_active')
                            ->disabled() // Only admin can activate/deactivate
                            ->helperText('Stall status (managed by admin)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->size(60),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('opening_time')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('closing_time')
                    ->time('H:i'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateHeading('No Stall Assigned')
            ->emptyStateDescription('You have not been assigned a stall yet. Please contact the administrator.')
            ->emptyStateIcon('heroicon-o-building-storefront');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenantStalls::route('/'),
            'view' => Pages\ViewTenantStall::route('/{record}'),
            'edit' => Pages\EditTenantStall::route('/{record}/edit'),
        ];
    }

    // Tenant cannot create new stalls (admin assigns them)
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false; // Tenant cannot delete their stall
    }
}