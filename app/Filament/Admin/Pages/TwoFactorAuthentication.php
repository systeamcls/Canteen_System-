<?php

namespace App\Filament\Admin\Pages;

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
    protected static string $view = 'filament.admin.pages.two-factor-authentication';
    protected static ?string $title = '2FA Security';
    protected static ?string $navigationLabel = '2FA Security';
    protected static ?string $navigationGroup = 'System';
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
            // STATUS CHECK
            Placeholder::make('status')
                ->label('2FA Status')
                ->content(function () use ($user): HtmlString {
                    $isEnabled = false;
                    try {
                        $isEnabled = $user && method_exists($user, 'hasEnabledTwoFactorAuthentication') && $user->hasEnabledTwoFactorAuthentication();
                    } catch (\Exception $e) {
                        // Ignore errors
                    }
                    
                    if ($isEnabled) {
                        return new HtmlString('<div class="p-3 bg-green-100 rounded"><strong>Status:</strong> 2FA is Enabled</div>');
                    }
                    return new HtmlString('<div class="p-3 bg-red-100 rounded"><strong>Status:</strong> 2FA is Disabled</div>');
                }),

            // DESCRIPTION
            Placeholder::make('description')
                ->label('About 2FA')
                ->content(fn(): HtmlString => new HtmlString('<p>Two-factor authentication adds extra security to your account.</p>')),

            // QR CODE SECTION
            Placeholder::make('qr_manual_setup')
                ->label('Setup 2FA')
                ->content(function () use ($user): HtmlString {
                    if (!$this->showingQrCode || !$user || !$user->two_factor_secret) {
                        return new HtmlString('');
                    }
                    
                    try {
                        $secretKey = decrypt($user->two_factor_secret);
                        if (!is_string($secretKey) || empty($secretKey)) {
                            return new HtmlString('<div class="p-3 bg-red-100 rounded">Error: Invalid secret key</div>');
                        }
                        
                        $appName = config('app.name', 'Laravel App');
                        $userEmail = $user->email ?? 'user@example.com';
                        
                        // Create safe QR URL
                        $qrText = 'otpauth://totp/' . urlencode($appName) . ':' . urlencode($userEmail) . '?secret=' . $secretKey . '&issuer=' . urlencode($appName);
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrText);
                        
                        // Format secret safely
                        $chunks = str_split($secretKey, 4);
                        $formattedSecret = strtoupper(implode(' ', $chunks));
                        
                        return new HtmlString('
                            <div class="space-y-4">
                                <div class="text-center p-4 bg-white border rounded">
                                    <img src="' . htmlspecialchars($qrUrl) . '" alt="QR Code" style="width:200px;height:200px;" class="mx-auto">
                                    <p class="mt-2 text-sm">Scan with your authenticator app</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded">
                                    <p><strong>Manual Entry:</strong></p>
                                    <code class="block mt-2 p-2 bg-white border rounded">' . htmlspecialchars($formattedSecret) . '</code>
                                </div>
                            </div>
                        ');
                        
                    } catch (\Exception $e) {
                        return new HtmlString('<div class="p-3 bg-red-100 rounded">Error setting up 2FA: ' . htmlspecialchars($e->getMessage()) . '</div>');
                    }
                })
                ->visible(fn () => $this->showingQrCode),

            // CODE INPUT
            TextInput::make('code')
                ->label('Enter 6-digit code')
                ->placeholder('123456')
                ->maxLength(6)
                ->minLength(6)
                ->numeric()
                ->visible(fn () => $this->showingConfirmation)
                ->suffixAction(
                    Action::make('confirm')
                        ->label('Verify')
                        ->action('confirmTwoFactorAuthentication')
                ),

            // RECOVERY CODES - FIXED
Placeholder::make('recovery_codes')
    ->label('Recovery Codes')
    ->content(function () use ($user): HtmlString {
        if (!$this->showingRecoveryCodes || !$user || !$user->two_factor_recovery_codes) {
            return new HtmlString('');
        }

        try {
            $recoveryData = $user->two_factor_recovery_codes;
            $decrypted = decrypt($recoveryData);

            // Handle both string and array formats
            if (is_string($decrypted)) {
                $codes = json_decode($decrypted, true);
            } elseif (is_array($decrypted)) {
                $codes = $decrypted;
            } else {
                return new HtmlString('<div class="p-3 bg-yellow-100 rounded">Invalid recovery data format</div>');
            }

            if (!is_array($codes) || empty($codes)) {
                return new HtmlString('<div class="p-3 bg-yellow-100 rounded">No recovery codes available</div>');
            }

            // Build codes list safely
            $codesList = array_map(function($code) {
                // Handle nested arrays or objects
                if (is_array($code)) {
                    $code = reset($code);
                }
                return is_string($code) ? trim($code) : (string)$code;
            }, $codes);

            // Filter valid codes
            $validCodes = array_filter($codesList, function($code) {
                return !empty($code) && strlen($code) >= 4;
            });

            if (empty($validCodes)) {
                return new HtmlString('<div class="p-3 bg-yellow-100 rounded">No valid recovery codes found</div>');
            }

            // Take first 8 codes
            $displayCodes = array_slice($validCodes, 0, 8);

            $codeHtml = '';
            foreach ($displayCodes as $code) {
                $codeHtml .= '<div class="p-2 bg-gray-100 rounded font-mono text-sm mb-1">' 
                           . htmlspecialchars($code) . '</div>';
            }

            return new HtmlString('
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="font-bold mb-3 text-yellow-800">⚠️ Recovery Codes</p>
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        ' . $codeHtml . '
                    </div>
                    <p class="text-xs text-gray-600">💾 Save these codes safely. Each can only be used once if you lose access to your authenticator app.</p>
                </div>
            ');

        } catch (\Exception $e) {
            Log::error('2FA Recovery Codes Error: ' . $e->getMessage());
            return new HtmlString('<div class="p-3 bg-red-100 rounded">Error displaying codes. Please regenerate them.</div>');
        }
    })
    ->visible(fn () => $this->showingRecoveryCodes),
        ]);
    }

    public function enableTwoFactorAuthentication(): void
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                throw new \Exception('User not found');
            }
            
            $secret = app(TwoFactorAuthenticationProvider::class)->generateSecretKey();
            
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
            ])->save();

            $this->showingQrCode = true;
            $this->showingConfirmation = true;
            
            Notification::make()
                ->title('2FA Setup Started')
                ->body('Scan the QR code and enter the verification code.')
                ->info()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to enable 2FA: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function confirmTwoFactorAuthentication(): void
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user || !$user->two_factor_secret || !$this->code) {
                throw new \Exception('Invalid setup - missing user, secret, or code');
            }

            // Validate code format
            if (!preg_match('/^\d{6}$/', $this->code)) {
                Notification::make()
                    ->title('Invalid Code Format')
                    ->body('Please enter a 6-digit numeric code.')
                    ->danger()
                    ->send();
                return;
            }

            $secret = decrypt($user->two_factor_secret);
            $confirmed = app(TwoFactorAuthenticationProvider::class)->verify($secret, $this->code);

            if (!$confirmed) {
                Notification::make()
                    ->title('Invalid Code')
                    ->body('Please check the code and try again.')
                    ->danger()
                    ->send();
                return;
            }

            // Generate recovery codes
            $recoveryCodes = RecoveryCode::generate();

            $user->forceFill([
                'two_factor_confirmed_at' => now(),
                'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            ])->save();

            $this->reset(['showingQrCode', 'showingConfirmation', 'code']);
            $this->showingRecoveryCodes = true;
            
            Notification::make()
                ->title('2FA Enabled!')
                ->body('Two-factor authentication is now active. Please save your recovery codes.')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to confirm 2FA: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function disableTwoFactorAuthentication(): void
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user) {
                throw new \Exception('User not found');
            }

            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();

            $this->reset(['showingQrCode', 'showingConfirmation', 'showingRecoveryCodes', 'code']);
            
            Notification::make()
                ->title('2FA Disabled')
                ->body('Two-factor authentication has been disabled.')
                ->warning()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to disable 2FA: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function regenerateRecoveryCodes(): void
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user || !$user->hasEnabledTwoFactorAuthentication()) {
                throw new \Exception('2FA not enabled');
            }

            $recoveryCodes = RecoveryCode::generate();
            
            $user->forceFill([
                'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            ])->save();

            $this->showingRecoveryCodes = true;
            
            Notification::make()
                ->title('Recovery Codes Regenerated')
                ->body('New recovery codes have been generated. The old codes are no longer valid.')
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to regenerate codes: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            $hasEnabled = $user && method_exists($user, 'hasEnabledTwoFactorAuthentication') && $user->hasEnabledTwoFactorAuthentication();
            
            $actions = [];
            
            if (!$hasEnabled) {
                $actions[] = \Filament\Actions\Action::make('enable')
                    ->label('Enable 2FA')
                    ->color('success')
                    ->icon('heroicon-o-shield-check')
                    ->action('enableTwoFactorAuthentication');
            } else {
                $actions[] = \Filament\Actions\Action::make('regenerate')
                    ->label('Show Recovery Codes')
                    ->color('warning')
                    ->icon('heroicon-o-key')
                    ->action('regenerateRecoveryCodes');
                    
                $actions[] = \Filament\Actions\Action::make('disable')
                    ->label('Disable 2FA')
                    ->color('danger')
                    ->icon('heroicon-o-shield-exclamation')
                    ->requiresConfirmation()
                    ->modalHeading('Disable Two-Factor Authentication')
                    ->modalDescription('Are you sure you want to disable two-factor authentication? This will make your account less secure.')
                    ->modalSubmitActionLabel('Yes, disable 2FA')
                    ->action('disableTwoFactorAuthentication');
            }
            
            return $actions;
            
        } catch (\Exception $e) {
            return [];
        }
    }
}