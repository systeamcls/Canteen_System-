<div class="modern-product-card">
    <!-- Product Image -->
    <div class="modern-product-image">
        <!-- FIXED: Use model accessor for image URL -->
        <img src="{{ $product->image_url }}" 
             alt="{{ $product->name }}" 
             class="product-img">
        
        @if(!$product->image)
            <div class="no-image-overlay">
                <div class="food-icon">🍽️</div>
            </div>
        @endif
        
        <!-- Badges -->
<div class="product-badges">
    @if($product->is_available)
        <span class="available-badge">Available</span>
    @else
        <span class="unavailable-badge">Unavailable</span>
    @endif
</div>
</div>

<!-- Product Info -->
<div class="modern-product-info">
    <!-- Vendor Name -->
    <div class="vendor-tag">
        {{ $product->stall->name }}
    </div>

    <!-- Product Name -->
    <h3 class="modern-product-name">{{ $product->name }}</h3>

    <!-- Description -->
    <p class="modern-product-description">
        {{ Str::limit($product->description ?: 'Fresh and delicious', 50) }}
    </p>

    <!-- Price and Controls Row -->
    <div class="price-controls-row">
        <!-- Price Section -->
        <div class="price-section">
            @if($showPrice)
                <div class="current-price">{{ $this->formatPrice($product->price) }}</div>
            @endif
        </div>

            <!-- Add to Cart Controls -->
            <div class="add-to-cart-controls">
                @if($product->is_available)
                    @if($showQuantitySelector)
                        <!-- Quantity Selector Mode -->
                        <div class="quantity-row">
                            <div class="modern-quantity-selector">
                                <button 
                                    wire:click="decrementQuantity"
                                    class="qty-control-btn"
                                    {{ $quantity <= 1 ? 'disabled' : '' }}>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                                <span class="qty-number">{{ $quantity }}</span>
                                <button 
                                    wire:click="incrementQuantity"
                                    class="qty-control-btn"
                                    {{ $quantity >= 99 ? 'disabled' : '' }}>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                            </div>
                            <button 
                                wire:click="addToCart"
                                class="modern-add-button full-width"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                                <span wire:loading wire:target="addToCart">Adding...</span>
                            </button>
                        </div>
                    @else
                        <!-- Simple Add Button -->
                        <button 
                            wire:click="toggleQuantitySelector"
                            class="modern-add-button">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add
                        </button>
                    @endif
                @else
                    <button class="unavailable-button" disabled>
                        Unavailable
                    </button>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if($message && $messageType === 'success')
            <div class="success-message">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20,6 9,17 4,12"></polyline>
                </svg>
                {{ $message }}
            </div>
        @endif

        @if($message && $messageType === 'error')
            <div class="error-message">
                {{ $message }}
            </div>
        @endif
    </div>

    <!-- Modern Styles -->
    <style>
    .modern-product-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.3s;
        overflow: hidden;
        position: relative;
    }

    .modern-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(46,91,186,0.15);
    }

    .modern-product-image {
        height: 160px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* ADDED: Product image styles */
    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .no-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(239, 246, 255, 0.9);
    }

    .food-icon {
        font-size: 48px;
        opacity: 0.5;
    }

    .modern-product-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-badges {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        z-index: 2;
    }

    .discount-badge {
        background: #f59e0b;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        text-align: center;
    }

    .available-badge {
        background: #10b981;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-align: center;
    }

    .unavailable-badge {
        background: #ef4444;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-align: center;
    }

    .modern-product-info {
        padding: 16px;
    }

    .vendor-tag {
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }

    .modern-product-name {
        color: #1e293b;
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 8px 0;
        line-height: 1.3;
    }

    .modern-product-description {
        color: #64748b;
        font-size: 13px;
        line-height: 1.4;
        margin: 0 0 16px 0;
    }

    .price-controls-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 12px;
    }

    .price-section {
        flex: 1;
    }

    .current-price {
        color: #2E5BBA;
        font-size: 18px;
        font-weight: 800;
        line-height: 1;
    }

    .original-price {
        color: #94a3b8;
        font-size: 12px;
        text-decoration: line-through;
        margin-top: 2px;
    }

    .add-to-cart-controls {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
    }

    .quantity-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }

    .modern-quantity-selector {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border-radius: 20px;
        padding: 4px;
        gap: 4px;
        align-self: flex-end;
    }

    .qty-control-btn {
        width: 24px;
        height: 24px;
        border: none;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: #64748b;
    }

    .qty-control-btn:hover {
        background: #f1f5f9;
        color: #374151;
    }

    .qty-control-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .qty-number {
        padding: 0 12px;
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
        min-width: 20px;
        text-align: center;
    }

    .modern-add-button {
        background: #2E5BBA;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 4px;
        min-width: 70px;
        justify-content: center;
    }

    .modern-add-button:hover {
        background: #1e40af;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(46,91,186,0.3);
    }

    .modern-add-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .modern-add-button.full-width {
        width: 100%;
        padding: 12px;
        font-size: 14px;
        min-width: auto;
    }

    .unavailable-button {
        background: #f1f5f9;
        color: #94a3b8;
        padding: 8px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: not-allowed;
    }

    .success-message {
        margin-top: 12px;
        padding: 8px 12px;
        background: #d1fae5;
        color: #065f46;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .error-message {
        margin-top: 12px;
        padding: 8px 12px;
        background: #fee2e2;
        color: #991b1b;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
    }
    </style>
</div>