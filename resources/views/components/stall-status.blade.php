{{-- resources/views/components/stall-status.blade.php --}}
<div class="flex flex-col gap-1">
    @if($getRecord()->is_active)
        <x-filament::badge color="success" icon="heroicon-m-check-circle">
            Active
        </x-filament::badge>
    @else
        <x-filament::badge color="danger" icon="heroicon-m-pause-circle">
            Inactive
        </x-filament::badge>
    @endif
    
    @php
        $rentalStatus = $getRecord()->getCurrentRentalStatus();
    @endphp
    
    <x-filament::badge 
        color="{{ match($rentalStatus) {
            'vacant' => 'gray',
            'no_payment' => 'warning', 
            'pending' => 'warning',
            'paid' => 'success',
            'partially_paid' => 'info',
            'overdue' => 'danger',
            default => 'gray'
        } }}"
        size="sm"
    >
        {{ ucfirst(str_replace('_', ' ', $rentalStatus)) }}
    </x-filament::badge>
</div>