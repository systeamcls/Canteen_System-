<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StallResource\Pages;
use App\Models\Stall;
use App\Models\User;
use App\Models\RentalPayment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;

class StallResource extends Resource
{
    protected static ?string $model = Stall::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Stall Management';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Stall Identity')
                    ->description('Basic information about this stall')
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
                                    ->placeholder('e.g. Stall 1, Ground Floor')
                                    ->helperText('Physical location identifier'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->placeholder('Brief description of the stall\'s specialty or cuisine...')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Tenant Assignment')
                    ->description('Who operates this stall')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('tenant_id')
                                    ->label('Current Tenant')
                                    ->relationship('tenant', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->options(function ($record) {
                                        $query = User::where('type', 'tenant')
                                            ->where('is_active', true)
                                            ->whereDoesntHave('assignedStall');
                                        
                                        if ($record && $record->tenant_id) {
                                            $query->orWhere('id', $record->tenant_id);
                                        }
                                        return $query->pluck('name', 'id');
                                    })
                                    ->placeholder('Select a tenant')
                                    ->helperText('Person responsible for this stall')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\TextInput::make('email')->email()->required(),
                                        Forms\Components\TextInput::make('phone')->tel(),
                                        Forms\Components\Hidden::make('type')->default('tenant'),
                                    ])
                                    ->createOptionModalHeading('Create New Tenant'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Stall Active')
                                    ->helperText('Available for operation')
                                    ->default(true),
                            ]),
                    ]),

                Forms\Components\Section::make('Operating Details')
                    ->description('Business hours and contact information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('opening_time')
                                    ->label('Opens At')
                                    ->seconds(false)
                                    ->default('08:00'),
                                
                                Forms\Components\TimePicker::make('closing_time')
                                    ->label('Closes At')
                                    ->seconds(false)
                                    ->default('18:00'),

                                Forms\Components\TextInput::make('contact_number')
                                    ->label('Contact Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+63 912 345 6789'),
                            ]),
                    ]),

                Forms\Components\Section::make('Rental Information')
                    ->description('Fixed daily rental rate')
                    ->schema([
                        Forms\Components\TextInput::make('rental_fee')
                            ->label('Daily Rental Fee')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->step(0.01)
                            ->minValue(0)
                            ->default(5000.00)
                            ->helperText('Fixed daily rate paid by tenant')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Branding')
                    ->description('Stall logo and visual identity')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('stall-logos')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Max 2MB. Recommended: 400x400px')
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
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
                    ->label('Logo')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=Stall&background=f3f4f6&color=6b7280&size=120'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Stall Details')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record) => $record->location)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Current Tenant')
                    ->searchable()
                    ->default('Vacant')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->icon(fn ($state) => $state ? 'heroicon-m-user' : 'heroicon-m-building-office'),

                Tables\Columns\TextColumn::make('rental_payment_status')
                    ->label('Rent Status')
                    ->getStateUsing(function (Stall $record): string {
                        if (!$record->tenant_id) return 'No tenant';
                        
                        $currentMonth = now()->format('Y-m');
                        $payment = RentalPayment::where('stall_id', $record->id)
                            ->where('tenant_id', $record->tenant_id)
                            ->whereDate('due_date', '>=', now()->startOfMonth())
                            ->whereDate('due_date', '<=', now()->endOfMonth())
                            ->first();
                        
                        if (!$payment) return 'No payment due';
                        
                        return match($payment->status) {
                            'paid' => 'Paid',
                            'overdue' => 'Overdue',
                            'partially_paid' => 'Partial',
                            default => 'Pending',
                        };
                    })
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Paid' => 'success',
                        'Overdue' => 'danger',
                        'Partial' => 'warning',
                        'Pending' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('rental_fee')
                    ->label('Daily Rate')
                    ->money('PHP')
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('operating_hours')
                    ->label('Hours')
                    ->getStateUsing(function (Stall $record): string {
                        if (!$record->opening_time || !$record->closing_time) {
                            return 'Not set';
                        }
                        return Carbon::parse($record->opening_time)->format('g:i A') . 
                               ' - ' . 
                               Carbon::parse($record->closing_time)->format('g:i A');
                    })
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->tooltip(fn ($record): string => $record->is_active ? 'Active' : 'Inactive'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Menu Items')
                    ->counts('products')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_payment')
                    ->label('Last Payment')
                    ->getStateUsing(function (Stall $record): string {
                        if (!$record->tenant_id) return 'N/A';
                        
                        $lastPayment = RentalPayment::where('stall_id', $record->id)
                            ->where('status', 'paid')
                            ->latest('paid_date')
                            ->first();
                        
                        return $lastPayment ? $lastPayment->paid_date->diffForHumans() : 'Never';
                    })
                    ->color('gray')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Stall Status')
                    ->placeholder('All stalls')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\TernaryFilter::make('has_tenant')
                    ->label('Occupancy')
                    ->placeholder('All stalls')
                    ->trueLabel('Occupied stalls')
                    ->falseLabel('Vacant stalls')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('tenant_id'),
                        false: fn (Builder $query) => $query->whereNull('tenant_id'),
                    ),

                Tables\Filters\Filter::make('overdue_rent')
                    ->label('Overdue Rent')
                    ->query(fn (Builder $query) => 
                        $query->whereHas('rentalPayments', fn ($q) => 
                            $q->where('status', 'overdue')
                              ->whereDate('due_date', '<=', now())
                        )
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('high_rental')
                    ->label('High Rent (₱200+/day)')
                    ->query(fn (Builder $query) => $query->where('rental_fee', '>=', 200))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('View Details'),
                    
                    Tables\Actions\EditAction::make()
                        ->label('Edit Stall'),
                    
                    Tables\Actions\Action::make('manage_tenant')
                        ->label(fn ($record) => $record->tenant_id ? 'Change Tenant' : 'Assign Tenant')
                        ->icon(fn ($record) => $record->tenant_id ? 'heroicon-o-arrow-path' : 'heroicon-o-user-plus')
                        ->color(fn ($record) => $record->tenant_id ? 'warning' : 'success')
                        ->form([
                            Forms\Components\Select::make('tenant_id')
                                ->label('Select Tenant')
                                ->options(function ($record) {
                                    return User::where('type', 'tenant')
                                        ->where('is_active', true)
                                        ->where(function ($query) use ($record) {
                                            $query->whereDoesntHave('assignedStall')
                                                  ->orWhere('id', $record->tenant_id);
                                        })
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->placeholder('Choose a tenant'),
                            
                            Forms\Components\Textarea::make('assignment_notes')
                                ->label('Assignment Notes')
                                ->placeholder('Optional notes about this assignment change...')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            $oldTenant = $record->tenant?->name;
                            $record->update(['tenant_id' => $data['tenant_id'] ?: null]);
                            $newTenant = $record->fresh()->tenant?->name;
                            
                            if ($newTenant) {
                                $message = $oldTenant ? "Tenant changed from {$oldTenant} to {$newTenant}" : "Tenant {$newTenant} assigned to {$record->name}";
                            } else {
                                $message = "Tenant {$oldTenant} removed from {$record->name}";
                            }
                            
                            Notification::make()
                                ->title('Tenant Assignment Updated')
                                ->body($message)
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('rental_payment')
                        ->label('View Payments')
                        ->icon('heroicon-o-banknotes')
                        ->color('primary')
                        ->visible(fn ($record) => $record->tenant_id)
                        ->url(fn ($record) => route('filament.admin.resources.rental-payments.index') . 
                            '?tableFilters[stall][value]=' . $record->id),

                    Tables\Actions\Action::make('create_payment')
                        ->label('Record Payment')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->visible(fn ($record) => $record->tenant_id)
                        ->url(fn ($record) => route('filament.admin.resources.rental-payments.create') . 
                            '?stall_id=' . $record->id . '&tenant_id=' . $record->tenant_id),

                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                        ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalDescription(fn ($record) => $record->is_active 
                            ? 'This will make the stall unavailable for operations.'
                            : 'This will reactivate the stall for operations.')
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                            $status = $record->is_active ? 'activated' : 'deactivated';
                            
                            Notification::make()
                                ->title("Stall {$status}")
                                ->body("{$record->name} has been {$status}")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('contact_tenant')
                        ->label('Contact Tenant')
                        ->icon('heroicon-o-phone')
                        ->color('info')
                        ->visible(fn ($record) => $record->tenant_id && $record->tenant->phone)
                        ->action(function ($record) {
                            Notification::make()
                                ->title('Contact Information')
                                ->body("Tenant: {$record->tenant->name}\nPhone: {$record->tenant->phone}")
                                ->info()
                                ->persistent()
                                ->send();
                        }),
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
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                            
                            Notification::make()
                                ->title('Stalls Activated')
                                ->body(count($records) . ' stalls have been activated')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-pause-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('This will make the selected stalls unavailable for operations.')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                            
                            Notification::make()
                                ->title('Stalls Deactivated')
                                ->body(count($records) . ' stalls have been deactivated')
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('send_notification')
                        ->label('Notify Tenants')
                        ->icon('heroicon-o-bell')
                        ->color('info')
                        ->form([
                            Forms\Components\Textarea::make('message')
                                ->required()
                                ->label('Notification Message')
                                ->placeholder('Type your message to all selected stall tenants...')
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            $notifiedCount = 0;
                            foreach ($records as $record) {
                                if ($record->tenant_id) {
                                    // Here you would send actual notification (email/SMS)
                                    $notifiedCount++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Notifications Sent')
                                ->body("Message sent to {$notifiedCount} tenants")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No stalls registered')
            ->emptyStateDescription('Create your first stall to start managing your canteen space.')
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add First Stall')
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->striped()
            ->defaultSort('name', 'asc')
            ->poll('30s'); // Auto-refresh for payment status updates
    }

    public static function getNavigationBadge(): ?string
    {
        $total = static::getModel()::count();
        $maxStalls = 8;
        
        if ($total === 0) return null;
        
        return "{$total}/{$maxStalls}";
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $total = static::getModel()::count();
        $maxStalls = 8;
        
        if ($total === 0) return 'gray';
        
        $ratio = $total / $maxStalls;
        
        return match(true) {
            $ratio >= 0.9 => 'danger',   // Near capacity
            $ratio >= 0.7 => 'warning',  // Getting full
            $ratio >= 0.4 => 'success',  // Good occupancy
            default => 'info',           // Low occupancy
        };
    }

    public static function canCreate(): bool
    {
        return static::getModel()::count() < 8; // Max 8 stalls
    }

    public static function getCreateFormSuccessMessage(): string
    {
        $total = static::getModel()::count();
        $remaining = 8 - $total;
        
        return $remaining > 0 
            ? "Stall created successfully! You can add {$remaining} more stalls."
            : "Stall created successfully! You've reached the maximum of 8 stalls.";
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