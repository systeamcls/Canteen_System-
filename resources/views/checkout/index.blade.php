@extends('layouts.canteen')

@section('title', 'Checkout - LTO Canteen Central')

@section('content')
    @livewire('checkout-form', [
        'initialCustomerType' => $customerType,
        'initialUserType' => $userType,
        'availablePaymentMethods' => $availablePaymentMethods,
        'availableOrderTypes' => $availableOrderTypes,
    ])

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('order-completed', (event) => {
                    console.log('Order completed event:', event); // ⭐ Add debug log

                    // Extract orderGroupId properly
                    const orderGroupId = event.orderGroupId || event[0]?.orderGroupId;

                    console.log('Redirecting to order:', orderGroupId); // ⭐ Add debug log

                    if (orderGroupId) {
                        window.location.href = '/payment/success/' + orderGroupId;
                    } else {
                        console.error('No order group ID found!', event);
                        alert('Order created but redirect failed. Please check your orders.');
                    }
                });

                Livewire.on('checkout-error', (event) => {
                    console.error('Checkout error:', event.message);
                });
            });
        </script>
    @endpush
@endsection
