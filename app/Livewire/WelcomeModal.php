<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Helpers\RecaptchaHelper;
use Illuminate\Support\Facades\Log;

class WelcomeModal extends Component
{
    public $showModal = false;
    public $currentView = 'options'; // options, employee-form, register-form

    // Login form properties
    public $email = '';
    public $password = '';
    public $loginError = '';

    // reCAPTCHA tokens
    public $recaptcha_token_guest = '';
    public $recaptcha_token_login = '';
    public $recaptcha_token_register = '';

    // Registration form properties
    public $registerName = '';
    public $registerEmail = '';
    public $registerPassword = '';
    public $registerPasswordConfirmation = '';
    public $registerPhone = '';
    public $registerError = '';
    
    public $acceptTerms = false;

    protected $listeners = ['openWelcomeModal' => 'open'];

    public function mount()
    {
        // Auto-open modal if requested from session
        if (session('showModal')) {
            $this->showModal = true;
            session()->forget('showModal');
        }
        
        // Set login message if exists
        if (session('loginMessage')) {
            $this->loginError = session('loginMessage');
            session()->forget('loginMessage');
        }
    }

    public function open()
    {
        $this->showModal = true;
        $this->currentView = 'options';
        $this->resetForm();
    }

    public function close()
    {
        $this->showModal = false;
        $this->currentView = 'options';
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'email', 'password', 'loginError',
            'registerName', 'registerEmail', 'registerPassword', 
            'registerPasswordConfirmation', 'registerPhone', 'registerError',
            'acceptTerms'
        ]);
    }

    public function showEmployeeForm()
    {
        $this->currentView = 'employee-form';
        $this->resetForm();
    }

    public function showRegisterForm()
    {
        $this->currentView = 'register-form';
        $this->resetForm();
    }

    public function showGuestVerification()
    {
        $this->currentView = 'guest-verification';
        $this->resetForm();
    }

    public function showOptions()
    {
        $this->currentView = 'options';
        $this->resetForm();
    }

    public function loginAsGuest()
    {
        // Verify reCAPTCHA v2 Checkbox
        if (!RecaptchaHelper::verifyV2($this->recaptcha_token_guest, 'checkbox')) {
            $this->loginError = 'Security verification failed. Please complete the reCAPTCHA and try again.';
            $this->recaptcha_token_guest = '';
            return;
        }

        // Set guest session
        session([
            'user_type' => 'guest',
            'guest_session_id' => uniqid('guest_', true)
        ]);

        $this->close();

        // Emit event for other components
        $this->dispatch('userTypeUpdated', 'guest');

        // Redirect to menu
        return redirect()->route('home.index');
    }

    public function loginAsEmployee()
    {
        $this->loginError = '';

        // 🔥 SECURITY: Rate limiting by IP
        $rateLimitKey = 'login-attempt:' . request()->ip();
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            
            $this->loginError = "Too many login attempts. Please try again in {$minutes} minute(s).";
            
            // 🔥 LOG rate limit hit
            Log::warning('Rate limit exceeded for login', [
                'ip' => request()->ip(),
                'email_attempted' => $this->email,
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]);
            
            return;
        }

        // Verify reCAPTCHA v2 Checkbox
        if (!RecaptchaHelper::verifyV2($this->recaptcha_token_login, 'checkbox')) {
            $this->loginError = 'Security verification failed. Please complete the reCAPTCHA and try again.';
            $this->recaptcha_token_login = '';
            
            // 🔥 Count failed attempt due to reCAPTCHA
            RateLimiter::hit($rateLimitKey, 300); // 5 minutes
            
            Log::warning('reCAPTCHA verification failed', [
                'ip' => request()->ip(),
                'email' => $this->email,
                'timestamp' => now(),
            ]);
            
            return;
        }

        try {
            $this->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                // 🔥 SECURITY: Clear rate limiter on successful login
                RateLimiter::clear($rateLimitKey);
                
                // 🔥 SECURITY: Regenerate session to prevent session fixation
                request()->session()->regenerate();
                
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                // 🔥 LOG successful login
                Log::info('Successful login', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->roles->pluck('name')->first() ?? 'customer',
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'timestamp' => now(),
                ]);
                
                // 🔥 Role-based redirection for panel access
                if ($user->hasRole('admin')) {
                    session(['user_type' => 'admin']);
                    $this->close();
                    return $this->redirectToPanelWithTwoFactor('admin');
                    
                } elseif ($user->hasRole('tenant')) {
                    session(['user_type' => 'tenant']);
                    $this->close();
                    return $this->redirectToPanelWithTwoFactor('tenant');
                    
                } elseif ($user->hasRole('cashier')) {
                    session(['user_type' => 'cashier']);
                    $this->close();
                    // Cashier goes directly - no 2FA for fast POS access
                    return redirect('/' . config('app.cashier_prefix', 'cashier'));
                    
                } else {
                    // Regular customer/employee flow (existing functionality preserved)
                    if (!$user->hasAnyRole(['admin', 'tenant', 'cashier', 'customer'])) {
                        $user->assignRole('customer');
                    }
                    
                    session(['user_type' => 'employee']);
                    $this->close();
                    
                    // Emit event for other components
                    $this->dispatch('userTypeUpdated', 'employee');
                    
                    // Check if there was an intended redirect
                    if (session('redirectIntended')) {
                        $url = session('redirectIntended');
                        session()->forget('redirectIntended');
                        return redirect($url);
                    }
                    
                    // Default redirect to profile
                    return $this->redirectRoute('user.profile.show');
                }

            } else {
                // 🔥 SECURITY: Count failed login attempt
                RateLimiter::hit($rateLimitKey, 300); // Lock for 5 minutes after 5 attempts
                
                $attempts = RateLimiter::attempts($rateLimitKey);
                $remaining = 5 - $attempts;
                
                // 🔥 LOG failed login attempt
                Log::warning('Failed login attempt', [
                    'email' => $this->email,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'attempts' => $attempts,
                    'remaining' => $remaining,
                    'timestamp' => now(),
                ]);
                
                if ($remaining > 0) {
                    $this->loginError = "Invalid email or password. {$remaining} attempt(s) remaining.";
                } else {
                    $this->loginError = 'Too many failed attempts. Account temporarily locked for 5 minutes.';
                }
            }
        } catch (ValidationException $e) {
            $this->loginError = 'Please fill in all required fields.';
        } catch (\Exception $e) {
            $this->loginError = 'An error occurred. Please try again.';
            
            Log::error('Login exception in WelcomeModal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $this->email,
                'ip' => request()->ip(),
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * 🔥 Redirect to panel dashboard with 2FA check
     */
    protected function redirectToPanelWithTwoFactor(string $role)
    {
        $prefix = config("app.{$role}_prefix", $role);
        
        // Redirect to the panel (Filament middleware will handle 2FA challenge if needed)
        return redirect("/{$prefix}");
    }

    public function registerEmployee()
    {
        $this->registerError = '';

        // 🔥 SECURITY: Rate limiting for registration
        $rateLimitKey = 'register-attempt:' . request()->ip();
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            
            $this->registerError = "Too many registration attempts. Please try again in {$minutes} minute(s).";
            return;
        }

        // Verify reCAPTCHA v2 Checkbox FIRST
        if (!RecaptchaHelper::verifyV2($this->recaptcha_token_register, 'checkbox')) {
            $this->registerError = 'Security verification failed. Please complete the reCAPTCHA and try again.';
            $this->recaptcha_token_register = '';
            
            RateLimiter::hit($rateLimitKey, 600); // 10 minutes
            return;
        }

        try {
            $validated = $this->validate([
                'registerName' => 'required|string|max:255',
                'registerEmail' => 'required|string|email|max:255|unique:users,email',
                'registerPassword' => 'required|string|min:8',
                'registerPasswordConfirmation' => 'required|string|min:8|same:registerPassword',
                'registerPhone' => 'nullable|string|max:20',
                'acceptTerms' => 'accepted',
            ], [
                'acceptTerms.accepted' => 'You must accept the Terms and Conditions and Privacy Policy to register.',
            ]);

            // Create the user
            $user = User::create([
                'name' => $this->registerName,
                'email' => $this->registerEmail,
                'password' => Hash::make($this->registerPassword),
                'phone' => $this->registerPhone,
                'type' => 'employee',
                'is_active' => true,
                'verification_sent_at' => now(),
                'terms_accepted_at' => now(),
            ]);

            // Check if Spatie roles package is installed
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('customer');
            }

            // Send verification email
            $user->sendEmailVerificationNotification();

            // 🔥 SECURITY: Clear rate limit on successful registration
            RateLimiter::clear($rateLimitKey);
            
            // 🔥 LOG successful registration
            Log::info('New user registered', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]);

            // Log the user in
            Auth::login($user);
            
            // 🔥 SECURITY: Regenerate session after registration
            request()->session()->regenerate();
            
            session(['user_type' => 'employee']);
            
            $this->close();

            // Redirect to verification notice
            $this->redirectRoute('verification.notice');

        } catch (ValidationException $e) {
            RateLimiter::hit($rateLimitKey, 600);
            $this->registerError = 'Validation failed: ' . implode(', ', $e->validator->errors()->all());
        } catch (\Exception $e) {
            RateLimiter::hit($rateLimitKey, 600);
            $this->registerError = 'Registration failed: ' . $e->getMessage();
            
            Log::error('Registration error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $this->registerEmail,
                'ip' => request()->ip(),
                'timestamp' => now(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.welcome-modal');
    }
}