{{-- resources/views/filament/cashier/widgets/order-details-modal.blade.php --}}
<div class="space-y-6">
    <!-- Customer & Order Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Customer Information -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Customer Information</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-xs text-gray-500">Name:</span>
                    <p class="text-sm font-medium">{{ $order->customer_name ?: 'Walk-in Customer' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Phone:</span>
                    <p class="text-sm font-medium">{{ $order->customer_phone ?: 'Not provided' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Email:</span>
                    <p class="text-sm font-medium">{{ $order->customer_email ?: 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Order Information</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-xs text-gray-500">Order Number:</span>
                    <p class="text-sm font-medium">{{ $order->order_number }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Status:</span>
                    <p class="text-sm font-medium capitalize">{{ $order->status }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Order Type:</span>
                    <p class="text-sm font-medium capitalize">{{ $order->order_type }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Payment Method:</span>
                    <p class="text-sm font-medium capitalize">{{ $order->payment_method ?? 'Not set' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Total Amount:</span>
                    <p class="text-sm font-semibold text-green-600">₱{{ number_format($order->total_amount / 100, 2) }}
                    </p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Order Date:</span>
                    <p class="text-sm font-medium">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div>
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Order Items</h3>
        <div class="space-y-2">
            @forelse($order->items as $item)
                <div class="grid grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Product</div>
                        <div class="font-medium">
                            {{ $item->product_name ?? ($item->product?->name ?? 'Unknown Product') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Quantity</div>
                        <div class="font-medium">{{ $item->quantity ?? 0 }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Unit Price</div>
                        <div class="font-medium">₱{{ number_format(($item->unit_price ?? 0) / 100, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Subtotal</div>
                        <div class="font-semibold text-green-600 dark:text-green-400">
                            ₱{{ number_format(($item->subtotal ?? 0) / 100, 2) }}</div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">No items found</p>
            @endforelse
        </div>
    </div>
</div>
