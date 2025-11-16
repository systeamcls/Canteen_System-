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
use Illuminate\Support\Facades\Log;

class TwoFactorAuthentication extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static string $view = 'filament.tenant.pages.two-factor-authentication';
    protected static ?string $title = '2FA Security';
    protected static ?string $navigationGroup = null; 
    protected static ?string $navigationLabel = '2FA Security';
    protected static ?int $navigationSort = 7;

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
            // 🔥 Description first
            Placeholder::make('description')
                ->label('About 2FA')
                ->content('Two-factor authentication adds extra security to your tenant account by requiring a code from your phone in addition to your password.'),

            // 🔥 SINGLE QR Code (removed duplicate)
            Placeholder::make('qr_code')
                ->label('QR Code')
                ->content(function () use ($user) {
                    if ($this->showingQrCode && $user->two_factor_secret) {
                        try {
                            $qrCodeSvg = $user->twoFactorQrCodeSvg();
                            
                            // Make sure we have a valid SVG string
                            if (is_string($qrCodeSvg) && !empty($qrCodeSvg)) {
                                return new HtmlString('
                                    <div class="text-center p-6 bg-white rounded-lg border-2 border-blue-200">
                                        ' . $qrCodeSvg . '
                                        <p class="mt-4 text-sm text-gray-600 font-medium">
                                            📱 Scan this QR code with Google Authenticator or Authy
                                        </p>
                                    </div>
                                ');
                            }
                            
                            // Fallback if QR code generation fails
                            return new HtmlString('
                                <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-red-600">❌ QR Code generation failed. Please try again.</p>
                                </div>
                            ');
                        } catch (\Exception $e) {
                            // Error handling with logging
                            Log::error('2FA QR Code generation failed', [
                                'user_id' => $user->id,
                                'error' => $e->getMessage(),
                            ]);
                            
                            return new HtmlString('
                                <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-red-600">❌ Error generating QR code: ' . e($e->getMessage()) . '</p>
                                    <p class="text-sm text-red-500 mt-2">Please try clicking "Enable 2FA" again.</p>
                                </div>
                            ');
                        }
                    }
                    return '';
                })
                ->visible(fn () => $this->showingQrCode),

            // 🔥 Verification code input
            TextInput::make('code')
                ->label('Verification Code')
                ->placeholder('Enter 6-digit code from your app')
                ->maxLength(6)
                ->minLength(6)
                ->numeric()
                ->required()
                ->visible(fn () => $this->showingConfirmation)
                ->helperText('Enter the 6-digit code from your authenticator app'),

            // 🔥 Recovery codes
            Placeholder::make('recovery_codes')
                ->label('Recovery Codes')
                ->content(function () use ($user) {
                    if ($this->showingRecoveryCodes && $user->two_factor_recovery_codes) {
                        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
                        $codesList = collect($codes)->map(function($code) {
                            return '<div class="font-mono text-sm bg-gray-100 px-3 py-2 rounded mb-2">' . e($code) . '</div>';
                        })->join('');
                        
                        return new HtmlString('
                            <div class="p-4 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
                                <p class="font-bold text-yellow-800 mb-3 text-lg">⚠️ Save These Recovery Codes!</p>
                                <p class="text-sm text-yellow-700 mb-3">Store these codes in a safe place. Each can only be used once to access your account if you lose your phone.</p>
                                ' . $codesList . '
                                <p class="text-xs text-yellow-600 mt-3 font-medium">💾 Download or write these down NOW. You won\'t see them again!</p>
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
        
        try {
            // Generate new 2FA secret
            $secret = app(TwoFactorAuthenticationProvider::class)->generateSecretKey();
            
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
            ])->save();

            $this->showingQrCode = true;
            $this->showingConfirmation = true;
            
            Notification::make()
                ->title('2FA Setup Started')
                ->body('Scan the QR code with your authenticator app, then enter the 6-digit code below.')
                ->info()
                ->duration(5000)
                ->send();
                
            Log::info('2FA setup initiated', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
        } catch (\Exception $e) {
            Log::error('2FA enable failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            Notification::make()
                ->title('Error')
                ->body('Failed to enable 2FA. Please try again.')
                ->danger()
                ->send();
        }
    }

    public function confirmTwoFactorAuthentication(): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$this->code || strlen($this->code) !== 6) {
            Notification::make()
                ->title('Invalid Code')
                ->body('Please enter a 6-digit code.')
                ->danger()
                ->send();
            return;
        }
        
        try {
            $confirmed = app(TwoFactorAuthenticationProvider::class)->verify(
                decrypt($user->two_factor_secret), 
                $this->code
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
                
                Log::info('2FA enabled successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                Notification::make()
                    ->title('✅ 2FA Enabled Successfully!')
                    ->body('Please save your recovery codes below. You will need them if you lose access to your authenticator app.')
                    ->success()
                    ->duration(10000)
                    ->send();
            } else {
                Notification::make()
                    ->title('❌ Invalid Code')
                    ->body('The code you entered is incorrect. Please try again.')
                    ->danger()
                    ->send();
                    
                Log::warning('2FA confirmation failed - invalid code', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('2FA confirmation error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            Notification::make()
                ->title('Error')
                ->body('An error occurred. Please try again.')
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
        
        Log::info('2FA disabled', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
        
        Notification::make()
            ->title('2FA Disabled')
            ->body('Two-factor authentication has been disabled for your account.')
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
                ->modalHeading('Disable Two-Factor Authentication?')
                ->modalDescription('Are you sure you want to disable 2FA? This will make your account less secure.')
                ->modalSubmitActionLabel('Yes, Disable 2FA')
                ->action('disableTwoFactorAuthentication')
                ->visible(fn () => $user->hasEnabledTwoFactorAuthentication()),
                
            \Filament\Actions\Action::make('confirm')
                ->label('Confirm Code')
                ->color('primary')
                ->icon('heroicon-o-check-circle')
                ->action('confirmTwoFactorAuthentication')
                ->visible(fn () => $this->showingConfirmation),
        ];
    }
}