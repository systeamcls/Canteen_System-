<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
            <!-- User Type Badge -->
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm
                {{ auth()->check() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                <span class="capitalize">{{ auth()->check() ? 'Employee' : 'Guest' }} User</span>
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
                            @if(auth()->check())
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
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @if(auth()->check())
                            <label class="relative border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                                <input type="radio" wire:model="paymentMethod" value="cash" class="sr-only">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">Cash</p>
                                        <p class="text-gray-500">Pay at counter</p>
                                    </div>
                                </div>
                                <div class="absolute -inset-px rounded-lg border-2" aria-hidden="true"
                                     :class="{ 'border-red-600': paymentMethod === 'cash', 'border-transparent': paymentMethod !== 'cash' }">
                                </div>
                            </label>
                            @endif

                            <label class="relative border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                                <input type="radio" wire:model="paymentMethod" value="card" class="sr-only">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">Card</p>
                                        <p class="text-gray-500">Credit or Debit</p>
                                    </div>
                                </div>
                                <div class="absolute -inset-px rounded-lg border-2" aria-hidden="true"
                                     :class="{ 'border-red-600': paymentMethod === 'card', 'border-transparent': paymentMethod !== 'card' }">
                                </div>
                            </label>

                            <label class="relative border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                                <input type="radio" wire:model="paymentMethod" value="e-wallet" class="sr-only">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">E-Wallet</p>
                                        <p class="text-gray-500">GCash, Maya, etc.</p>
                                    </div>
                                </div>
                                <div class="absolute -inset-px rounded-lg border-2" aria-hidden="true"
                                     :class="{ 'border-red-600': paymentMethod === 'e-wallet', 'border-transparent': paymentMethod !== 'e-wallet' }">
                                </div>
                            </label>
                        </div>

                        @if(!auth()->check())
                        <p class="mt-3 text-sm text-gray-500">
                            * Guest users can only use online payment methods (Card or E-Wallet).
                            <a href="{{ route('login') }}" class="text-red-600 hover:text-red-700">
                                Login as employee
                            </a>
                            for cash payment option.
                        </p>
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

                        @if(!auth()->check())
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