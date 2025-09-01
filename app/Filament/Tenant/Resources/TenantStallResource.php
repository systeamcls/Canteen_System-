<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Stall;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tenant\Resources\TenantStallResource\Pages;

class TenantStallResource extends Resource
{
    protected static ?string $model = Stall::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Stall Management';
    protected static ?string $navigationLabel = 'My Stall';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        return parent::getEloquentQuery()
            ->where('tenant_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                // Also include stalls where user is assigned via admin_stall_id
                $query->where('id', $user->admin_stall_id);
            });
    }

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
                            ->disabled()
                            ->helperText('Location is managed by the canteen admin'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Describe your stall, cuisine type, and what makes it special'),

                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('stall-logos')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                            ->maxSize(2048)
                            ->helperText('Square logo recommended, max 2MB'),

                        Forms\Components\TextInput::make('contact_number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+63 912 345 6789')
                            ->helperText('Customer contact number for your stall'),

                        Forms\Components\TimePicker::make('opening_time')
                            ->seconds(false)
                            ->default('08:00')
                            ->helperText('When do you start serving customers?'),

                        Forms\Components\TimePicker::make('closing_time')
                            ->seconds(false)
                            ->default('18:00')
                            ->helperText('When do you stop serving customers?'),

                        // Read-only fields managed by admin
                        Forms\Components\TextInput::make('rental_fee')
                            ->disabled()
                            ->numeric()
                            ->prefix('PHP ')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->helperText('Monthly rental fee (set by admin)'),

                        Forms\Components\TextInput::make('commission_rate')
                            ->disabled()
                            ->numeric()
                            ->suffix('%')
                            ->helperText('Commission rate (set by admin)'),

                        Forms\Components\Toggle::make('is_active')
                            ->disabled()
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
                    ->disk('public')
                    ->circular()
                    ->size(80)
                    ->defaultImageUrl(url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGM0Y0RjYiLz4KPHN2ZyB4PSI4IiB5PSI4IiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIGZpbGw9Im5vbmUiPgo8cGF0aCBkPSJNMyAybDEzIDEzTDMgMjgiIHN0cm9rZT0iIzlDQTNBRiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+Cjwvc3ZnPgo=')),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->weight('bold')
                        ->size('lg'),
                    Tables\Columns\TextColumn::make('location')
                        ->color('gray'),
                    Tables\Columns\TextColumn::make('description')
                        ->limit(100)
                        ->color('gray'),
                ]),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('operating_hours')
                        ->getStateUsing(fn ($record) => 
                            ($record->opening_time ? $record->opening_time->format('H:i') : '00:00') . 
                            ' - ' . 
                            ($record->closing_time ? $record->closing_time->format('H:i') : '00:00')
                        )
                        ->icon('heroicon-m-clock'),
                    Tables\Columns\TextColumn::make('contact_number')
                        ->icon('heroicon-m-phone')
                        ->placeholder('No contact number'),
                ]),

                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('rental_fee')
                        ->money('PHP')
                        ->icon('heroicon-m-banknotes'),
                    Tables\Columns\TextColumn::make('commission_rate')
                        ->suffix('%')
                        ->icon('heroicon-m-calculator'),
                ]),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->contentGrid([
                'md' => 1,
                'lg' => 1,
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Update Details'),
                Tables\Actions\ViewAction::make(),
            ])
            ->emptyStateHeading('No stall assigned')
            ->emptyStateDescription('You have not been assigned a stall yet. Please contact the canteen administrator.')
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $stall = $user->assignedStall;
        
        if (!$stall) return '!';
        
        return $stall->is_active ? null : '!';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $user = Auth::user();
        $stall = $user->assignedStall;
        
        if (!$stall || !$stall->is_active) return 'danger';
        
        return null;
    }
    public static function canViewAny(): bool
    {
    return Auth::user()?->assignedStall !== null;
    }
}