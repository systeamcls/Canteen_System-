<div class="checkout-page">
    <!-- Red Header Section -->
    <div class="checkout-header">
        <div class="header-container">
            <button class="back-button" onclick="history.back()">
                <i class="arrow-left"></i>
                Back
            </button>
            <div class="header-title">
                <h1>Checkout</h1>
                <p>Review your order</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="progress-container">
        <div class="progress-steps">
            <div class="step completed">
                <div class="step-circle">
                    <i class="checkmark"></i>
                </div>
                <span>Menu</span>
            </div>
            <div class="progress-line completed"></div>
            <div class="step active">
                <div class="step-circle">2</div>
                <span>Details</span>
            </div>
            <div class="progress-line"></div>
            <div class="step">
                <div class="step-circle">3</div>
                <span>Payment</span>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if($successMessage)
        <div class="alert success">
            <i class="check-icon"></i>
            {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="alert error">
            <i class="warning-icon"></i>
            {{ $errorMessage }}
        </div>
    @endif

    <!-- Main Content -->
    @if(!empty($cartSnapshot))
        <div class="main-content">
            <form wire:submit.prevent="submitOrder" class="checkout-form">
                <div class="content-grid">
                    <!-- Left Column: Order Summary & Special Instructions -->
                    <div class="left-section">
                        <!-- Order Summary Card -->
                        <div class="order-summary-card">
                            <div class="card-header">
                                <i class="cart-icon"></i>
                                <h3>Order Summary</h3>
                            </div>
                            <div class="order-items">
                                @php 
                                    $currentVendor = null;
                                    $vendorItemCount = [];
                                    // Count items per vendor
                                    foreach($cartSnapshot as $item) {
                                        $vendorName = $item['vendor_name'];
                                        $vendorItemCount[$vendorName] = ($vendorItemCount[$vendorName] ?? 0) + 1;
                                    }
                                @endphp
                                
                                @foreach($cartSnapshot as $index => $item)
                                    @if($currentVendor !== $item['vendor_name'])
                                        @php 
                                            $currentVendor = $item['vendor_name'];
                                            $itemNumber = $vendorItemCount[$currentVendor];
                                        @endphp
                                        <div class="vendor-section">
                                            <div class="vendor-badge">
                                                <span class="vendor-number">{{ $itemNumber }}</span>
                                                <span class="vendor-name">{{ $item['vendor_name'] }}</span>
                                                <div class="vendor-rating">
                                                    <i class="star-icon"></i>
                                                    4.{{ rand(5,9) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="food-item" wire:key="item-{{ $index }}">
                                        <div class="item-image-container">
                                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=80&h=80&fit=crop&crop=center" alt="{{ $item['product_name'] }}" class="food-image">
                                        </div>
                                        <div class="item-content">
                                            <h4 class="item-name">{{ $item['product_name'] }}</h4>
                                            <p class="item-description">Traditional Filipino cuisine with authentic flavors</p>
                                            <div class="quantity-controls">
                                                <button type="button" class="qty-btn minus">-</button>
                                                <span class="quantity">{{ $item['quantity'] }}</span>
                                                <button type="button" class="qty-btn plus">+</button>
                                            </div>
                                        </div>
                                        <div class="item-actions">
                                            <div class="item-pricing">
                                                <div class="item-total">₱{{ number_format($item['line_total'], 0) }}</div>
                                                <div class="item-unit-price">₱{{ number_format($item['unit_price'], 0) }} each</div>
                                            </div>
                                            <div class="action-buttons">
                                                <button type="button" class="edit-btn">
                                                    <i class="edit-icon"></i>
                                                </button>
                                                <button type="button" class="delete-btn">
                                                    <i class="delete-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Special Instructions Card -->
                        <div class="special-instructions-card">
                            <div class="card-header">
                                <i class="note-icon"></i>
                                <h3>Special Instructions</h3>
                            </div>
                            <div class="instructions-content">
                                <textarea 
                                    wire:model="notes" 
                                    class="instructions-textarea @error('notes') error @enderror"
                                    placeholder="Add cooking instructions, allergies, or delivery notes..."
                                    rows="4"></textarea>
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
                            <div class="contact-form">
                                <div class="input-group">
                                    <label>Full Name</label>
                                    <input 
                                        type="text" 
                                        wire:model.blur="customerName" 
                                        class="text-input @error('customerName') error @enderror"
                                        placeholder="Enter your name">
                                    @error('customerName')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-group">
                                    <label>Phone Number</label>
                                    <input 
                                        type="tel" 
                                        wire:model.blur="customerPhone" 
                                        class="text-input @error('customerPhone') error @enderror"
                                        placeholder="Enter phone number">
                                    @error('customerPhone')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="input-group">
                                    <label>Email (Optional)</label>
                                    <input 
                                        type="email" 
                                        wire:model.blur="customerEmail" 
                                        class="text-input @error('customerEmail') error @enderror"
                                        placeholder="Enter email address">
                                    @error('customerEmail')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Order Total Card -->
                        <div class="order-total-card">
                            <!-- Order Breakdown -->
                            <div class="order-breakdown">
                                <div class="breakdown-row">
                                    <span>Subtotal</span>
                                    <span>₱{{ number_format($totalAmount, 0) }}</span>
                                </div>
                                <div class="breakdown-row">
                                    <span>Delivery Fee</span>
                                    <span>₱2,500</span>
                                </div>
                                <div class="breakdown-divider"></div>
                                <div class="breakdown-row total">
                                    <span>Total</span>
                                    <span class="total-price">₱{{ number_format($totalAmount + 2500, 0) }}</span>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="payment-section">
                                <div class="payment-header">
                                    <i class="payment-icon"></i>
                                    <span>Payment Method</span>
                                </div>
                                
                                @forelse($this->paymentMethodOptions as $method => $details)
                                    <label class="payment-method {{ $paymentMethod === $method ? 'selected' : '' }}">
                                        <input type="radio" wire:model.live="paymentMethod" value="{{ $method }}" class="payment-radio">
                                        <div class="payment-content">
                                            <div class="payment-check">
                                                <i class="radio-selected"></i>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-type">
                                                    <i class="card-icon"></i>
                                                    <span>{{ $details['label'] }}</span>
                                                </div>
                                                <div class="payment-desc">Credit/Debit Card</div>
                                            </div>
                                        </div>
                                    </label>
                                @empty
                                    <p class="no-payment">No payment methods available</p>
                                @endforelse
                                @error('paymentMethod')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Place Order Button -->
                            <button 
                                type="submit" 
                                class="place-order-button"
                                wire:loading.attr="disabled"
                                wire:target="submitOrder"
                                {{ $isProcessing ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="submitOrder">
                                    Place Order • ₱{{ number_format($totalAmount + 2500, 0) }}
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

    <!-- Styles -->
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .checkout-page {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f7fafc;
        min-height: 100vh;
    }

    /* Header Section */
    .checkout-header {
        background-color: #E53E3E;
        color: white;
        padding: 20px 0;
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .back-button {
        background: none;
        border: none;
        color: white;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        cursor: pointer;
        padding: 8px 0;
    }

    .back-button:hover {
        opacity: 0.8;
    }

    .header-title {
        flex: 1;
        text-align: center;
    }

    .header-title h1 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .header-title p {
        font-size: 14px;
        opacity: 0.9;
    }

    /* Progress Steps */
    .progress-container {
        background: white;
        padding: 24px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .progress-steps {
        max-width: 400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: #a0aec0;
        font-size: 14px;
        font-weight: 500;
    }

    .step.active {
        color: #E53E3E;
    }

    .step.completed {
        color: #E53E3E;
    }

    .step-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #718096;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .step.active .step-circle {
        background: #E53E3E;
        color: white;
    }

    .step.completed .step-circle {
        background: #E53E3E;
        color: white;
    }

    .progress-line {
        width: 60px;
        height: 2px;
        background: #e2e8f0;
        margin: 0 20px;
    }

    .progress-line.completed {
        background: #E53E3E;
    }

    /* Main Content */
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

    /* Cards */
    .order-summary-card,
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

    /* Order Items */
    .order-items {
        padding: 24px;
    }

    .vendor-section {
        margin-bottom: 20px;
    }

    .vendor-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f7fafc;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .vendor-number {
        background: #E53E3E;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
    }

    .vendor-name {
        font-weight: 600;
        color: #2d3748;
        flex: 1;
    }

    .vendor-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        color: #fbbf24;
        font-size: 14px;
        font-weight: 500;
    }

    .food-item {
        display: flex;
        gap: 16px;
        padding: 16px;
        border: 1px solid #f7fafc;
        border-radius: 8px;
        margin: 12px 0;
        background: #fdfdfd;
    }

    .item-image-container {
        width: 80px;
        height: 80px;
        flex-shrink: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .food-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-content {
        flex: 1;
    }

    .item-name {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 4px;
    }

    .item-description {
        font-size: 13px;
        color: #718096;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        color: #718096;
    }

    .qty-btn:hover {
        border-color: #E53E3E;
        color: #E53E3E;
    }

    .quantity {
        font-weight: 600;
        color: #2d3748;
        min-width: 20px;
        text-align: center;
    }

    .item-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    .item-pricing {
        text-align: right;
    }

    .item-total {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 2px;
    }

    .item-unit-price {
        font-size: 12px;
        color: #718096;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .edit-btn,
    .delete-btn {
        background: none;
        border: none;
        color: #E53E3E;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        font-size: 14px;
    }

    .edit-btn:hover,
    .delete-btn:hover {
        background: #fed7d7;
    }

    /* Special Instructions */
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

    /* Contact Form */
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

    /* Order Total */
    .order-total-card {
        position: sticky;
        top: 20px;
    }

    .order-breakdown {
        padding: 24px 24px 20px 24px;
    }

    .breakdown-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 14px;
        color: #718096;
    }

    .breakdown-row.total {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0;
    }

    .total-price {
        color: #E53E3E;
        font-size: 18px;
        font-weight: 700;
    }

    .breakdown-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 16px 0;
    }

    /* Payment Section */
    .payment-section {
        border-top: 1px solid #f7fafc;
        padding: 20px 24px;
    }

    .payment-header {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #E53E3E;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .payment-method {
        display: block;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
        margin-bottom: 8px;
    }

    .payment-method:hover {
        border-color: #E53E3E;
        background: #fef5f5;
    }

    .payment-method.selected {
        border-color: #E53E3E;
        background: #fef5f5;
    }

    .payment-radio {
        display: none;
    }

    .payment-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .payment-check {
        width: 20px;
        height: 20px;
        border: 2px solid #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: transparent;
        transition: all 0.2s;
    }

    .payment-method.selected .payment-check {
        border-color: #E53E3E;
        background: #E53E3E;
        color: white;
    }

    .payment-info {
        flex: 1;
    }

    .payment-type {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #2d3748;
        margin-bottom: 2px;
    }

    .payment-desc {
        color: #718096;
        font-size: 12px;
    }

    /* Place Order Button */
    .place-order-button {
        width: 100%;
        background: #E53E3E;
        color: white;
        border: none;
        padding: 16px 24px;
        border-radius: 8px;
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
        background: #c53030;
        transform: translateY(-1px);
    }

    .place-order-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* Security Info */
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

    /* Error Messages */
    .error-message {
        display: block;
        margin-top: 4px;
        color: #e53e3e;
        font-size: 12px;
        font-weight: 500;
    }

    /* Alerts */
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

    /* Empty Cart */
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

    /* Icons */
    .arrow-left::before { content: "←"; }
    .checkmark::before { content: "✓"; }
    .check-icon::before { content: "✓"; }
    .warning-icon::before { content: "⚠"; }
    .cart-icon::before { content: "🛒"; }
    .star-icon::before { content: "⭐"; }
    .edit-icon::before { content: "✏️"; }
    .delete-icon::before { content: "🗑️"; }
    .note-icon::before { content: "📝"; }
    .user-icon::before { content: "👤"; }
    .payment-icon::before { content: "💳"; }
    .card-icon::before { content: "💳"; }
    .radio-selected::before { content: "●"; }
    .shield-icon::before { content: "🛡️"; }
    .loading-spinner::before { content: "⏳"; }
    .empty-cart-icon::before { content: "🛒"; }

    /* Loading Animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-spinner {
        animation: spin 1s linear infinite;
    }

    /* Responsive Design */
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
        
        .progress-steps {
            gap: 0;
        }
        
        .progress-line {
            width: 40px;
            margin: 0 10px;
        }
        
        .food-item {
            flex-direction: column;
            gap: 12px;
        }
        
        .item-image-container {
            width: 100%;
            height: 120px;
            align-self: center;
            max-width: 200px;
        }
        
        .item-actions {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        
        .item-pricing {
            text-align: left;
        }
        
        .action-buttons {
            gap: 12px;
        }
        
        .qty-btn {
            width: 32px;
            height: 32px;
        }
        
        .card-header {
            padding: 16px 20px;
        }
        
        .order-items,
        .instructions-content,
        .contact-form {
            padding: 20px;
        }
        
        .vendor-badge {
            padding: 10px 12px;
        }
        
        .food-item {
            padding: 12px;
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
        
        .progress-steps {
            gap: 0;
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
        
        .card-header h3 {
            font-size: 16px;
        }
        
        .vendor-name {
            font-size: 14px;
        }
        
        .item-name {
            font-size: 15px;
        }
        
        .item-description {
            font-size: 12px;
        }
        
        .item-total {
            font-size: 16px;
        }
        
        .breakdown-row {
            font-size: 13px;
        }
        
        .breakdown-row.total {
            font-size: 15px;
        }
        
        .total-price {
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

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    console.log('Livewire initialized for checkout');
    
    // Handle successful order completion
    Livewire.on('order-completed', (event) => {
        console.log('=== ORDER COMPLETED EVENT DEBUG ===');
        console.log('Raw event:', event);
        console.log('Event type:', typeof event);
        console.log('Event is array:', Array.isArray(event));
        
        const eventData = Array.isArray(event) ? event[0] : event;
        console.log('Processed eventData:', eventData);
        
        if (eventData) {
            console.log('orderGroupId:', eventData.orderGroupId);
            console.log('orderGroupId type:', typeof eventData.orderGroupId);
            console.log('paymentMethod:', eventData.paymentMethod);
        } else {
            console.error('eventData is null or undefined!');
        }
        
        if (eventData && eventData.paymentMethod === 'cash') {
            if (eventData.orderGroupId) {
                console.log('Redirecting to:', '/checkout/success/' + eventData.orderGroupId);
                setTimeout(() => {
                    window.location.href = '/checkout/success/' + eventData.orderGroupId;
                }, 2000);
            } else {
                console.error('orderGroupId is missing or falsy:', eventData.orderGroupId);
            }
        } else {
            console.log('Online payment - waiting for redirect event');
        }
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
            errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endpush