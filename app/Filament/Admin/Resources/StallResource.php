<?php

// ========================================
// IMPROVED STALLRESOURCE.PHP
// ========================================

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
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class StallResource extends Resource
{
    protected static ?string $model = Stall::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Stall identity and location details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Mario\'s Kitchen')
                                    ->unique(ignoreRecord: true),
                                
                                Forms\Components\TextInput::make('location')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Ground Floor, Section A')
                                    ->helperText('Physical location in the canteen'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->placeholder('Describe the stall\'s specialty, cuisine type, or unique features...')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Management & Assignment')
                    ->description('Who manages and operates this stall')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('tenant_id')
                                    ->label('Assigned Tenant')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->options(function ($record) {
                                        $query = User::role('tenant')
                                        ->where('is_active',    true)
                                        ->whereDoesntHave('assignedStall'); // This prevents double assignment
                                        if ($record && $record->tenant_id) {
                                            $query->orWhere('id', $record->tenant_id);
                                        }
                                        return $query->pluck('name', 'id');
                                    })
                                    ->placeholder('Select a tenant')
                                    ->helperText('The person responsible for operating this stall')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\TextInput::make('email')->email()->required(),
                                        Forms\Components\TextInput::make('phone')->tel(),
                                        Forms\Components\Hidden::make('role')->default('tenant'),
                                    ])
                                    ->createOptionModalHeading('Create New Tenant'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Stall Active')
                                    ->helperText('Can accept orders and operate')
                                    ->default(true),
                            ]),
                    ]),

                Forms\Components\Section::make('Business Settings')
                    ->description('Operating hours, fees, and policies')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('opening_time')
                                    ->label('Opening Time')
                                    ->seconds(false)
                                    ->default('08:00'),
                                
                                Forms\Components\TimePicker::make('closing_time')
                                    ->label('Closing Time')
                                    ->seconds(false)
                                    ->default('18:00'),

                                Forms\Components\TextInput::make('contact_number')
                                    ->label('Contact Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+63 912 345 6789'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('rental_fee')
                                    ->label('Monthly Rental Fee')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₱')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->default(5000.00)
                                    ->rules(['regex:/^\d+(\.\d{1,2})?$/'])
                                    ->helperText('Amount tenant pays monthly'),
                        
                                Forms\Components\TextInput::make('commission_rate')
                                    ->label('Commission Rate (%)')
                                    ->required()
                                    ->numeric()
                                    ->suffix('%')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(15.00)
                                    ->helperText('Percentage of sales as commission'),
                            ]),
                    ]),

                Forms\Components\Section::make('Branding & Media')
                    ->description('Logo and visual identity')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('stall-logos')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->helperText('Max 2MB. JPEG or PNG format.')
                            ->deletable(true)
                            ->removeUploadedFileButtonPosition('right')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(asset('images/default-stall.png')),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->location),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->placeholder('Vacant')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->url(fn ($record) => $record->tenant ?  
                        route('filament.admin.resources.users.view', $record->tenant) : null),

                Tables\Columns\ViewColumn::make('performance_metrics')
                    ->label('Performance')
                    ->view('components.stall-metrics'),

                Tables\Columns\TextColumn::make('rental_fee')
                    ->label('Rent')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission_rate')
                    ->label('Commission')
                    ->suffix('%')
                    ->alignCenter(),

                Tables\Columns\ViewColumn::make('status_indicators')
                    ->label('Status')
                    ->view('components.stall-status'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All stalls')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('vacant')
                    ->label('Vacant Stalls')
                    ->query(fn (Builder $query) => $query->whereNull('tenant_id'))
                    ->toggle(),
                
                Tables\Filters\Filter::make('high_performance')
                    ->label('High Performers')
                    ->query(function (Builder $query) {
                        return $query->whereHas('orders', function ($q) {
                            $q->where('created_at', '>=', now()->subMonth())
                              ->where('status', 'completed');
                        }, '>=', 50);
                    })
                    ->toggle(),

                Tables\Filters\Filter::make('overdue_rent')
                    ->label('Overdue Rent')
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
                    Tables\Actions\Action::make('view_performance')
                        ->label('View Analytics')
                        ->icon('heroicon-o-chart-bar')
                        ->color('info')
                        ->url(fn ($record) => route('filament.admin.resources.orders.index', [
                            'tableFilters[stall][value]' => $record->id
                        ])),

                    Tables\Actions\Action::make('manage_tenant')
                        ->label(fn ($record) => $record->tenant_id ? 'Change Tenant' : 'Assign Tenant')
                        ->icon(fn ($record) => $record->tenant_id ? 'heroicon-o-arrow-path' : 'heroicon-o-user-plus')
                        ->color(fn ($record) => $record->tenant_id ? 'warning' : 'success')
                        ->form([
                            Forms\Components\Select::make('tenant_id')
                                ->label('Select Tenant')
                                ->options(function ($record) {
                                    return User::role('tenant')
                                        ->where('is_active', true)
                                        ->where(function ($query) use ($record) {
                                            $query->whereDoesntHave('assignedStall')
                                                  ->orWhere('id', $record->tenant_id);
                                        })
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->placeholder('Choose a tenant'),
                        ])
                        ->action(function ($record, array $data) {
                            $oldTenant = $record->tenant?->name;
                            $record->update(['tenant_id' => $data['tenant_id'] ?: null]);
                            $newTenant = $record->fresh()->tenant?->name;
                            
                            if ($newTenant) {
                                $message = $oldTenant ? "Tenant changed from {$oldTenant} to {$newTenant}" : "Tenant {$newTenant} assigned";
                            } else {
                                $message = "Tenant {$oldTenant} removed";
                            }
                            
                            Notification::make()->title($message)->success()->send();
                        }),

                    Tables\Actions\Action::make('create_payment')
                        ->label('Record Payment')
                        ->icon('heroicon-o-banknotes')
                        ->color('primary')
                        ->visible(fn ($record) => $record->tenant_id)
                        ->url(fn ($record) => route('filament.admin.resources.rental-payments.create', [
                            'stall_id' => $record->id,
                            'tenant_id' => $record->tenant_id,
                            'amount' => $record->rental_fee
                        ])),

                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                        ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                            $status = $record->is_active ? 'activated' : 'deactivated';
                            Notification::make()->title("Stall {$status}")->success()->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-play-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-pause-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No stalls registered')
            ->emptyStateDescription('Create your first stall to start managing the canteen.')
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add First Stall')
                    ->icon('heroicon-o-plus'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $total = static::getModel()::count();
        $active = static::getModel()::where('is_active', true)->count();
        return $total > 0 ? "{$active}/{$total}" : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $total = static::getModel()::count();
        if ($total === 0) return 'gray';
        
        $active = static::getModel()::where('is_active', true)->count();
        $ratio = $active / $total;
        
        return match(true) {
            $ratio >= 0.8 => 'success',
            $ratio >= 0.5 => 'warning',
            default => 'danger',
        };
    }

    public static function canCreate(): bool
{
    return true;
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
}