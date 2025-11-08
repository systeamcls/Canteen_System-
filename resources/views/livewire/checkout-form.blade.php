<div>
    <div class="checkout-page">
        <!-- Sticky Header Only -->
        <div class="sticky-header">
            <div class="header-content">
                <button class="back-button-new" onclick="history.back()">
                    <i class="arrow-left-new">←</i>
                    <span>Back</span>
                </button>
                <div class="header-center">
                    <h1 class="checkout-title">Checkout</h1>
                    <p class="checkout-subtitle">Review your order</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if ($successMessage)
            <div class="alert success">
                <i class="check-icon"></i>
                {{ $successMessage }}
            </div>
        @endif

        @if ($errorMessage)
            <div class="alert error">
                <i class="warning-icon"></i>
                {{ $errorMessage }}
            </div>
        @endif

        <!-- Main Content -->
        @if (!empty($cartSnapshot))
            <div class="main-content">
                <form wire:submit.prevent="submitOrder" class="checkout-form">
                    <div class="content-grid">
                        <!-- Left Column: Order Summary & Special Instructions -->
                        <div class="left-section">
                            <!-- Order Summary Card - Compact Version -->
                            <div class="order-summary-card">
                                <div class="order-summary-header">
                                    <span class="location-icon">📍</span>
                                    <h3>Order Summary</h3>
                                </div>

                                <div class="order-items-list">
                                    @php
                                        $totalItems = count($cartSnapshot);
                                        $showLimit = 3;
                                        $itemsToShow = $showExpandedItems
                                            ? $cartSnapshot
                                            : array_slice($cartSnapshot, 0, $showLimit);
                                    @endphp

                                    @foreach ($itemsToShow as $index => $item)
                                        <div class="order-item" wire:key="item-{{ $index }}">
                                            <div class="item-image-wrapper">
                                                @if (isset($item['product_image']) && $item['product_image'])
                                                    <img src="{{ asset('storage/' . $item['product_image']) }}"
                                                        alt="{{ $item['product_name'] }}" class="item-image">
                                                @else
                                                    <img src="{{ asset('images/default-product.png') }}"
                                                        alt="{{ $item['product_name'] }}" class="item-image">
                                                @endif
                                            </div>

                                            <div class="item-content">
                                                <div class="item-info">
                                                    <h5 class="item-title">{{ $item['product_name'] }} -
                                                        {{ $item['vendor_name'] }}</h5>
                                                    <p class="item-subtitle">Traditional Filipino cuisine with authentic
                                                        flavors</p>
                                                </div>

                                                <div class="quantity-controls">
                                                    <button type="button" class="quantity-btn minus-btn"
                                                        wire:click="updateQuantity({{ $index }}, {{ max(1, $item['quantity'] - 1) }})">
                                                        −
                                                    </button>
                                                    <span class="quantity-display">{{ $item['quantity'] }}</span>
                                                    <button type="button" class="quantity-btn plus-btn"
                                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})">
                                                        +
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="item-actions">
                                                <button type="button" class="delete-item-btn"
                                                    onclick="confirmDelete({{ $index }}, '{{ $item['product_name'] }}')">
                                                    🗑️
                                                </button>
                                                <div class="price-section">
                                                    <div class="unit-price">
                                                        ₱{{ number_format($item['unit_price'] / 100, 0) }} each</div>
                                                    <div class="item-total">
                                                        ₱{{ number_format($item['line_total'] / 100, 0) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if ($totalItems > $showLimit)
                                        <div class="items-toggle">
                                            @if (!$showExpandedItems)
                                                <button type="button" class="see-more-btn"
                                                    wire:click="toggleExpandedItems">
                                                    <span>See {{ $totalItems - $showLimit }} more items</span>
                                                    <i class="chevron-down">▼</i>
                                                </button>
                                            @else
                                                <button type="button" class="see-less-btn"
                                                    wire:click="toggleExpandedItems">
                                                    <span>Show less</span>
                                                    <i class="chevron-up">▲</i>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Special Instructions Card -->
                            <div class="special-instructions-card">
                                <div class="card-header">
                                    <i class="note-icon"></i>
                                    <h3>Special Instructions</h3>
                                </div>
                                <div class="instructions-content">
                                    <textarea wire:model="notes" class="instructions-textarea @error('notes') error @enderror"
                                        placeholder="Add cooking instructions, allergies, or delivery notes..." rows="4"></textarea>
                                    @error('notes')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Contact Details & Order Total -->
                        <div class="right-section">
                            <!-- Contact Details Card -->
                            <div class="contact-card">
                                <div class="card-header">
                                    <i class="user-icon"></i>
                                    <h3>Contact Details</h3>
                                </div>
                                <!-- Replace lines 164-215 in your blade file with this updated contact form section -->

                                <div class="contact-form">
                                    <div class="input-group">
                                        <label>Full Name</label>
                                        <input type="text" wire:model.blur="customerName"
                                            class="text-input @error('customerName') error @enderror"
                                            placeholder="Enter your name">
                                        @error('customerName')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="input-group">
                                        <label>How would you like to receive order updates?</label>
                                        <select wire:model.live="notificationPreference"
                                            class="text-input @error('notificationPreference') error @enderror">
                                            <option value="">Select notification method</option>
                                            <option value="sms">SMS (requires phone number)</option>
                                            <option value="email">Email (requires email address)</option>
                                            <option value="both">Both SMS & Email (requires both)</option>
                                        </select>
                                        @error('notificationPreference')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Phone Number Field - Shows when SMS or Both selected -->
                                    @if (in_array($notificationPreference, ['sms', 'both']))
                                        <div class="input-group">
                                            <label>Phone Number <span style="color: #e53e3e;">*</span></label>
                                            <input type="tel" wire:model.blur="customerPhone"
                                                class="text-input phone-input @error('customerPhone') error @enderror"
                                                placeholder="09123456789" maxlength="13" inputmode="numeric">
                                            <small
                                                style="color: #718096; font-size: 12px; margin-top: 4px; display: block;">
                                                Format: 09123456789, 639123456789, or +639123456789
                                            </small>
                                            @error('customerPhone')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    <!-- Email Address Field - Shows when Email or Both selected -->
                                    @if (in_array($notificationPreference, ['email', 'both']))
                                        <div class="input-group">
                                            <label>Email Address <span style="color: #e53e3e;">*</span></label>
                                            <input type="email" wire:model.blur="customerEmail"
                                                class="text-input @error('customerEmail') error @enderror"
                                                placeholder="your.email@example.com">
                                            <small
                                                style="color: #718096; font-size: 12px; margin-top: 4px; display: block;">
                                                Please enter a valid email with proper domain (e.g., user@gmail.com)
                                            </small>
                                            @error('customerEmail')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Order Total Card -->
                            <div class="order-total-card">
                                <!-- Order Summary Header -->
                                <div class="payment-total-header">
                                    <div class="payment-icon-wrapper">
                                        <i class="payment-header-icon">💳</i>
                                        <h3>Payment & Total</h3>
                                    </div>
                                    <div class="total-amount-display">
                                        <div class="total-label">Total Amount</div>
                                        <div class="total-amount">₱{{ number_format($totalAmount / 100, 0) }}</div>
                                    </div>
                                </div>

                                <!-- Order Summary Breakdown -->
                                <div class="order-summary-section">
                                    <h4 class="summary-title">ORDER SUMMARY</h4>
                                    <div class="breakdown-row">
                                        <span>Subtotal ({{ count($cartSnapshot) }} items)</span>
                                        <span>₱{{ number_format($totalAmount / 100, 0) }}</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>Delivery</span>
                                        <span class="free-text">Free</span>
                                    </div>
                                    <div class="breakdown-divider"></div>
                                    <div class="breakdown-row total-row">
                                        <span class="total-label">Total</span>
                                        <span class="total-amount">₱{{ number_format($totalAmount / 100, 0) }}</span>
                                    </div>
                                </div>

                                <!-- Payment Method Selection -->
                                <div class="payment-method-section">
                                    @if (!$selectedPaymentMethod)
                                        <!-- Collapsed State -->
                                        <button type="button"
                                            class="payment-method-toggle {{ $errors->has('paymentMethod') ? 'error-border' : '' }}"
                                            wire:click="togglePaymentMethods">
                                            <span>Choose Payment Method</span>
                                            <i class="chevron-down">▼</i>
                                        </button>

                                        <!-- Show error message for payment method -->
                                        @error('paymentMethod')
                                            <span class="error-message payment-error">{{ $message }}</span>
                                        @enderror

                                        <!-- Expanded Payment Options -->
                                        @if ($showPaymentMethods)
                                            <div class="payment-methods-dropdown">
                                                @forelse($this->paymentMethodOptions as $method => $details)
                                                    <div class="payment-option"
                                                        wire:click="selectPaymentMethod('{{ $method }}')">
                                                        <div class="payment-option-content">
                                                            <div class="payment-icon">💳</div>
                                                            <div class="payment-details">
                                                                <div class="payment-name">{{ $details['label'] }}
                                                                </div>
                                                                <div class="payment-description">
                                                                    {{ $details['description'] }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="no-payment">No payment methods available</p>
                                                @endforelse
                                            </div>
                                        @endif
                                    @else
                                        <!-- Selected Payment Method Display -->
                                        <div class="selected-payment-display">
                                            <div class="selected-payment-info">
                                                <div class="payment-icon">💳</div>
                                                <div class="payment-details">
                                                    <div class="payment-name">
                                                        {{ $this->paymentMethodOptions[$selectedPaymentMethod]['label'] ?? $selectedPaymentMethod }}
                                                    </div>
                                                    <div class="payment-description">
                                                        {{ $this->paymentMethodOptions[$selectedPaymentMethod]['description'] ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="change-payment-btn"
                                                wire:click="changePaymentMethod">
                                                Change
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- ✅ Hidden input for reCAPTCHA token -->
                                <input type="hidden" wire:model="recaptcha_token" id="recaptcha_token_checkout">

                                <!-- Updated Place Order Button with reCAPTCHA -->
                                <button type="button" onclick="executeRecaptchaCheckout(event)"
                                    class="place-order-button {{ !$selectedPaymentMethod ? 'disabled' : '' }}"
                                    wire:loading.attr="disabled" wire:target="submitOrder"
                                    {{ $isProcessing || !$selectedPaymentMethod ? 'disabled' : '' }}>
                                    <span wire:loading.remove wire:target="submitOrder">
                                        @if (!$selectedPaymentMethod)
                                            Select Payment Method First
                                        @else
                                            Place Order - ₱{{ number_format($totalAmount / 100, 0) }}
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="submitOrder">
                                        <i class="loading-spinner"></i>
                                        Processing...
                                    </span>
                                </button>

                                <!-- Security Information -->
                                <div class="security-info">
                                    <div class="secure-payment">
                                        <i class="shield-icon"></i>
                                        <span>Secure Payment</span>
                                    </div>
                                    <p class="terms-text">By placing this order, you agree to our Terms of Service</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="empty-cart">
                <div class="empty-content">
                    <i class="empty-cart-icon"></i>
                    <h3>Your cart is empty</h3>
                    <p>Add some delicious items to your cart before proceeding to checkout.</p>
                    <a href="{{ route('menu.index') }}" class="back-to-menu-button">
                        <i class="arrow-left"></i>
                        Back to Menu
                    </a>
                </div>
            </div>
        @endif
    </div>

    <style>
        /* ==========================================================================
   CHECKOUT PAGE - ORGANIZED CSS
   ========================================================================== */

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .checkout-page {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FFF7F3;
            min-height: 100vh;
        }

        /* ==========================================================================
   STICKY HEADER (Back button + Title only)
   ========================================================================== */
        .sticky-header {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 65px;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .back-button-new {
            background: none;
            border: none;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            cursor: pointer;
            padding: 8px 0;
            font-weight: 500;
        }

        .back-button-new:hover {
            opacity: 0.7;
        }

        .arrow-left-new {
            font-size: 18px;
            font-weight: 600;
        }

        .header-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .checkout-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 2px 0;
        }

        .checkout-subtitle {
            font-size: 14px;
            color: #8a8a8a;
            margin: 0;
        }

        /* ==========================================================================
   PROGRESS STEPS (Non-sticky)
   ========================================================================== */
        .progress-section {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .progress-steps-new {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 60px;
            padding: 20px 20px 24px 20px;
            max-width: 400px;
            margin: 0 auto;
        }

        .step-new {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .step-circle-new {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .step-new .step-circle-new {
            background: #f0f0f0;
            color: #8a8a8a;
        }

        .step-new.completed .step-circle-new {
            background: #22c55e;
            color: white;
        }

        .step-new.active .step-circle-new {
            background: #ef4444;
            color: white;
        }

        .step-label {
            font-size: 14px;
            font-weight: 500;
            color: #8a8a8a;
            text-align: center;
        }

        .step-new.completed .step-label,
        .step-new.active .step-label {
            color: #1a1a1a;
        }

        .checkmark-new {
            font-size: 14px;
            font-weight: 700;
        }

        /* ==========================================================================
   MAIN LAYOUT
   ========================================================================== */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }

        /* ==========================================================================
   CARD BASE STYLES
   ========================================================================== */
        .special-instructions-card,
        .contact-card,
        .order-total-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 24px;
            border-bottom: 1px solid #f7fafc;
            color: #E53E3E;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        /* ==========================================================================
   ORDER SUMMARY DESIGN - COMPACT VERSION
   ========================================================================== */
        .order-summary-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e8e8e8;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .order-summary-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 24px 24px 24px 24px;
            border-bottom: 1px solid #f0f0f0;
        }

        .location-icon {
            font-size: 18px;
            color: #ff6b35;
        }

        .order-summary-header h3 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .order-items-list {
            padding: 0;
        }

        .order-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 24px;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image-wrapper {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
            background: #f8f8f8;
        }

        .item-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .item-info {
            flex: 1;
        }

        .item-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 6px 0;
            line-height: 1.3;
        }

        .item-subtitle {
            font-size: 14px;
            color: #8a8a8a;
            margin: 0;
            line-height: 1.4;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0;
            background: #f5f5f5;
            border-radius: 10px;
            padding: 4px;
            width: fit-content;
            align-self: flex-start;
        }

        .quantity-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            background: #e5e5e5;
            color: #333;
        }

        .quantity-btn:active {
            background: #ddd;
            transform: scale(0.95);
        }

        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            padding: 0 12px;
        }

        .item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
            flex-shrink: 0;
        }

        .delete-item-btn {
            background: none;
            border: none;
            font-size: 18px;
            color: #ccc;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-item-btn:hover {
            color: #ff6b35;
            background: #fff5f2;
        }

        .price-section {
            text-align: right;
        }

        .unit-price {
            font-size: 14px;
            color: #8a8a8a;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .item-total {
            font-size: 18px;
            font-weight: 700;
            color: #ff6b35;
            line-height: 1.2;
        }

        .items-toggle {
            padding: 16px 24px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: center;
        }

        .see-more-btn,
        .see-less-btn {
            background: none;
            border: none;
            color: #ff6b35;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .see-more-btn:hover,
        .see-less-btn:hover {
            background: #fef7f4;
        }

        .chevron-down,
        .chevron-up {
            font-size: 10px;
            transition: transform 0.2s ease;
        }

        /* ==========================================================================
   SPECIAL INSTRUCTIONS
   ========================================================================== */
        .instructions-content {
            padding: 24px;
        }

        .instructions-textarea {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            line-height: 1.5;
            resize: vertical;
            font-family: inherit;
            background: #fdfdfd;
        }

        .instructions-textarea:focus {
            outline: none;
            border-color: #E53E3E;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        .instructions-textarea.error {
            border-color: #e53e3e;
        }

        /* ==========================================================================
   CONTACT FORM
   ========================================================================== */
        .contact-form {
            padding: 24px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            color: #4a5568;
            font-weight: 500;
            font-size: 14px;
        }

        .text-input {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            font-size: 14px;
            background: #fdfdfd;
            transition: border-color 0.2s;
        }

        .text-input:focus {
            outline: none;
            border-color: #E53E3E;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        .text-input.error {
            border-color: #e53e3e;
        }

        /* ==========================================================================
   UPDATED PAYMENT & TOTAL SECTION
   ========================================================================== */
        .order-total-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            border: 1px solid #e8e8e8;
            position: sticky;
            top: 20px;
        }

        .payment-total-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 24px 24px 20px 24px;
            border-bottom: 1px solid #f0f0f0;
        }

        .payment-icon-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-header-icon {
            font-size: 18px;
            color: #ff6b35;
        }

        .payment-icon-wrapper h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .total-amount-display {
            text-align: right;
        }

        .total-label {
            font-size: 14px;
            color: #8a8a8a;
            margin-bottom: 4px;
        }

        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: #ff6b35;
        }

        .order-summary-section {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-title {
            font-size: 14px;
            font-weight: 600;
            color: #8a8a8a;
            margin: 0 0 16px 0;
            letter-spacing: 0.5px;
        }

        .breakdown-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 14px;
            color: #1a1a1a;
        }

        .breakdown-row:last-of-type {
            margin-bottom: 0;
        }

        .free-text {
            color: #22c55e;
            font-weight: 500;
        }

        .breakdown-divider {
            height: 1px;
            background: #f0f0f0;
            margin: 16px 0;
        }

        .total-row {
            font-size: 16px;
            font-weight: 600;
            margin-top: 16px;
        }

        .total-row .total-amount {
            color: #ff6b35;
            font-size: 18px;
            font-weight: 700;
        }

        .payment-method-section {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
        }

        .payment-method-toggle {
            width: 100%;
            background: white;
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #8a8a8a;
            transition: all 0.2s ease;
        }

        .payment-method-toggle:hover {
            border-color: #ff6b35;
            color: #ff6b35;
        }

        .chevron-down {
            font-size: 12px;
            transition: transform 0.2s ease;
        }

        .payment-methods-dropdown {
            margin-top: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }

        .payment-option {
            padding: 16px 20px;
            border-bottom: 1px solid #f7fafc;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .payment-option:last-child {
            border-bottom: none;
        }

        .payment-option:hover {
            background: #fef7f4;
        }

        .payment-option-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .payment-icon {
            font-size: 18px;
            color: #ff6b35;
        }

        .payment-details {
            flex: 1;
        }

        .payment-name {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .payment-description {
            font-size: 12px;
            color: #8a8a8a;
        }

        .selected-payment-display {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fef7f4;
            border: 1px solid #ff6b35;
            border-radius: 8px;
            padding: 16px 20px;
        }

        .selected-payment-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .change-payment-btn {
            background: none;
            border: none;
            color: #ff6b35;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .change-payment-btn:hover {
            background: rgba(255, 107, 53, 0.1);
        }

        .place-order-button {
            width: 90%;
            background: #ff9c66;
            color: white;
            border: none;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px 24px 16px 24px;
        }

        .place-order-button:hover:not(:disabled) {
            background: #ff8a47;
            transform: translateY(-1px);
        }

        .place-order-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .security-info {
            padding: 0 24px 24px 24px;
            text-align: center;
        }

        .secure-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #38a169;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .terms-text {
            color: #718096;
            font-size: 12px;
            line-height: 1.4;
        }

        /* ==========================================================================
   ALERTS & MESSAGES
   ========================================================================== */
        .alert {
            max-width: 1200px;
            margin: 20px auto;
            padding: 16px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert.success {
            background: #f0fff4;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert.error {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        .error-message {
            display: block;
            margin-top: 4px;
            color: #e53e3e;
            font-size: 12px;
            font-weight: 500;
        }

        /* ==========================================================================
   EMPTY CART
   ========================================================================== */
        .empty-cart {
            max-width: 600px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .empty-content {
            text-align: center;
            background: white;
            border-radius: 12px;
            padding: 60px 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .empty-content i {
            font-size: 4rem;
            color: #e2e8f0;
            margin-bottom: 20px;
        }

        .empty-content h3 {
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 20px;
        }

        .empty-content p {
            color: #718096;
            margin-bottom: 24px;
        }

        .back-to-menu-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #E53E3E;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .back-to-menu-button:hover {
            background: #c53030;
            color: white;
        }

        /* ==========================================================================
   ICONS
   ========================================================================== */
        .arrow-left::before {
            content: "←";
        }

        .checkmark::before {
            content: "✓";
        }

        .check-icon::before {
            content: "✓";
        }

        .warning-icon::before {
            content: "⚠";
        }

        .note-icon::before {
            content: "📝";
        }

        .user-icon::before {
            content: "👤";
        }

        .payment-icon::before {
            content: "💳";
        }

        .card-icon::before {
            content: "💳";
        }

        .radio-selected::before {
            content: "●";
        }

        .shield-icon::before {
            content: "🛡️";
        }

        .loading-spinner::before {
            content: "⏳";
        }

        .empty-cart-icon::before {
            content: "🛒";
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        /* ==========================================================================
   RESPONSIVE DESIGN
   ========================================================================== */
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .right-section {
                order: -1;
            }

            .order-total-card {
                position: static;
            }

            .header-container {
                padding: 0 15px;
            }

            .main-content {
                padding: 20px 15px;
            }

            .progress-line {
                width: 40px;
                margin: 0 10px;
            }

            .order-summary-header {
                padding: 16px 20px 12px 20px;
            }

            .order-summary-header h3 {
                font-size: 18px;
            }

            .vendor-info {
                padding: 0 20px 16px 20px;
            }

            .order-item {
                padding: 16px 20px;
                gap: 12px;
            }

            .item-image-wrapper {
                width: 56px;
                height: 56px;
            }

            .item-title {
                font-size: 15px;
            }

            .item-subtitle {
                font-size: 12px;
                margin-bottom: 10px;
            }

            .quantity-btn {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .quantity-display {
                min-width: 32px;
                font-size: 14px;
                padding: 0 6px;
            }

            .total-price {
                font-size: 15px;
            }

            .unit-price {
                font-size: 11px;
            }

            .card-header {
                padding: 16px 20px;
            }

            .instructions-content,
            .contact-form {
                padding: 20px;
            }

            .place-order-button {
                margin: 16px 20px 12px 20px;
                padding: 14px 20px;
                font-size: 15px;
            }

            .security-info {
                padding: 0 20px 20px 20px;
            }
        }

        @media (max-width: 480px) {
            .header-title h1 {
                font-size: 20px;
            }

            .header-title p {
                font-size: 13px;
            }

            .progress-container {
                padding: 20px 0;
            }

            .step-circle {
                width: 28px;
                height: 28px;
                font-size: 13px;
            }

            .step span {
                font-size: 13px;
            }

            .progress-line {
                width: 30px;
                margin: 0 8px;
            }

            .main-content {
                padding: 15px 10px;
            }

            .order-summary-header {
                padding: 14px 16px 10px 16px;
            }

            .vendor-info {
                padding: 0 16px 14px 16px;
            }

            .order-item {
                padding: 14px 16px;
                gap: 10px;
            }

            .item-image-wrapper {
                width: 48px;
                height: 48px;
                border-radius: 8px;
            }

            .item-title {
                font-size: 14px;
            }

            .item-subtitle {
                font-size: 11px;
                margin-bottom: 8px;
            }

            .quantity-controls {
                padding: 1px;
            }

            .quantity-btn {
                width: 26px;
                height: 26px;
                font-size: 13px;
            }

            .quantity-display {
                min-width: 28px;
                font-size: 13px;
                padding: 0 4px;
            }

            .delete-item-btn {
                font-size: 14px;
                padding: 2px;
            }

            .total-price {
                font-size: 14px;
            }

            .unit-price {
                font-size: 10px;
            }

            .card-header h3 {
                font-size: 16px;
            }

            .breakdown-row {
                font-size: 13px;
            }

            .breakdown-row.total {
                font-size: 15px;
            }

            .breakdown-row .total-price {
                font-size: 16px;
            }

            .payment-type {
                font-size: 14px;
            }

            .place-order-button {
                font-size: 14px;
            }

            .empty-content {
                padding: 40px 20px;
            }

            .empty-content h3 {
                font-size: 18px;
            }
        }
    </style>

</div>


<script>
    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized for checkout');

        // Handle successful order completion
        Livewire.on('order-completed', (event) => {
            console.log('=== ORDER COMPLETED EVENT ===');
            console.log('Raw event:', event);

            // Handle both array and object formats
            let orderGroupId, paymentMethod;

            if (Array.isArray(event) && event.length > 0) {
                // Livewire 3 format: array of objects
                orderGroupId = event[0].orderGroupId || event[0];
                paymentMethod = event[1] || event[0].paymentMethod;
            } else if (typeof event === 'object') {
                // Object format
                orderGroupId = event.orderGroupId;
                paymentMethod = event.paymentMethod;
            } else {
                console.error('Unexpected event format:', event);
                alert('Error: Invalid order data received. Please contact support with order details.');
                return;
            }

            console.log('Parsed - OrderGroupId:', orderGroupId, 'PaymentMethod:', paymentMethod);

            // Validate orderGroupId
            if (!orderGroupId || orderGroupId === 'undefined' || orderGroupId === null) {
                console.error('Invalid orderGroupId:', orderGroupId);
                alert('Error: Order was created but redirect failed. Please check your order history.');
                return;
            }

            // Convert to integer
            orderGroupId = parseInt(orderGroupId, 10);

            if (isNaN(orderGroupId)) {
                console.error('orderGroupId is not a number:', orderGroupId);
                alert('Error: Invalid order ID. Please check your order history.');
                return;
            }

            console.log('Redirecting to success page for order:', orderGroupId);

            // Redirect immediately (remove the setTimeout delay)
            window.location.href = '/checkout/success/' + orderGroupId;
        });

        // Keep your existing redirect-to-payment handler
        Livewire.on('redirect-to-payment', (event) => {
            console.log('Raw redirect-to-payment event:', event);

            const eventData = Array.isArray(event) ? event[0] : event;
            console.log('Payment redirect data:', eventData);

            if (eventData && eventData.url) {
                sessionStorage.setItem('pendingPayment', JSON.stringify({
                    paymentId: eventData.paymentId,
                    timestamp: Date.now()
                }));

                console.log('Redirecting to PayMongo:', eventData.url);
                window.location.href = eventData.url;
            } else {
                console.error('No redirect URL received:', eventData);
            }
        });

        // Auto-scroll to errors
        Livewire.on('checkout-error', () => {
            const errorElement = document.querySelector('.alert.error');
            if (errorElement) {
                errorElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    });
</script>

<script>
    function executeRecaptchaCheckout(event) {
        event.preventDefault();

        console.log('Place Order clicked - executing reCAPTCHA...');

        if (typeof grecaptcha === 'undefined') {
            console.error('reCAPTCHA not loaded!');
            alert('Security system not loaded. Please refresh the page.');
            return false;
        }

        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                action: '{{ Auth::check() ? 'checkout' : 'guest_checkout' }}'
            }).then(function(token) {
                console.log('reCAPTCHA token received:', token.substring(0, 20) + '...');

                // Get the Livewire component from the button
                const component = Livewire.find(event.target.closest('[wire\\:id]').getAttribute(
                    'wire:id'));

                if (component) {
                    console.log('Setting token...');
                    component.set('recaptcha_token', token);

                    console.log('Calling submitOrder...');
                    component.call('submitOrder');
                } else {
                    console.error('Livewire component not found!');
                    alert('Error processing order. Please refresh the page.');
                }
            }).catch(function(error) {
                console.error('reCAPTCHA error:', error);
                alert('Security verification failed. Please refresh the page and try again.');
            });
        });
    }


    // Alpine.js component for phone number formatting

    // Initialize default phone value when component mounts
    document.addEventListener('livewire:init', () => {
        // Set default phone number to '09' when notification preference includes SMS
        Livewire.on('notificationPreferenceChanged', () => {
            const phoneInput = document.querySelector('input[wire\\:model\\.blur="customerPhone"]');
            if (phoneInput && (!phoneInput.value || phoneInput.value.length < 2)) {
                phoneInput.value = '09';
                phoneInput.dispatchEvent(new Event('input'));
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(index, productName) {
        Swal.fire({
            title: 'Remove Item?',
            text: `Are you sure you want to remove "${productName}" from your cart?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('removeItem', index);

                Swal.fire({
                    title: 'Removed!',
                    text: 'Item has been removed from your cart.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }
</script>
