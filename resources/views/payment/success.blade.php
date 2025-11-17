<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - LTO Canteen Central</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Receipt Card */
        .receipt-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        /* Success Header */
        .success-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
        }

        .success-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .success-header p {
            opacity: 0.95;
            font-size: 16px;
        }

        /* Receipt Body */
        .receipt-body {
            padding: 30px;
        }

        /* Order Info */
        .order-info {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .order-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .order-info-row:last-child {
            margin-bottom: 0;
        }

        .order-info-label {
            color: #6b7280;
        }

        .order-info-value {
            font-weight: 600;
            color: #1f2937;
        }

        /* Items List */
        .items-section h3 {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .item-meta {
            font-size: 13px;
            color: #6b7280;
        }

        .item-price {
            font-weight: 600;
            color: #1f2937;
            text-align: right;
        }

        /* Total */
        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            font-weight: 700;
            color: #10b981;
        }

        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            grid-column: 1 / -1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* What's Next */
        .whats-next {
            background: #eff6ff;
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
        }

        .whats-next h4 {
            color: #1e40af;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .whats-next ul {
            list-style: none;
            color: #1e40af;
        }

        .whats-next li {
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .whats-next li:before {
            content: "✓";
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: #10b981;
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: bold;
        }

        @media (max-width: 640px) {
            .action-buttons {
                grid-template-columns: 1fr;
            }

            .btn-primary {
                grid-column: 1;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .action-buttons,
            .whats-next {
                display: none;
            }

            .receipt-card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="receipt-card">
            <!-- Success Header -->
            <div class="success-header">
                <div class="success-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1>Payment Successful!</h1>
                <p>Your order has been confirmed and is being prepared</p>
            </div>

            <!-- Receipt Body -->
            <div class="receipt-body">
                <!-- Order Info -->
                <div class="order-info">
                    <div class="order-info-row">
                        <span class="order-info-label">Order Number:</span>
                        <span class="order-info-value">#{{ $orderGroup->order_number ?? 'N/A' }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Date:</span>
                        <span class="order-info-value">{{ now()->format('F d, Y h:i A') }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Payment Method:</span>
                        <span class="order-info-value">{{ ucfirst($orderGroup->payment_method ?? 'N/A') }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Order Type:</span>
                        <span class="order-info-value">{{ ucfirst($orderGroup->order_type ?? 'Pickup') }}</span>
                    </div>
                </div>

                <!-- Items Ordered -->
                <div class="items-section">
                    <h3>Items Ordered</h3>
                    @if ($orderGroup->orders && $orderGroup->orders->count() > 0)
                        @foreach ($orderGroup->orders as $order)
                            @foreach ($order->orderItems as $item)
                                <div class="item-row">
                                    <div class="item-details">
                                        <div class="item-name">{{ $item->product_name }}</div>
                                        <div class="item-meta">₱{{ number_format($item->unit_price / 100, 2) }} ×
                                            {{ $item->quantity }}</div>
                                    </div>
                                    <div class="item-price">
                                        ₱{{ number_format($item->subtotal / 100, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <p style="text-align: center; color: #6b7280; padding: 20px;">No items found</p>
                    @endif

                    <!-- Total -->
                    <div class="total-section">
                        <div class="total-row">
                            <span>Total Amount</span>
                            <span>₱{{ number_format($orderGroup->amount_total / 100, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="/menu" class="btn btn-primary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Continue Shopping
                    </a>
                    <button onclick="downloadReceipt()" class="btn btn-secondary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Receipt
                    </button>
                    <a href="/home" class="btn btn-secondary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Back to Home
                    </a>
                </div>

                <!-- What's Next -->
                <div class="whats-next">
                    <h4>What happens next?</h4>
                    <ul>
                        <li>We've received your payment</li>
                        <li>Your order is being processed</li>
                        <li>The kitchen will start preparing your food</li>
                        <li>You'll be notified when it's ready</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadReceipt() {
            const receiptCard = document.querySelector('.receipt-card');
            const downloadBtn = event.target.closest('button');
            const originalText = downloadBtn.innerHTML;

            // Show loading state
            downloadBtn.innerHTML = `
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                    <style>@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>
                    <circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" fill="none" opacity="0.25"/>
                    <path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"/>
                </svg>
                <span>Generating...</span>
            `;
            downloadBtn.disabled = true;

            // Hide buttons temporarily for cleaner screenshot
            const actionButtons = document.querySelector('.action-buttons');
            const whatsNext = document.querySelector('.whats-next');
            actionButtons.style.display = 'none';
            whatsNext.style.display = 'none';

            html2canvas(receiptCard, {
                scale: 2, // High quality
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff',
                width: receiptCard.offsetWidth,
                height: receiptCard.offsetHeight
            }).then(canvas => {
                // Convert canvas to blob
                canvas.toBlob(function(blob) {
                    // Create download link
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    const filename = 'receipt-{{ $orderGroup->order_number ?? 'order' }}-' + new Date()
                        .getTime() + '.png';

                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Clean up
                    URL.revokeObjectURL(url);

                    // Restore UI
                    actionButtons.style.display = 'grid';
                    whatsNext.style.display = 'block';
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;

                    // Show success message
                    showNotification('Receipt downloaded successfully!', 'success');
                }, 'image/png', 1.0);
            }).catch(error => {
                console.error('Error generating image:', error);

                // Restore UI
                actionButtons.style.display = 'grid';
                whatsNext.style.display = 'block';
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;

                showNotification('Failed to generate receipt. Please try again.', 'error');
            });
        }

        // Simple notification function
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                z-index: 9999;
                font-weight: 600;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;

            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(400px); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(400px); opacity: 0; }
                }
            `;
            document.head.appendChild(style);

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Optional: Share functionality for mobile
        function shareReceipt() {
            const receiptCard = document.querySelector('.receipt-card');
            const actionButtons = document.querySelector('.action-buttons');
            const whatsNext = document.querySelector('.whats-next');

            actionButtons.style.display = 'none';
            whatsNext.style.display = 'none';

            html2canvas(receiptCard, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                canvas.toBlob(async function(blob) {
                    const file = new File([blob], 'receipt.png', {
                        type: 'image/png'
                    });

                    if (navigator.share && navigator.canShare({
                            files: [file]
                        })) {
                        try {
                            await navigator.share({
                                title: 'Order Receipt',
                                text: 'My order from LTO Canteen Central',
                                files: [file]
                            });
                        } catch (error) {
                            if (error.name !== 'AbortError') {
                                console.error('Share failed:', error);
                            }
                        }
                    } else {
                        showNotification('Share not supported on this device', 'error');
                    }

                    actionButtons.style.display = 'grid';
                    whatsNext.style.display = 'block';
                }, 'image/png');
            });
        }
    </script>
</body>

</html>
