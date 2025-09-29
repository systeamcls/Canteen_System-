<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Stall;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->description('Basic user details and contact information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name'),
                                    
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('user@example.com'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('+63 912 345 6789'),
                                    
                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255)
                                    ->helperText('Leave blank to keep current password when editing'),
                            ]),
                    ]),

                Forms\Components\Section::make('Role & Permissions')
                    ->description('Define user role and system access')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Role')
                                    ->options([
                                        'staff' => 'Canteen Staff',
                                        'cashier' => 'Cashier', 
                                        'tenant' => 'Stall Tenant',
                                    ])
                                    ->required()
                                    ->default('staff')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                        // Only auto-set is_staff when creating new user or if not manually changed
                                        if ($state === 'tenant') {
                                            $set('is_staff', false);
                                        } elseif (in_array($state, ['staff', 'cashier']) && !$get('is_staff')) {
                                            // Only set to true if it's currently false
                                            $set('is_staff', true);
                                        }
                                    })
                                    ->helperText('Primary role classification'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active Account')
                                    ->helperText('User can login and access system')
                                    ->default(true),

                                Forms\Components\Toggle::make('is_staff')
                                    ->label('Include in Attendance')
                                    ->helperText('Track attendance and salary for this user')
                                    ->default(true)
                                    ->live(),
                            ]),
                    ]),

                Forms\Components\Section::make('Staff & Tenant Settings')
                    ->description('Additional settings for staff members and tenants')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('daily_rate')
                                    ->label('Daily Salary Rate')
                                    ->prefix('₱')
                                    ->numeric()
                                    ->default(500.00)
                                    ->step(50)
                                    ->minValue(0)
                                    ->maxValue(10000)
                                    ->visible(fn (Forms\Get $get) => $get('is_staff'))
                                    ->required(fn (Forms\Get $get) => $get('is_staff'))
                                    ->helperText('Daily salary for attendance tracking'),

                                Forms\Components\Select::make('assigned_stall')
                                    ->label('Assigned Stall')
                                    ->options(function () {
                                        return Stall::where('is_active', true)
                                            ->whereNull('tenant_id')
                                            ->orWhere(function ($query) {
                                                // Include current stall if editing
                                                if (request()->route('record')) {
                                                    $user = User::find(request()->route('record'));
                                                    if ($user) {
                                                        $query->where('tenant_id', $user->id);
                                                    }
                                                }
                                            })
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'tenant')
                                    ->helperText('Which stall should this tenant operate?')
                                    ->dehydrated(false), // Don't save this to users table
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('preferred_notification_channel')
                                    ->label('Notification Preference')
                                    ->options([
                                        'email' => 'Email Only',
                                        'sms' => 'SMS Only', 
                                        'both' => 'Both Email & SMS',
                                    ])
                                    ->default('email')
                                    ->helperText('How to send notifications')
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(fn (Forms\Get $get) => !in_array($get('type'), ['tenant']) && !$get('is_staff')),
            ]);
    }

    public static function handleStallAssignment($record, array $data): void
    {
        // Update stall tenant assignment if needed
        if ($data['type'] === 'tenant' && isset($data['assigned_stall'])) {
            $stall = Stall::find($data['assigned_stall']);
            if ($stall) {
                // Clear previous tenant assignment for this user
                Stall::where('tenant_id', $record->id)->update(['tenant_id' => null]);
                
                // Assign new stall
                $stall->update(['tenant_id' => $record->id]);
            }
        } elseif ($data['type'] !== 'tenant') {
            // If user is no longer a tenant, remove stall assignment
            Stall::where('tenant_id', $record->id)->update(['tenant_id' => null]);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->email),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('No phone')
                    ->icon('heroicon-m-phone')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Role')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'staff' => 'Canteen Staff',
                        'cashier' => 'Cashier',
                        'tenant' => 'Stall Tenant',
                        default => ucfirst($state ?? 'Unknown'),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'staff' => 'purple',
                        'cashier' => 'info',
                        'tenant' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_staff')
                    ->label('Staff')
                    ->boolean()
                    ->trueIcon('heroicon-o-identification')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('purple')
                    ->falseColor('gray')
                    ->tooltip(fn ($record) => $record->is_staff ? 
                        'Staff Member' . ($record->daily_rate ? ' (₱' . number_format($record->daily_rate, 0) . '/day)' : '') : 
                        'Not in Attendance System'),

                Tables\Columns\TextColumn::make('assignedStall.name')
                    ->label('Stall Assignment')
                    ->getStateUsing(function ($record) {
                        // Find stall where this user is the tenant
                        $stall = Stall::where('tenant_id', $record->id)->first();
                        return $stall?->name;
                    })
                    ->placeholder('None assigned')
                    ->badge()
                    ->color('primary')
                    ->visible(fn () => request()->has('tableFilters')),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->is_active ? 'Active Account' : 'Inactive Account'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime('M j, Y')
                    ->placeholder('Not verified')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Role')
                    ->options([
                        'staff' => 'Canteen Staff',
                        'cashier' => 'Cashiers',
                        'tenant' => 'Stall Tenants',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Account Status')
                    ->placeholder('All users')
                    ->trueLabel('Active users')
                    ->falseLabel('Inactive users'),

                Tables\Filters\TernaryFilter::make('is_staff')
                    ->label('In Attendance System')
                    ->placeholder('All users')
                    ->trueLabel('In attendance')
                    ->falseLabel('Not in attendance'),

                Tables\Filters\SelectFilter::make('assigned_stall')
                    ->label('Assigned Stall')
                    ->options(Stall::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $stallId): Builder => $query->whereHas('assignedStall', function ($q) use ($stallId) {
                                $q->where('id', $stallId);
                            }),
                        );
                    })
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verification')
                    ->placeholder('All users')
                    ->trueLabel('Verified emails')
                    ->falseLabel('Unverified emails')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                        ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                            $status = $record->is_active ? 'activated' : 'deactivated';
                            
                            Notification::make()
                                ->title('User Status Updated')
                                ->body("{$record->name} has been {$status}")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription(fn ($record) => $record->is_active 
                            ? 'This user will no longer be able to access the system.'
                            : 'This user will regain access to the system.'),

                    Tables\Actions\Action::make('toggle_staff')
                        ->label(fn ($record) => $record->is_staff ? 'Remove from Attendance' : 'Add to Attendance')
                        ->icon(fn ($record) => $record->is_staff ? 'heroicon-o-user-minus' : 'heroicon-o-user-plus')
                        ->color(fn ($record) => $record->is_staff ? 'warning' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_staff' => !$record->is_staff]);
                            $status = $record->is_staff ? 'added to attendance system' : 'removed from attendance system';
                            
                            Notification::make()
                                ->title('Attendance Status Updated')
                                ->body("{$record->name} has been {$status}")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription(fn ($record) => $record->is_staff 
                            ? 'This user will no longer be tracked for attendance and payroll.'
                            : 'This user will be included in attendance tracking and payroll.'),

                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('new_password')
                                ->label('New Password')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->confirmed(),
                            Forms\Components\TextInput::make('new_password_confirmation')
                                ->label('Confirm Password')
                                ->password()
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'password' => Hash::make($data['new_password']),
                            ]);
                            
                            Notification::make()
                                ->title('Password Reset')
                                ->body('Password has been updated successfully')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('manage_stall')
                        ->label('Manage Stall')
                        ->icon('heroicon-o-building-storefront')
                        ->color('primary')
                        ->visible(fn ($record) => $record->type === 'tenant' && $record->adminStall)
                        ->url(fn ($record) => route('filament.admin.resources.stalls.view', $record->adminStall)),

                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('This will permanently delete the user account and cannot be undone.'),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate_users')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $count = $records->where('is_active', false)->count();
                            $records->each->update(['is_active' => true]);
                            
                            Notification::make()
                                ->title('Users Activated')
                                ->body("Activated {$count} user accounts")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate_users')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $count = $records->where('is_active', true)->count();
                            $records->each->update(['is_active' => false]);
                            
                            Notification::make()
                                ->title('Users Deactivated')
                                ->body("Deactivated {$count} user accounts")
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Selected users will no longer be able to access the system.')
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('add_to_attendance')
                        ->label('Add to Attendance')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->action(function ($records) {
                            $count = $records->where('is_staff', false)->count();
                            $records->each->update(['is_staff' => true]);
                            
                            Notification::make()
                                ->title('Users Added to Attendance')
                                ->body("Added {$count} users to attendance system")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Selected users will be included in attendance tracking and payroll.')
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('remove_from_attendance')
                        ->label('Remove from Attendance')
                        ->icon('heroicon-o-user-minus')
                        ->color('warning')
                        ->action(function ($records) {
                            $count = $records->where('is_staff', true)->count();
                            $records->each->update(['is_staff' => false]);
                            
                            Notification::make()
                                ->title('Users Removed from Attendance')
                                ->body("Removed {$count} users from attendance system")
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Selected users will no longer be tracked for attendance and payroll.')
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('export_users')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function ($records) {
                            Notification::make()
                                ->title('Export Prepared')
                                ->body('User data export for ' . $records->count() . ' users')
                                ->info()
                                ->send();
                            // Add your export logic here
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('This will permanently delete the selected users and cannot be undone.'),
                ]),
            ])
            ->emptyStateHeading('No users found')
            ->emptyStateDescription('Start by creating user accounts for staff, cashiers, and tenants.')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add First User')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function getNavigationBadge(): ?string
    {
        $inactiveCount = static::getModel()::where('is_active', false)->count();
        $unverifiedCount = static::getModel()::whereNull('email_verified_at')->count();
        
        $total = $inactiveCount + $unverifiedCount;
        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $inactiveCount = static::getModel()::where('is_active', false)->count();
        return $inactiveCount > 0 ? 'danger' : 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}