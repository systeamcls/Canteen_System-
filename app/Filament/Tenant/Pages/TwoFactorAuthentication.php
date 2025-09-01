<?php

namespace App\Filament\Tenant\Pages;

use App\Models\User;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Features;
use Laravel\Fortify\RecoveryCode;

class TwoFactorAuthentication extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static string $view = 'filament.tenant.pages.two-factor-authentication';
    protected static ?string $title = '2FA Security';
    protected static ?string $navigationLabel = '2FA Security';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = 100;

    public ?string $code = null;
    public bool $showingQrCode = false;
    public bool $showingConfirmation = false;
    public bool $showingRecoveryCodes = false;

    public function mount(): void
    {
        if (!Features::enabled(Features::twoFactorAuthentication())) {
            abort(404);
        }
        
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        /** @var User $user */
        $user = Auth::user();
        
        return $form->schema([
            Placeholder::make('qr_code')
    ->label('QR Code')
    ->content(function () use ($user) {
        if ($this->showingQrCode && $user->two_factor_secret) {
            try {
                $qrCodeSvg = $user->twoFactorQrCodeSvg();
                
                // Make sure we have a valid SVG string
                if (is_string($qrCodeSvg) && !empty($qrCodeSvg)) {
                    return new HtmlString('
                        <div class="text-center p-4 bg-white rounded-lg border">
                            ' . $qrCodeSvg . '
                            <p class="mt-4 text-sm text-gray-600">
                                Scan this QR code with your authenticator app
                            </p>
                        </div>
                    ');
                }
                
                // Fallback if QR code generation fails
                return new HtmlString('
                    <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-600">QR Code generation failed. Please try again.</p>
                    </div>
                ');
            } catch (\Exception $e) {
                // Error handling
                return new HtmlString('
                    <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-600">Error generating QR code. Please try again.</p>
                    </div>
                ');
            }
        }
        return '';
    })
    ->visible(fn () => $this->showingQrCode),

            Placeholder::make('description')
                ->label('About 2FA')
                ->content('Two-factor authentication adds extra security to your tenant account by requiring a code from your phone in addition to your password.'),

            Placeholder::make('qr_code')
                ->label('QR Code')
                ->content(function () use ($user) {
                    if ($this->showingQrCode) {
                        return new HtmlString('
                            <div class="text-center p-4 bg-white rounded-lg border">
                                ' . $user->twoFactorQrCodeSvg() . '
                                <p class="mt-4 text-sm text-gray-600">
                                    Scan this QR code with your authenticator app
                                </p>
                            </div>
                        ');
                    }
                    return '';
                })
                ->visible(fn () => $this->showingQrCode),

            TextInput::make('code')
                ->label('Verification Code')
                ->placeholder('Enter 6-digit code')
                ->maxLength(6)
                ->minLength(6)
                ->visible(fn () => $this->showingConfirmation)
                ->suffixAction(
                    Action::make('confirm')
                        ->label('Confirm')
                        ->action('confirmTwoFactorAuthentication')
                ),

            Placeholder::make('recovery_codes')
                ->label('Recovery Codes')
                ->content(function () use ($user) {
                    if ($this->showingRecoveryCodes && $user->two_factor_recovery_codes) {
                        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
                        $codesList = collect($codes)->map(function($code) {
                            return '<div class="font-mono text-sm bg-gray-100 px-3 py-2 rounded mb-2">' . $code . '</div>';
                        })->join('');
                        
                        return new HtmlString('
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="font-medium text-yellow-800 mb-3">⚠️ Save These Recovery Codes</p>
                                ' . $codesList . '
                                <p class="text-xs text-yellow-600 mt-2">Store these safely. Each can only be used once.</p>
                            </div>
                        ');
                    }
                    return '';
                })
                ->visible(fn () => $this->showingRecoveryCodes),
        ]);
    }

    public function enableTwoFactorAuthentication(): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->forceFill([
            'two_factor_secret' => encrypt(app(TwoFactorAuthenticationProvider::class)->generateSecretKey()),
        ])->save();

        $this->showingQrCode = true;
        $this->showingConfirmation = true;
        
        Notification::make()
            ->title('2FA Setup Started')
            ->body('Scan the QR code and enter the verification code.')
            ->info()
            ->send();
    }

    public function confirmTwoFactorAuthentication(): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        $confirmed = app(TwoFactorAuthenticationProvider::class)->verify(
            decrypt($user->two_factor_secret), $this->code
        );

        if ($confirmed) {
            $user->forceFill([
                'two_factor_confirmed_at' => now(),
                'two_factor_recovery_codes' => encrypt(json_encode(RecoveryCode::generate())),
            ])->save();

            $this->showingQrCode = false;
            $this->showingConfirmation = false;
            $this->showingRecoveryCodes = true;
            $this->code = null;
            
            Notification::make()
                ->title('2FA Enabled Successfully!')
                ->body('Please save your recovery codes.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Invalid Code')
                ->body('Please try again.')
                ->danger()
                ->send();
        }
    }

    public function disableTwoFactorAuthentication(): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;
        
        Notification::make()
            ->title('2FA Disabled')
            ->body('Two-factor authentication has been disabled.')
            ->warning()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = Auth::user();
        
        return [
            \Filament\Actions\Action::make('enable')
                ->label('Enable 2FA')
                ->color('success')
                ->icon('heroicon-o-shield-check')
                ->action('enableTwoFactorAuthentication')
                ->visible(fn () => !$user->hasEnabledTwoFactorAuthentication()),

            \Filament\Actions\Action::make('disable')
                ->label('Disable 2FA')
                ->color('danger')
                ->icon('heroicon-o-shield-exclamation')
                ->requiresConfirmation()
                ->action('disableTwoFactorAuthentication')
                ->visible(fn () => $user->hasEnabledTwoFactorAuthentication()),
        ];
    }
}