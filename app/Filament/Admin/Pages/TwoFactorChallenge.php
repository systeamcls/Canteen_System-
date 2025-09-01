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

class TwoFactorChallenge extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.admin.pages.two-factor-challenge';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '2FA Verification Required';

    public ?string $code = null;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user || !$user->hasEnabledTwoFactorAuthentication()) {
            $this->redirect('/admin');
            return;
        }

        if (session('auth.two_factor_confirmed_at')) {
            $this->redirect('/admin');
            return;
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')
                ->label('Verification Code')
                ->placeholder('Enter 6-digit code from your app')
                ->required()
                ->autofocus()
                ->maxLength(8)
                ->minLength(6),
        ]);
    }

    public function verify(): void
    {
        $data = $this->form->getState();
        
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            $this->redirect('/admin/login');
            return;
        }

        // Try TOTP verification
        $confirmed = app(TwoFactorAuthenticationProvider::class)->verify(
            decrypt($user->two_factor_secret), 
            $data['code']
        );

        // Try recovery code if TOTP failed
        if (!$confirmed) {
            $confirmed = $this->verifyRecoveryCode($data['code']);
        }

        if ($confirmed) {
            session(['auth.two_factor_confirmed_at' => now()]);
            
            Notification::make()
                ->title('Access Granted')
                ->success()
                ->send();

            $this->redirect('/admin');
            return;
        }

        $this->addError('code', 'Invalid code. Please try again.');
    }

    private function verifyRecoveryCode(string $code): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user || !$user->two_factor_recovery_codes) {
            return false;
        }
        
        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        
        if (in_array($code, $codes)) {
            // Remove used recovery code
            $remainingCodes = array_values(array_diff($codes, [$code]));
            $user->forceFill([
                'two_factor_recovery_codes' => encrypt(json_encode($remainingCodes)),
            ])->save();
            
            return true;
        }
        
        return false;
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/admin/login');
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('verify')
                ->label('Verify')
                ->submit('verify')
                ->color('primary'),
                
            \Filament\Actions\Action::make('logout')
                ->label('Logout')
                ->color('gray')
                ->action('logout'),
        ];
    }
}