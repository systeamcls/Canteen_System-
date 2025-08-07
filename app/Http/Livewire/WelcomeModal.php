<?php

namespace App\Http\Livewire;

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
        $this->emit('userTypeUpdated', 'guest');
        
        // Redirect to menu
        return redirect()->route('menu.index');
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
                $user = Auth::user();
                if (!$user->hasAnyRole(['admin', 'tenant', 'cashier', 'customer'])) {
                    $user->assignRole('customer');
                }
                
                session(['user_type' => 'employee']);
                $this->close();
                
                // Emit event for other components
                $this->emit('userTypeUpdated', 'employee');
                
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
            $this->validate([
                'registerName' => 'required|string|max:255',
                'registerEmail' => 'required|string|email|max:255|unique:users,email',
                'registerPassword' => 'required|string|min:8|confirmed',
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

            // Assign customer role
            $user->assignRole('customer');

            // Log the user in
            Auth::login($user);
            
            session(['user_type' => 'employee']);
            $this->close();
            
            // Emit event for other components
            $this->emit('userTypeUpdated', 'employee');
            
            // Redirect to dashboard
            return redirect()->route('dashboard');
            
        } catch (ValidationException $e) {
            $this->registerError = 'Please check your input and try again.';
        } catch (\Exception $e) {
            $this->registerError = 'An error occurred during registration. Please try again.';
        }
    }

    public function render()
    {
        return view('livewire.welcome-modal');
    }
}