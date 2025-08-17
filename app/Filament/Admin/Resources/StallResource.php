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

class StallResource extends Resource
{
    protected static ?string $model = Stall::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Canteen Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Stalls';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('tenant_id')
                            ->label('Assigned Tenant')
                            ->relationship('tenant', 'name')
                            ->options(User::role('tenant')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionAction(fn ($action) => 
                                $action->after(fn ($record) => $record->assignRole('tenant'))
                            ),
                            
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('rental_fee')
                            ->label('Monthly Rental Fee')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->maxValue(999999.99)
                            ->helperText('This amount will be used for automatic rental payment generation'),
                            
                        Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Assigned Tenant')
                    ->searchable()
                    ->sortable()
                    ->default('Unassigned')
                    ->url(fn ($record) => $record->tenant ? 
                        route('filament.admin.resources.users.view', $record->tenant) : null
                    ),
                    
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('rental_fee')
                    ->label('Monthly Rent')
                    ->money('PHP')
                    ->sortable(),

                // Add rental status indicator
                Tables\Columns\TextColumn::make('rental_status')
                    ->label('Rental Status')
                    ->getStateUsing(function ($record) {
                        if (!$record->tenant_id) return 'no_tenant';
                        
                        $currentPayment = $record->currentRentalPayment();
                        if (!$currentPayment) return 'no_payment';
                        
                        return $currentPayment->status;
                    })
                    ->colors([
                        'secondary' => 'no_tenant',
                        'warning_no_payment' => 'no_payment',
                        'warning_pending' => 'pending',
                        'success' => 'paid', 
                        'info' => 'partially_paid',
                        'danger' => 'overdue',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'no_tenant' => 'No Tenant',
                        'no_payment' => 'No Payment Record',
                        'pending' => 'Payment Pending',
                        'paid' => 'Rent Paid',
                        'partially_paid' => 'Partially Paid',
                        'overdue' => 'Overdue',
                        default => 'Unknown'
                    }),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant')
                    ->relationship('tenant', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
                
                Tables\Filters\Filter::make('has_overdue_payments')
                    ->label('Has Overdue Payments')
                    ->query(fn (Builder $query) => 
                        $query->whereHas('rentalPayments', fn ($q) => 
                            $q->where('status', 'overdue')
                        )
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                // Link to rental payments
                Tables\Actions\Action::make('view_rental_payments')
                    ->label('Rental Payments')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.rental-payments.index', [
                        'tableFilters[stall][value]' => $record->id
                    ])),
                    
                // Quick action to create rental payment
                Tables\Actions\Action::make('create_rental_payment')
                    ->label('New Payment')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->tenant_id)
                    ->url(fn ($record) => route('filament.admin.resources.rental-payments.create', [
                        'stall_id' => $record->id,
                        'tenant_id' => $record->tenant_id
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
}