@extends('layouts.canteen')

@section('title', 'Checkout - LTO Canteen Central')

@section('content')
@livewire('checkout-form', [
    'initialCustomerType' => $customerType,
    'initialUserType' => $userType,
    'availablePaymentMethods' => $availablePaymentMethods,
    'availableOrderTypes' => $availableOrderTypes
])

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('order-completed', (event) => {
            window.location.href = '/checkout/success/' + event.orderGroupId;
        });
        
        Livewire.on('checkout-error', (event) => {
            console.error('Checkout error:', event.message);
        });
    });
</script>
@endpush
@endsection