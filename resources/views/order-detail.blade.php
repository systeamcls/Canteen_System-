<x-layouts.profile>
    <style>
        :root {
            --primary-orange: #FF6B35;
            --secondary-orange: #FF8A6B;
            --accent-orange: #E55B2B;
            --light-orange: #FFF4F1;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --background: #F9FAFB;
            --white: #FFFFFF;
            --success: #10B981;
            --error: #EF4444;
            --warning: #F59E0B;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        .order-detail-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .order-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-orange) 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .order-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .order-header-content {
            display: flex;
            justify-content: space-between;
            align-items: start;
            position: relative;
            z-index: 1;
        }

        .order-title {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .order-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .order-date {
            font-size: 1rem;
            opacity: 0.9;
        }

        .order-status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .back-button {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 2;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .order-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .order-items-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px var(--shadow);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: var(--light-orange);
            color: var(--primary-orange);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .order-item:hover {
            border-color: var(--primary-orange);
            background: var(--light-orange);
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            font-size: 2rem;
            color: var(--primary-orange);
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .item-meta {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .item-special-instructions {
            background: var(--background);
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            color: var(--text-muted);
            font-style: italic;
        }

        .item-pricing {
            text-align: right;
            min-width: 120px;
        }

        .item-quantity {
            background: var(--primary-orange);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .item-unit-price {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .item-line-total {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .order-summary {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px var(--shadow);
            height: fit-content;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--text-dark);
        }

        .summary-label {
            color: var(--text-muted);
        }

        .summary-value {
            font-weight: 600;
            color: var(--text-dark);
        }

        .order-info-card {
            background: var(--background);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-weight: 600;
            color: var(--text-dark);
        }

        .status-timeline {
            background: var(--background);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            position: relative;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 15px;
            top: 100%;
            width: 2px;
            height: 1.5rem;
            background: var(--border);
        }

        .timeline-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .timeline-dot.completed {
            background: var(--success);
            color: white;
        }

        .timeline-dot.current {
            background: var(--primary-orange);
            color: white;
        }

        .timeline-dot.pending {
            background: var(--border);
            color: var(--text-muted);
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .timeline-time {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .order-detail-container {
                padding: 1rem;
            }

            .order-header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .order-content {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .item-image {
                margin-right: 0;
            }

            .item-pricing {
                text-align: center;
                min-width: auto;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="order-detail-container">
        <!-- Order Header -->
        <div class="order-header fade-in">
            <button class="back-button" onclick="goBack()">
                <i class="fas fa-arrow-left"></i>
            </button>
            
            <div class="order-header-content">
                <div class="order-title">
                    <h1 class="order-number">{{ $order->order_number }}</h1>
                    <p class="order-date">
                        <i class="fas fa-calendar-alt"></i>
                        Ordered on {{ $order->created_at->format('F j, Y • g:i A') }}
                    </p>
                </div>
                <div class="order-status-badge">
                    {{ ucfirst($order->status) }}
                </div>
            </div>
        </div>

        <div class="order-content fade-in">
            <!-- Order Items Section -->
            <div class="order-items-section">
                <h2 class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    Order Items
                </h2>

                @foreach($order->orderItems as $item)
                <div class="order-item">
                    <div class="item-image">
                        @if($item->product && $item->product->image)
                            <img src="{{ Storage::url($item->product->image) }}" 
                                 alt="{{ $item->product_name }}"
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                        @else
                            <i class="fas fa-utensils"></i>
                        @endif
                    </div>
                    
                    <div class="item-details">
                        <h3 class="item-name">{{ $item->product_name }}</h3>
                        <p class="item-meta">
                            Unit Price: ₱{{ number_format($item->unit_price / 100, 2) }}
                        </p>
                        @if($item->special_instructions)
                        <div class="item-special-instructions">
                            <i class="fas fa-sticky-note"></i>
                            Special Instructions: {{ $item->special_instructions }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="item-pricing">
                        <div class="item-quantity">Qty: {{ $item->quantity }}</div>
                        <div class="item-unit-price">₱{{ number_format($item->unit_price / 100, 2) }} each</div>
                        <div class="item-line-total">₱{{ number_format($item->line_total / 100, 2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary Sidebar -->
            <div class="order-summary fade-in">
                <h2 class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    Order Summary
                </h2>

                <!-- Order Information -->
                <div class="order-info-card">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Order Type</span>
                            <span class="info-value">{{ ucfirst($order->order_type ?? 'Online') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Service Type</span>
                            <span class="info-value">{{ ucfirst($order->service_type ?? 'Take-away') }}</span>
                        </div>
                        @if($order->payment_method)
                        <div class="info-item">
                            <span class="info-label">Payment Method</span>
                            <span class="info-value">{{ ucfirst($order->payment_method) }}</span>
                        </div>
                        @endif
                        @if($order->estimated_completion)
                        <div class="info-item">
                            <span class="info-label">Estimated Time</span>
                            <span class="info-value">{{ $order->estimated_completion }} minutes</span>
                        </div>
                        @endif
                    </div>
                    
                    @if($order->special_instructions)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                        <span class="info-label">Special Instructions</span>
                        <div style="margin-top: 0.5rem; padding: 0.75rem; background: white; border-radius: 8px; font-style: italic;">
                            {{ $order->special_instructions }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Order Status Timeline -->
                <div class="status-timeline">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Order Status</h3>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Placed</div>
                            <div class="timeline-time">{{ $order->created_at->format('M j, Y • g:i A') }}</div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot {{ $order->status === 'pending' ? 'current' : (in_array($order->status, ['processing', 'completed']) ? 'completed' : 'pending') }}">
                            @if(in_array($order->status, ['processing', 'completed']))
                                <i class="fas fa-check"></i>
                            @elseif($order->status === 'pending')
                                <i class="fas fa-clock"></i>
                            @else
                                <i class="fas fa-times"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Order Confirmed</div>
                            <div class="timeline-time">
                                @if($order->status === 'pending')
                                    Awaiting confirmation
                                @elseif(in_array($order->status, ['processing', 'completed']))
                                    Confirmed
                                @elseif($order->status === 'cancelled')
                                    Order cancelled
                                @else
                                    Awaiting confirmation
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot {{ $order->status === 'completed' ? 'completed' : ($order->status === 'processing' ? 'current' : 'pending') }}">
                            @if($order->status === 'completed')
                                <i class="fas fa-check"></i>
                            @elseif($order->status === 'processing')
                                <i class="fas fa-utensils"></i>
                            @elseif($order->status === 'cancelled')
                                <i class="fas fa-times"></i>
                            @else
                                <i class="fas fa-clock"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">
                                @if($order->status === 'completed')
                                    Order Completed
                                @elseif($order->status === 'processing')
                                    Preparing Order
                                @elseif($order->status === 'cancelled')
                                    Order Cancelled
                                @else
                                    Preparing Order
                                @endif
                            </div>
                            <div class="timeline-time">
                                @if($order->status === 'completed')
                                    {{ $order->updated_at->format('M j, Y • g:i A') }}
                                @elseif($order->status === 'processing')
                                    In progress...
                                @elseif($order->status === 'cancelled')
                                    Order was cancelled
                                @else
                                    Waiting to start
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-dot {{ $order->status === 'completed' ? 'completed' : 'pending' }}">
                            @if($order->status === 'completed')
                                <i class="fas fa-check"></i>
                            @elseif($order->status === 'cancelled')
                                <i class="fas fa-ban"></i>
                            @else
                                <i class="fas fa-hand-holding"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Ready for Pickup</div>
                            <div class="timeline-time">
                                @if($order->status === 'completed')
                                    Ready now!
                                @elseif($order->status === 'cancelled')
                                    Unavailable
                                @else
                                    Estimated in {{ $order->estimated_completion ?? 15 }} minutes
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Summary -->
                <div style="background: white; border-radius: 16px; padding: 1.5rem;">
                    @if($order->orderGroup)
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value">₱{{ number_format($order->orderItems->sum('line_total') / 100, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Tax & Fees</span>
                        <span class="summary-value">₱{{ number_format(($order->orderGroup->amount_total - $order->orderItems->sum('line_total')) / 100, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount</span>
                        <span>₱{{ number_format($order->orderGroup->amount_total / 100, 2) }}</span>
                    </div>
                    @else
                    <div class="summary-row">
                        <span class="summary-label">Items Total</span>
                        <span class="summary-value">₱{{ number_format($order->orderItems->sum('line_total') / 100, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount</span>
                        <span>₱{{ number_format($order->orderItems->sum('line_total') / 100, 2) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                    @if($order->status === 'pending')
                    <button class="btn btn-danger" onclick="cancelOrder({{ $order->id }})">
                        <i class="fas fa-times"></i>
                        Cancel Order
                    </button>
                    @elseif($order->status === 'cancelled')
                    <div class="alert" style="background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 0.75rem; border-radius: 8px; text-align: center;">
                        <i class="fas fa-ban"></i> Order was cancelled
                    </div>
                    @endif
                    
                    <button class="btn btn-secondary" onclick="reorderItems({{ $order->id }})">
                        <i class="fas fa-redo"></i>
                        Reorder Items
                    </button>
                    
                    <button class="btn btn-outline" onclick="downloadReceipt()">
                        <i class="fas fa-download"></i>
                        Download Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let orderStatusInterval = null;
        const currentOrderId = {{ $order->id }};
        let currentStatus = '{{ $order->status }}';

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '{{ route("user.profile.show") }}';
            }
        }

        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                fetch(`/profile/orders/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Order cancelled successfully', 'success');
                        // Refresh the page to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to cancel order. Please try again.', 'error');
                });
            }
        }

        function reorderItems(orderId) {
            // Implement reorder functionality
            showToast('Reorder functionality coming soon!', 'info');
        }

        function downloadReceipt() {
            // Implement receipt download
            window.print();
        }

        function checkOrderStatus() {
            fetch(`/profile/orders/${currentOrderId}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== currentStatus) {
                        currentStatus = data.status;
                        showToast(`Order status updated to: ${data.status}`, 'info');
                        // Refresh page to show updated timeline
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                });
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.textContent = message;
            
            let bgColor = '#374151'; // default
            if (type === 'success') bgColor = '#10B981';
            if (type === 'error') bgColor = '#EF4444';
            if (type === 'info') bgColor = '#3B82F6';
            
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: ${bgColor};
                color: white;
                padding: 12px 20px;
                border-radius: 25px;
                font-size: 14px;
                font-weight: 500;
                z-index: 10000;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            `;
            
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        // Start real-time status checking for active orders
        document.addEventListener('DOMContentLoaded', function() {
            if (['pending', 'processing'].includes(currentStatus)) {
                // Check status every 30 seconds
                orderStatusInterval = setInterval(checkOrderStatus, 30000);
                
                // Also check when page becomes visible again
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden && ['pending', 'processing'].includes(currentStatus)) {
                        checkOrderStatus();
                    }
                });
            }
        });

        // Cleanup interval when leaving page
        window.addEventListener('beforeunload', function() {
            if (orderStatusInterval) {
                clearInterval(orderStatusInterval);
            }
        });
    </script>

    <style>
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            width: 100%;
        }

        .btn-danger {
            background: var(--error);
            color: white;
        }

        .btn-danger:hover {
            background: #DC2626;
        }

        .btn-secondary {
            background: var(--primary-orange);
            color: white;
        }

        .btn-secondary:hover {
            background: var(--accent-orange);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-dark);
            border: 2px solid var(--border);
        }

        .btn-outline:hover {
            background: var(--background);
            border-color: var(--primary-orange);
        }
    </style>
</x-layouts.profile>