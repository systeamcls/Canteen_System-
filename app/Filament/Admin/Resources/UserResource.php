<?php

// ========================================
// IMPROVED USERRESOURCE.PHP 
// ========================================

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['tenant', 'cashier']);
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->description('Basic user details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Full Name'),
                                    
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('email@example.com'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->placeholder('+63 912 345 6789'),
                                    
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255)
                                    ->helperText('Leave blank to keep current password when editing.'),
                            ]),
                    ]),

                Forms\Components\Section::make('Role & Access')
                    ->description('Define user permissions and assignments')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('role')
                                    ->label('Role')
                                    ->options([
                                        'tenant' => 'Tenant',
                                        'cashier' => 'Cashier',
                                    ])
                                    ->required()
                                    ->default('tenant')
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $record) {
                                        if ($record && $record->roles->isNotEmpty()) {
                                            $component->state($record->roles->first()->name);
                                        }
                                    })
                                    ->live()
                                    ->helperText('Select the user\'s primary role'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active Account')
                                    ->helperText('User can login and access the system')
                                    ->default(true),
                            ]),

                        Forms\Components\Select::make('admin_stall_id')
                            ->label('Assigned Stall')
                            ->relationship('adminStall', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('role') === 'tenant')
                            ->required(fn (Forms\Get $get) => $get('role') === 'tenant')
                            ->helperText('Which stall should this tenant manage?'),
                    ]),

                Forms\Components\Section::make('Additional Settings')
                    ->description('Optional configurations')
                    ->schema([
                        Forms\Components\Select::make('preferred_notification_channel')
                            ->label('Notification Preference')
                            ->options([
                                'email' => 'Email Only',
                                'sms' => 'SMS Only',
                                'both' => 'Both Email & SMS',
                            ])
                            ->default('email')
                            ->helperText('How should we send notifications?'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->email),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('No phone')
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tenant' => 'success',
                        'cashier' => 'info',
                        'admin' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('adminStall.name')
                    ->label('Stall')
                    ->placeholder('None assigned')
                    ->badge()
                    ->color('primary')
                    ->url(fn ($record) => $record->adminStall ? 
                        route('filament.admin.resources.stalls.view', $record->adminStall) : null),

                Tables\Columns\ViewColumn::make('status_indicator')
                    ->label('Status')
                    ->view('components.user-status'),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->since()
                    ->placeholder('Never')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'tenant' => 'Tenants',
                        'cashier' => 'Cashiers',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $role): Builder => $query->whereHas('roles', function ($q) use ($role) {
                                $q->where('name', $role);
                            }),
                        );
                    }),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Account Status')
                    ->placeholder('All users')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\SelectFilter::make('stall')
                    ->label('Assigned Stall')
                    ->relationship('adminStall', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('recent_login')
                    ->label('Active Users')
                    ->query(fn (Builder $query) => 
                        $query->where('last_login_at', '>=', now()->subDays(30))
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                            $status = $record->is_active ? 'activated' : 'deactivated';
                            Notification::make()->title("User {$status} successfully")->success()->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('send_welcome')
                        ->label('Send Welcome Email')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->visible(fn ($record) => $record->is_active && !$record->email_verified_at)
                        ->action(function ($record) {
                            // Add your welcome email logic here
                            Notification::make()->title('Welcome email sent')->success()->send();
                        }),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No users found')
            ->emptyStateDescription('Start by creating your first tenant or cashier user.')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add User')
                    ->icon('heroicon-o-plus'),
            ]);
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