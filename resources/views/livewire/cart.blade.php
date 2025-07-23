<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        @if(count($cartItems) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="p-6 space-y-6">
                            @foreach($cartItems as $item)
                                <div class="flex items-center space-x-4 py-4 border-b border-gray-200 last:border-0">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-24 h-24">
                                        @if($item['image'])
                                            <img src="{{ Storage::url($item['image']) }}" 
                                                 alt="{{ $item['name'] }}"
                                                 class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $item['name'] }}</h3>
                                        <p class="text-sm text-gray-500">From {{ $item['stall'] }}</p>
                                        <div class="mt-2 flex items-center space-x-4">
                                            <span class="text-red-600 font-medium">₱{{ number_format($item['price'], 2) }}</span>
                                            <div class="flex items-center border border-gray-300 rounded-md">
                                                <button wire:click="decrementQuantity({{ $item['id'] }})"
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 focus:outline-none">
                                                    -
                                                </button>
                                                <span class="px-3 py-1 border-x border-gray-300">{{ $item['quantity'] }}</span>
                                                <button wire:click="incrementQuantity({{ $item['id'] }})"
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 focus:outline-none">
                                                    +
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Subtotal & Remove -->
                                    <div class="text-right">
                                        <p class="text-lg font-medium text-gray-900">₱{{ number_format($item['subtotal'], 2) }}</p>
                                        <button wire:click="removeItem({{ $item['id'] }})"
                                                class="mt-2 text-sm text-red-600 hover:text-red-500">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="text-gray-900 font-medium">₱{{ number_format($total, 2) }}</span>
                            </div>
                            
                            <!-- Add other fees if needed -->
                            
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-medium text-gray-900">Total</span>
                                    <span class="text-lg font-medium text-red-600">₱{{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <button wire:click="checkout"
                                    class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-lg shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-2 text-gray-500">Add some delicious items to your cart and come back here to check out!</p>
                <a href="{{ route('menu') }}"
                   class="mt-6 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Browse Menu
                </a>
            </div>
        @endif
    </div>
</div>