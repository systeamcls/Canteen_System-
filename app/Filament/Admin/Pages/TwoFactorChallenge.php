<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Illuminate\Support\Facades\Log;

class TwoFactorChallenge extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.admin.pages.two-factor-challenge';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '2FA Verification';
    protected static string $routePath = 'two-factor-challenge';

    public ?string $code = null;
    public bool $recovery = false;

    public function mount(): void
    {
        // Check if user is logged in
        if (!Auth::check()) {
            redirect()->route('filament.admin.auth.login');
            return;
        }

        /** @var User $user */
        $user = Auth::user();
        
        // If 2FA not enabled, redirect to dashboard
        if (!$user->hasEnabledTwoFactorAuthentication()) {
            redirect()->route('filament.admin.pages.dashboard');
            return;
        }

        // If already verified this session, redirect to dashboard
        if (session('auth.two_factor_confirmed_at')) {
            redirect()->route('filament.admin.pages.dashboard');
            return;
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
return $form->schema([
            TextInput::make('code')
    ->label($this->recovery ? 'Recovery Code' : 'Authentication Code')
    ->placeholder($this->recovery ? 'Enter recovery code' : 'Enter 6-digit code')
    ->required()
    ->autofocus()
    ->maxLength($this->recovery ? 30 : 6)
    ->minLength($this->recovery ? 8 : 6)
    ->rules($this->recovery ? ['string'] : ['required', 'string', 'size:6'])
    ->helperText($this->recovery 
        ? 'Enter one of your recovery codes' 
        : 'Enter the code from your authenticator app'
    ),
        ]);
    }

    public function verify(): void
    {
        $this->validate();
        
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            $this->logout();
            return;
        }

        $confirmed = false;

        if ($this->recovery) {
            $confirmed = $this->verifyRecoveryCode($this->code);
        } else {
            try {
                $secret = decrypt($user->two_factor_secret);
                $confirmed = app(TwoFactorAuthenticationProvider::class)
                    ->verify($secret, $this->code);
            } catch (\Exception $e) {
                Log::error('2FA Verification Error: ' . $e->getMessage());
            }
        }

        if ($confirmed) {
            session(['auth.two_factor_confirmed_at' => now()]);
            session()->regenerate();
            
            Notification::make()
                ->title('Verification Successful')
                ->body('Welcome back!')
                ->success()
                ->send();

            redirect()->route('filament.admin.pages.dashboard');
            return;
        }

        $this->code = null;
        
        Notification::make()
            ->title('Verification Failed')
            ->body($this->recovery 
                ? 'Invalid recovery code. Please try again.' 
                : 'Invalid authentication code. Please try again or use a recovery code.'
            )
            ->danger()
            ->send();
    }

    private function verifyRecoveryCode(string $code): bool
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user || !$user->two_factor_recovery_codes) {
            return false;
        }
        
        try {
            $decrypted = decrypt($user->two_factor_recovery_codes);
            $codes = is_string($decrypted) ? json_decode($decrypted, true) : $decrypted;
            
            if (!is_array($codes)) {
                return false;
            }

            $normalizedInput = strtolower(str_replace([' ', '-'], '', trim($code)));
            
            foreach ($codes as $index => $recoveryCode) {
                $normalizedRecovery = strtolower(str_replace([' ', '-'], '', trim((string)$recoveryCode)));
                
                if ($normalizedInput === $normalizedRecovery) {
                    unset($codes[$index]);
                    $codes = array_values($codes);
                    
                    $user->forceFill([
                        'two_factor_recovery_codes' => encrypt(json_encode($codes)),
                    ])->save();
                    
                    Notification::make()
                        ->title('Recovery Code Used')
                        ->body('You have ' . count($codes) . ' recovery codes remaining.')
                        ->warning()
                        ->send();
                    
                    return true;
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Recovery Code Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleRecovery(): void
    {
        $this->recovery = !$this->recovery;
        $this->code = null;
        $this->form->fill();
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        redirect()->route('filament.admin.auth.login');
    }

    public function hasLogo(): bool
{
    return false;
}

    protected function getFormActions(): array
{
    return [
        \Filament\Actions\Action::make('verify')
            ->label('Verify & Continue')
            ->submit('verify')
            ->color('primary')
            ->icon('heroicon-o-check-circle')
            ->size('lg')
            ->extraAttributes(['class' => 'w-full justify-center']),
            
        \Filament\Actions\Action::make('logout')
            ->label('Cancel & Logout')
            ->color('gray')
            ->icon('heroicon-o-arrow-left-on-rectangle')
            ->action('logout')
            ->size('lg')
            ->outlined()
            ->extraAttributes(['class' => 'w-full justify-center']),
    ];
}
    public function getTitle(): string
    {
        return '2FA Verification Required';
    }

    public function getHeading(): string
    {
        return '🔐 Two-Factor Authentication';
    }
}