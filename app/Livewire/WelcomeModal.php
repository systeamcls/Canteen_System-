<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class WelcomeModal extends Component
{
    public $showModal = false;
    public $currentView = 'options'; // options, employee-form, register-form

    // Login form properties
    public $email = '';
    public $password = '';
    public $loginError = '';

    // Registration form properties
    public $registerName = '';
    public $registerEmail = '';
    public $registerPassword = '';
    public $registerPasswordConfirmation = '';
    public $registerPhone = '';
    public $registerError = '';

    protected $listeners = ['openWelcomeModal' => 'open'];

    public function mount()
    {
        // Auto-open modal if requested from session
        if (session('showModal')) {
            $this->showModal = true;
            session()->forget('showModal');
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
            'registerPasswordConfirmation', 'registerPhone', 'registerError'
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

    public function showOptions()
    {
        $this->currentView = 'options';
        $this->resetForm();
    }

    public function loginAsGuest()
    {
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

        try {
            $this->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                // Assign customer role if not already assigned
                /** @var \App\Models\User $user */
                $user = Auth::user();
                if (!$user->hasAnyRole(['admin', 'tenant', 'cashier', 'customer'])) {
                    $user->assignRole('customer');
                }

                session(['user_type' => 'employee']);
                $this->close();

                // Emit event for other components
                $this->dispatch('userTypeUpdated', 'employee');

                // Redirect to dashboard or menu
                return redirect()->route('dashboard');
            } else {
                $this->loginError = 'Invalid email or password. Please try again.';
            }
        } catch (ValidationException $e) {
            $this->loginError = 'Please fill in all required fields.';
        } catch (\Exception $e) {
            $this->loginError = 'An error occurred. Please try again.';
        }
    }

    public function registerEmployee()
{
    $this->registerError = '';

    try {
        $validated = $this->validate([
            'registerName' => 'required|string|max:255',
            'registerEmail' => 'required|string|email|max:255|unique:users,email',
            'registerPassword' => 'required|string|min:8',
            'registerPasswordConfirmation' => 'required|string|min:8|same:registerPassword',
            'registerPhone' => 'nullable|string|max:20',
        ]);

        // Create the user
        $user = User::create([
            'name' => $this->registerName,
            'email' => $this->registerEmail,
            'password' => Hash::make($this->registerPassword),
            'phone' => $this->registerPhone,
            'type' => 'employee',
            'is_active' => true,
        ]);

        // Check if Spatie roles package is installed
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('customer');
        }

        // Log the user in
        Auth::login($user);

        session(['user_type' => 'employee']);
        $this->close();

        // Redirect to dashboard
        return redirect()->route('dashboard');

    } catch (ValidationException $e) {
        $this->registerError = 'Validation failed: ' . implode(', ', $e->validator->errors()->all());
    } catch (\Exception $e) {
        $this->registerError = 'Registration failed: ' . $e->getMessage();
        \Illuminate\Support\Facades\Log::error('Registration error: ' . $e->getMessage());
    }
}

    public function render()
    {
        return view('livewire.welcome-modal');
    }
}