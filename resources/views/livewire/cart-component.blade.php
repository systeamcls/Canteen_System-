<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>
        
        @if(empty($cartItems))
            <div class="text-center py-12">
                <h2 class="text-xl text-gray-500 mb-4">Your cart is empty</h2>
                <a href="{{ route('menu') }}" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700">
                    Browse Menu
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        @foreach($cartItems as $item)
                            <div class="flex items-center justify-between py-4 border-b border-gray-200">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-500">{{ $item['stall'] }}</p>
                                    <p class="text-lg font-semibold text-red-600">₱{{ number_format($item['price'], 2) }}</p>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" 
                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
                                            -
                                        </button>
                                        <span class="w-8 text-center">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" 
                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
                                            +
                                        </button>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">₱{{ number_format($item['subtotal'], 2) }}</p>
                                        <button wire:click="removeFromCart({{ $item['id'] }})" 
                                                class="text-red-600 hover:text-red-700 text-sm">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>₱{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between font-semibold text-lg">
                                <span>Total</span>
                                <span class="text-red-600">₱{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('checkout') }}" 
                           class="w-full mt-6 bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 text-center block">
                            Proceed to Checkout
                        </a>
                        
                        <a href="{{ route('menu') }}" 
                           class="w-full mt-3 border border-gray-300 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-50 text-center block">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
