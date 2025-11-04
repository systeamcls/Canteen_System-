<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeliveryOptionsModal extends Component
{
    public $isOpen = false;
    public $deliveryType = 'pickup'; // 'delivery' or 'pickup'
    public $deliveryTime = 'now'; // 'now' or 'scheduled'
    public $scheduledDate = '';
    public $scheduledTime = '';
    public $deliveryAddress = '';


    // Time slots (8 AM - 5 PM hourly)
    public function getTimeSlots()
    {
        return [
            '08:00' => '8:00 AM',
            '09:00' => '9:00 AM',
            '10:00' => '10:00 AM',
            '11:00' => '11:00 AM',
            '12:00' => '12:00 PM',
            '13:00' => '1:00 PM',
            '14:00' => '2:00 PM',
            '15:00' => '3:00 PM',
            '16:00' => '4:00 PM',
            '17:00' => '5:00 PM',
        ];
    }

    // Check email verification before opening
    #[On('openDeliveryModal')]
    public function checkAndOpen()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            session()->flash('error', 'Please login first');
            $this->dispatch('showWelcomeModal');
            return;
        }

        // Check if email is verified
        if (Auth::user()->email_verified_at === null) {
            session()->flash('error', 'Please verify your email before proceeding to checkout');
            return;
        }

        // All good, open modal
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['deliveryType', 'deliveryTime', 'scheduledDate', 'scheduledTime', 'deliveryAddress']);
    }

    public function setDeliveryType($type)
    {
        $this->deliveryType = $type;
    }

    public function setDeliveryTime($time)
    {
        $this->deliveryTime = $time;
        
        // Reset scheduled fields when switching to "now"
        if ($time === 'now') {
            $this->scheduledDate = '';
            $this->scheduledTime = '';
        }
    }

    public function confirm()
{
    // Only block unverified logged-in users
    // Guests and verified users can proceed
    if (Auth::check() && Auth::user()->email_verified_at === null) {
        session()->flash('error', 'Please verify your email before proceeding to checkout');
        return;
    }

    // Validation
    $this->validate([
        'deliveryType' => 'required|in:delivery,pickup',
        'deliveryTime' => 'required|in:now,scheduled',
        'deliveryAddress' => 'required_if:deliveryType,delivery',
        'scheduledDate' => 'required_if:deliveryTime,scheduled|date|after_or_equal:today',
        'scheduledTime' => 'required_if:deliveryTime,scheduled',
    ], [
        'deliveryAddress.required_if' => 'Please enter your delivery address',
        'scheduledDate.required_if' => 'Please select a date for your pre-order',
        'scheduledDate.after_or_equal' => 'Date must be today or in the future',
        'scheduledTime.required_if' => 'Please select a time for your pre-order',
    ]);

    // Calculate estimated ready time
    $estimatedReadyTime = null;
    $scheduledDatetime = null;

    if ($this->deliveryTime === 'now') {
        $estimatedReadyTime = now()->addMinutes(30);
    } else {
        $scheduledDatetime = $this->scheduledDate . ' ' . $this->scheduledTime;
        $estimatedReadyTime = Carbon::parse($scheduledDatetime);
    }

    // Store in session
    session([
        'delivery_type' => $this->deliveryType,
        'delivery_time' => $this->deliveryTime,
        'scheduled_datetime' => $scheduledDatetime,
        'delivery_address' => $this->deliveryAddress,
        'estimated_ready_time' => $estimatedReadyTime,
    ]);

    $this->close();
    
    $this->dispatch('proceedToCheckout');
}

    public function render()
    {
        return view('livewire.delivery-options-modal', [
            'timeSlots' => $this->getTimeSlots()
        ]);
    }
}