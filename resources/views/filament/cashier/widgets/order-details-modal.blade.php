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

    <!-- Order Items - Simple List Format -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Items Ordered</h3>
        <div class="space-y-3">
            @forelse($order->items as $item)
                <div class="flex justify-between items-start py-2 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ $item->product_name ?? ($item->product?->name ?? 'Unknown Product') }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            ₱{{ number_format(($item->unit_price ?? 0) / 100, 2) }} × {{ $item->quantity ?? 0 }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            ₱{{ number_format(($item->subtotal ?? 0) / 100, 2) }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8">No items found</p>
            @endforelse
        </div>

        <!-- Total -->
        <div class="mt-4 pt-4 border-t-2 border-gray-300 dark:border-gray-600">
            <div class="flex justify-between items-center">
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">Total</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">
                    ₱{{ number_format($order->total_amount / 100, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>
