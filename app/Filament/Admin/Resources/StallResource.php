<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StallResource\Pages;
use App\Models\Stall;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Support\Facades\Auth;

class StallResource extends Resource
{
    protected static ?string $model = Stall::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Stalls';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Manage stall details and configuration')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => 
                                $set('slug', \Illuminate\Support\Str::slug($state))
                            ),
                        
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Physical location in the canteen'),
                            
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Management')
                    ->schema([
                        Forms\Components\Select::make('owner_id')
                            ->label('Stall Owner')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('email')->email()->required(),
                                Forms\Components\TextInput::make('phone')->tel(),
                            ]),
                            
                        Forms\Components\Select::make('tenant_id')
                            ->label('Current Tenant')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                return User::role('tenant')
                                    ->where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->helperText('Assign a tenant to manage this stall'),

                        Forms\Components\Select::make('user_id')
                            ->label('Assigned Manager')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Primary user responsible for this stall'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Business Settings')
                    ->schema([
                        Forms\Components\TimePicker::make('opening_time')
                            ->seconds(false)
                            ->default('08:00'),
                        
                        Forms\Components\TimePicker::make('closing_time')
                            ->seconds(false)
                            ->default('18:00'),
                            
                        Forms\Components\TextInput::make('rental_fee')
                            ->label('Monthly Rental Fee')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(999999.99)
                            ->default(5000),
                        
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('Commission Rate (%)')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(15.00),
                            
                        Forms\Components\TextInput::make('contact_number')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Branding')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('stall-logos')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300')
                            ->maxSize(2048),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->size(50),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->placeholder('Vacant')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->url(fn ($record) => $record->tenant ? 
                        route('filament.admin.resources.users.view', $record->tenant) : null
                    ),

                Tables\Columns\TextColumn::make('rental_status')
                    ->label('Rental Status')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->getCurrentRentalStatus())
                    ->color(fn (string $state): string => match($state) {
                        'vacant' => 'gray',
                        'no_payment' => 'warning', 
                        'pending' => 'warning',
                        'paid' => 'success',
                        'partially_paid' => 'info',
                        'overdue' => 'danger',
                        default => 'gray'
                    }),
                    
                Tables\Columns\TextColumn::make('rental_fee')
                    ->label('Monthly Rent')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission_rate')
                    ->label('Commission')
                    ->suffix('%')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant')
                    ->relationship('tenant', 'name')
                    ->placeholder('All tenants'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All stalls')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('vacant')
                    ->label('Vacant Stalls')
                    ->query(fn (Builder $query) => $query->whereNull('tenant_id'))
                    ->toggle(),
                
                Tables\Filters\Filter::make('overdue_payments')
                    ->label('Overdue Payments')
                    ->query(fn (Builder $query) => 
                        $query->whereHas('rentalPayments', fn ($q) => 
                            $q->where('status', 'overdue')
                        )
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('assign_tenant')
                        ->label('Assign Tenant')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->visible(fn ($record) => !$record->tenant_id)
                        ->form([
                            Forms\Components\Select::make('tenant_id')
                                ->label('Select Tenant')
                                ->options(function () {
                                    return User::role('tenant')
                                        ->where('is_active', true)
                                        ->whereDoesntHave('assignedStall')
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->searchable()
                                ->placeholder('Choose an available tenant'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update(['tenant_id' => $data['tenant_id']]);
                            
                            Notification::make()
                                ->title('Tenant assigned successfully')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('remove_tenant')
                        ->label('Remove Tenant')
                        ->icon('heroicon-o-user-minus')
                        ->color('danger')
                        ->visible(fn ($record) => $record->tenant_id)
                        ->requiresConfirmation()
                        ->modalHeading('Remove Tenant')
                        ->modalDescription('This will remove the tenant assignment. Are you sure?')
                        ->action(function ($record) {
                            $record->update(['tenant_id' => null]);
                            
                            Notification::make()
                                ->title('Tenant removed successfully')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('view_payments')
                        ->label('View Payments')
                        ->icon('heroicon-o-banknotes')
                        ->color('info')
                        ->url(fn ($record) => route('filament.admin.resources.rental-payments.index', [
                            'tableFilters[stall][value]' => $record->id
                        ])),
                        
                    Tables\Actions\Action::make('create_payment')
                        ->label('Create Payment')
                        ->icon('heroicon-o-plus-circle')
                        ->color('warning')
                        ->visible(fn ($record) => $record->tenant_id)
                        ->url(fn ($record) => route('filament.admin.resources.rental-payments.create', [
                            'stall_id' => $record->id,
                            'tenant_id' => $record->tenant_id,
                            'amount' => $record->rental_fee
                        ])),

                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('30s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Stall Overview')
                    ->schema([
                        Components\Split::make([
                            Components\ImageEntry::make('logo')
                                ->hiddenLabel()
                                ->size(120)
                                ->grow(false),
                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('name')
                                        ->size('lg')
                                        ->weight('bold'),
                                    Components\TextEntry::make('location'),
                                    Components\IconEntry::make('is_active')
                                        ->label('Status')
                                        ->boolean(),
                                    Components\TextEntry::make('created_at')
                                        ->dateTime(),
                                ]),
                        ]),
                        Components\TextEntry::make('description')
                            ->columnSpanFull()
                            ->placeholder('No description provided'),
                    ])
                    ->columns(1),

                Components\Section::make('Management')
                    ->schema([
                        Components\TextEntry::make('owner.name')
                            ->label('Owner'),
                        Components\TextEntry::make('tenant.name')
                            ->label('Current Tenant')
                            ->placeholder('Vacant'),
                        Components\TextEntry::make('user.name')
                            ->label('Assigned Manager')
                            ->placeholder('None assigned'),
                        Components\TextEntry::make('getCurrentRentalStatus')
                            ->label('Rental Status')
                            ->badge()
                            ->color(fn (string $state): string => match($state) {
                                'vacant' => 'gray',
                                'no_payment' => 'warning', 
                                'pending' => 'warning',
                                'paid' => 'success',
                                'partially_paid' => 'info',
                                'overdue' => 'danger',
                                default => 'gray'
                            }),
                    ])
                    ->columns(2),

                Components\Section::make('Business Details')
                    ->schema([
                        Components\TextEntry::make('rental_fee')
                            ->money('PHP'),
                        Components\TextEntry::make('commission_rate')
                            ->suffix('%'),
                        Components\TextEntry::make('opening_time')
                            ->time(),
                        Components\TextEntry::make('closing_time')
                            ->time(),
                        Components\TextEntry::make('contact_number')
                            ->placeholder('Not provided'),
                        Components\TextEntry::make('products_count')
                            ->label('Total Products')
                            ->getStateUsing(fn ($record) => $record->products()->count()),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStalls::route('/'),
            'create' => Pages\CreateStall::route('/create'),
            'view' => Pages\ViewStall::route('/{record}'),
            'edit' => Pages\EditStall::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $total = static::getModel()::count();
        $maxStalls = config('canteen.max_stalls', 10); // Make this configurable
        
        return $total >= $maxStalls ? 'danger' : 'success';
    }

    // Make stall limit configurable
    public static function canCreate(): bool
    {
        $maxStalls = config('canteen.max_stalls', 10);
        return Stall::count() < $maxStalls;
    }
}