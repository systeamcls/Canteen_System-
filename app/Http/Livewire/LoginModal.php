<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginModal extends Component
{
    public $showModal = false;
    public $email = '';
    public $password = '';
    public $errorMessage = '';

    protected $listeners = ['openLoginModal' => 'open'];

    public function open()
    {
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
        $this->reset(['email', 'password', 'errorMessage']);
    }

    public function loginAsGuest()
    {
        session(['user_type' => 'guest']);
        $this->close();
        $this->emit('userTypeUpdated', 'guest');
    }

    public function loginAsEmployee()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session(['user_type' => 'employee']);
            $this->close();
            $this->emit('userTypeUpdated', 'employee');
            return redirect()->intended('/menu');
        }

        $this->errorMessage = 'Invalid credentials';
    }

    public function render()
    {
        return view('livewire.login-modal');
    }
}