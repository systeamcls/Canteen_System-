<!-- Cart Sidebar Overlay -->
<div x-data="{ open: @entangle('isOpen') }" 
     x-show="open" 
     x-transition:enter="ease-in-out duration-500"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in-out duration-500"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-hidden"
     style="display: none;">
    
    <!-- Background overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="closeCart"></div>
    
    <!-- Sidebar -->
    <div class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-xl"
         x-show="open"
         x-transition:enter="transform transition ease-in-out duration-500"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-500"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-900">Your Order</h2>
            </div>
            <button wire:click="closeCart" class="p-2 rounded-md hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Cart Content -->
        <div class="flex flex-col h-full">
            @if(count($cartItems ?? []) > 0)
                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                    @foreach($cartItems as $item)
                        <div class="flex items-center space-x-3 py-3 border-b border-gray-100 last:border-0">
                            <!-- Product Image -->
                            <div class="flex-shrink-0 w-16 h-16">
                                @if($item['image'])
                                    <img src="{{ Storage::url($item['image']) }}" 
                                         alt="{{ $item['name'] }}"
                                         class="w-full h-full object-cover rounded-lg">
                                @else
                                    <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $item['stall'] }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm font-medium text-green-600">₱{{ number_format($item['price'], 2) }}</span>
                                    <div class="flex items-center space-x-2">
                                        <!-- Quantity Controls -->
                                        <button wire:click="decrementQuantity({{ $item['id'] }})"
                                                class="w-6 h-6 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="text-sm font-medium w-8 text-center">{{ $item['quantity'] }}</span>
                                        <button wire:click="incrementQuantity({{ $item['id'] }})"
                                                class="w-6 h-6 flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <!-- Subtotal and Remove -->
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-sm font-semibold text-gray-900">₱{{ number_format($item['subtotal'], 2) }}</span>
                                    <button wire:click="removeItem({{ $item['id'] }})"
                                            class="text-xs text-red-500 hover:text-red-700">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Sub Total</span>
                            <span class="text-gray-900">₱{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax 5%</span>
                            <span class="text-gray-900">₱{{ number_format($total * 0.05, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-300 pt-3">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-gray-900">Total Amount</span>
                                <span class="text-lg font-bold text-green-600">₱{{ number_format($total * 1.05, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button wire:click="checkout"
                            class="w-full mt-4 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors font-medium">
                        Place Order
                    </button>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="flex-1 flex flex-col items-center justify-center px-6 py-8">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
                    <p class="text-gray-500 text-center mb-6">Add some delicious items to your cart and come back here!</p>
                    <button wire:click="closeCart"
                            onclick="window.location.href='{{ route('menu.index') }}'"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        Browse Menu
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

