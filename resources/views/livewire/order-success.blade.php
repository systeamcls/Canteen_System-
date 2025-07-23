<div class="bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">Order Placed Successfully!</h1>
            <p class="mt-2 text-gray-600">Thank you for your order. We'll start preparing it right away.</p>

            <!-- Order Details -->
            <div class="mt-8 text-left">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Order Number</p>
                            <p class="font-medium">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Amount</p>
                            <p class="font-medium">₱{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Order Type</p>
                            <p class="font-medium capitalize">{{ $order->order_type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Payment Method</p>
                            <p class="font-medium capitalize">{{ $order->payment_method }}</p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-4">Order Items</h3>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex justify-between">
                                    <div>
                                        <p class="font-medium">{{ $item->product->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->quantity }} x ₱{{ number_format($item->unit_price, 2) }}</p>
                                    </div>
                                    <p class="font-medium">₱{{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 space-x-4">
                <a href="{{ route('menu') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Order More
                </a>
                <a href="{{ route('orders') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    View Orders
                </a>
            </div>
        </div>
    </div>
</div>