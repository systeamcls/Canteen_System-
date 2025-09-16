<?php

namespace App\Filament\Tenant\Pages;

use App\Models\User;
use App\Models\Stall;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TenantProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.tenant.pages.tenant-profile';
    protected static ?string $title = 'My Profile';
    protected static ?string $navigationGroup = null; 
    protected static ?string $navigationLabel = 'Profile';
    protected static ?int $navigationSort = 6;

    public ?array $profileData = [];
    public ?array $stallData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        $this->profileForm->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'preferred_notification_channel' => $user->preferred_notification_channel,
        ]);

        if ($stall) {
            $this->stallForm->fill([
                'name' => $stall->name,
                'description' => $stall->description,
                'logo' => $stall->logo,
                'contact_number' => $stall->contact_number,
                'opening_time' => $stall->opening_time,
                'closing_time' => $stall->closing_time,
            ]);
        }
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Update your account details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Your full name'),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->placeholder('your.email@example.com'),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+63 912 345 6789')
                            ->helperText('Your personal contact number'),

                        TextInput::make('preferred_notification_channel')
                            ->label('Notification Preference')
                            ->placeholder('email')
                            ->disabled()
                            ->helperText('Notification settings (managed by system)'),
                    ])
                    ->columns(2),
            ])
            ->statePath('profileData');
    }

    public function stallForm(Form $form): Form
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        if (!$stall) {
            return $form->schema([
                Section::make('No Stall Assigned')
                    ->description('Contact the canteen administrator to get assigned to a stall')
                    ->schema([
                        TextInput::make('no_stall')
                            ->label('Status')
                            ->default('No stall assigned')
                            ->disabled(),
                    ]),
            ]);
        }

        return $form
            ->schema([
                Section::make('Stall Information')
                    ->description('Manage your stall details and settings')
                    ->schema([
                        TextInput::make('name')
                            ->label('Stall Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Your stall name as it appears to customers'),

                        Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Describe your stall, cuisine type, and what makes it special')
                            ->placeholder('Authentic Filipino dishes made with love...'),

                        FileUpload::make('logo')
                            ->label('Stall Logo')
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
                            ->helperText('Square logo recommended, max 2MB')
                            ->columnSpanFull(),

                        TextInput::make('contact_number')
                            ->label('Stall Contact Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+63 912 345 6789')
                            ->helperText('Customer contact number for your stall'),

                        TimePicker::make('opening_time')
                            ->seconds(false)
                            ->helperText('When do you start serving customers?'),

                        TimePicker::make('closing_time')
                            ->seconds(false)
                            ->helperText('When do you stop serving customers?'),
                    ])
                    ->columns(2),

                Section::make('Stall Status (Read Only)')
                    ->description('These settings are managed by the canteen administrator')
                    ->schema([
                        TextInput::make('location')
                            ->default($stall->location)
                            ->disabled()
                            ->helperText('Stall location in the canteen'),

                        TextInput::make('rental_fee')
                            ->default('PHP ' . number_format($stall->rental_fee ?? 0, 2))
                            ->disabled()
                            ->helperText('Monthly rental fee'),

                        TextInput::make('commission_rate')
                            ->default(($stall->commission_rate ?? 0) . '%')
                            ->disabled()
                            ->helperText('Commission rate on sales'),

                        TextInput::make('is_active')
                            ->label('Stall Status')
                            ->default($stall->is_active ? 'Active' : 'Inactive')
                            ->disabled()
                            ->helperText('Current stall status'),
                    ])
                    ->columns(2),
            ])
            ->statePath('stallData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Change Password')
                    ->description('Update your account password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required()
                            ->currentPassword()
                            ->helperText('Enter your current password to confirm changes'),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->rule(Password::default())
                            ->confirmed()
                            ->helperText('Choose a strong password'),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->required()
                            ->helperText('Re-enter your new password'),
                    ]),
            ])
            ->statePath('passwordData');
    }

    public function updateProfile(): void
    {
        $data = $this->profileForm->getState();
        
        /** @var User $user */
        $user = Auth::user();
        
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        Notification::make()
            ->title('Profile Updated')
            ->body('Your profile information has been updated successfully.')
            ->success()
            ->send();
    }

    public function updateStall(): void
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        if (!$stall) {
            Notification::make()
                ->title('No Stall Assigned')
                ->body('You need to be assigned to a stall first.')
                ->warning()
                ->send();
            return;
        }

        $data = $this->stallForm->getState();
        
        $stall->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'logo' => $data['logo'],
            'contact_number' => $data['contact_number'],
            'opening_time' => $data['opening_time'],
            'closing_time' => $data['closing_time'],
        ]);

        Notification::make()
            ->title('Stall Updated')
            ->body('Your stall information has been updated successfully.')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $data = $this->passwordForm->getState();
        
        /** @var User $user */
        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        $this->passwordForm->fill([]);

        Notification::make()
            ->title('Password Updated')
            ->body('Your password has been changed successfully.')
            ->success()
            ->send();
    }

    protected function getForms(): array
    {
        return [
            'profileForm',
            'stallForm', 
            'passwordForm',
        ];
    }

    public function getTitle(): string
    {
        return 'Profile & Stall Management';
    }
}