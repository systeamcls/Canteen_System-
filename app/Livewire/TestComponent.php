<?php

namespace App\Livewire;

use Livewire\Component;

class TestComponent extends Component
{
    public $message = 'Livewire is working!';
    
    public function render()
    {
        return view('livewire.test-component');
    }
}