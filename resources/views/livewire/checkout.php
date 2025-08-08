<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
            <!-- User Type Badge -->
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm
                {{ $userType === 'guest' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                <span class="capitalize">{{ $userType }} User</span>
            </div>
        </div>
             <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                    <!-- Order Type -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Order Type</h2>
                        <div class="flex space-x-4">
                            @if($userType === 'employee')
                            <label class="flex items-center">
                                <input type="radio" wire:model="orderType" value="onsite" 
                                       class="text-red-600 focus:ring-red-500">
                                <span class="ml-2">Dine In</span>
                            </label>
                            @endif
                            <label class="flex items-center">
                                <input type="radio" wire:model="orderType" value="online" 
                                       class="text-red-600 focus:ring-red-500">
                                <span class="ml-2">Take Out</span>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Payment Method</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($availablePaymentMethods as $method => $details)
                            <label class="relative border rounded-lg p-4 cursor-pointer hover:bg-gray-50 {{ $paymentMethod === $method ? 'border-red-600' : 'border-gray-300' }}">
                                <input type="radio" wire:model="paymentMethod" value="{{ $method }}" class="sr-only">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">{{ $details['name'] }}</p>
                                        <p class="text-gray-500">{{ $details['description'] }}</p>    
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
    
                        @if($userType === 'guest')
                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">
                                        As a guest, you can only use online payment methods. 
                                        <a href="/" class="font-medium underline hover:text-blue-900">
                                            Login as an employee
                                        </a>
                                        to access cash payment options.
                                    </p>
                                </div>        
                            </div>

                        </div>

                        @endif
                    </div>

                         <!-- Special Instructions -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Special Instructions</h2>
                        <textarea
                            wire:model="specialInstructions"
                            rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Any special requests or notes for your order?">
                        </textarea>
                    </div>
                </div>
            </div>

                <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between">
                                <div>
                                    <span class="text-gray-900">{{ $item['quantity'] }}x {{ $item['name'] }}</span>
                                    <p class="text-sm text-gray-500">{{ $item['stall'] }}</p>
                                </div>

                                <span class="text-gray-900">₱{{ number_format($item['subtotal'], 2) }}</span>
                            </div>
                        @endforeach

                            <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Total</span>
                                <span class="text-lg font-medium text-red-600">₱{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <button
                            wire:click="placeOrder"
                            class="w-full mt-6 bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-not-allowed"
                        >
                            <span wire:loading.remove>Place Order</span>
                            <span wire:loading>Processing...</span>
                        </button>

                            @if($userType === 'guest')
                        <p class="mt-4 text-sm text-center text-gray-500">
                            Order will be tracked using your session. 
                            <br>Please save your order number for reference.
                        </p>
                        @endif
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>