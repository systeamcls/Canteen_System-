<div class="cart-container" x-data="{ open: @entangle('isOpen') }">

    <!-- Modern Cart Button -->
    <button @click="open = true" class="modern-cart-button">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2E5BBA" stroke-width="2">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 7M7 13l1.5 7m0 0h8m-8 0h.01M19 20h.01"/>
        </svg>
        @if(($cartTotals['item_count'] ?? 0) > 0)
            <span class="modern-cart-badge">{{ $cartTotals['item_count'] }}</span>
        @endif
    </button>

    <!-- Modern Cart Overlay -->
    <div class="modern-cart-overlay" 
         x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         style="display: none;">
        
        <div class="modern-cart-sidebar" 
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="transform translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform translate-x-full"
             @click.stop>
             
            <!-- Header -->
            <div class="modern-cart-header orange-header">
                <div class="header-left">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 7M7 13l1.5 7m0 0h8m-8 0h.01M19 20h.01"/>
                    </svg>
                    <div class="header-text">
                        <h3>My Cart</h3>
                        @if(($cartTotals['item_count'] ?? 0) > 0)
                            <span class="item-count-text">{{ $cartTotals['item_count'] }} items</span>
                        @endif
                    </div>
                </div>
                <button @click="open = false" class="modern-close-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Cart Content -->
            <div class="modern-cart-content">
                @if(empty($cartTotals['items']) || $cartTotals['items']->isEmpty())
                    <div class="empty-cart-state">
                        <div class="empty-cart-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5">
                                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 7M7 13l1.5 7m0 0h8m-8 0h.01M19 20h.01"/>
                            </svg>
                        </div>
                        <h4>Your cart is empty</h4>
                        <p>Add some delicious items to get started</p>
                    </div>
                @else
                    @php
                        $groupedItems = collect($cartTotals['items'])->groupBy('vendor_id');
                    @endphp
                    
                    @foreach($groupedItems as $vendorId => $vendorItems)
                        <div class="vendor-section">
                            <div class="vendor-header">
                                <span class="vendor-icon">🏪</span>
                                <span class="vendor-name">{{ $vendorItems->first()->vendor->name ?? 'Unknown Vendor' }}</span>
                            </div>
                            
                            @foreach($vendorItems as $item)
                                <div class="modern-cart-item">
                                    <div class="item-image-placeholder">
                                        @if(isset($item->product->image) && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="product-image">
                                        @else
                                            <img src="{{ asset('images/default-product.png') }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="product-image">
                                        @endif
                                    </div>
                                    
                                    <div class="item-details">
                                        <h5 class="item-name">{{ $item->product->name }}</h5>
                                        <p class="item-price">{{ $this->formatPrice($item->unit_price) }} each</p>
                                    </div>
                                    
                                    <div class="modern-quantity-controls">
                                        <button 
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            class="qty-btn minus-btn"
                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                        <span class="qty-display">{{ $item->quantity }}</span>
                                        <button 
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            class="qty-btn plus-btn">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <button wire:click="removeItem({{ $item->id }})"
                                            class="remove-item-btn">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3,6 5,6 21,6"></polyline>
                                            <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"></path>
                                        </svg>
                                    </button>
                                    
                                    <div class="item-total">
                                        {{ $this->formatPrice($item->line_total) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Footer -->
            @if(!empty($cartTotals['items']) && $cartTotals['items']->isNotEmpty())
                <div class="modern-cart-footer">
                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>{{ $this->formatPrice($cartTotals['subtotal']) }}</span>
                        </div>
                        @if($cartTotals['tax'] > 0)
                            <div class="summary-row">
                                <span>Tax</span>
                                <span>{{ $this->formatPrice($cartTotals['tax']) }}</span>
                            </div>
                        @endif
                        <div class="summary-total">
                            <span>Total</span>
                            <span>{{ $this->formatPrice($cartTotals['total']) }}</span>
                        </div>
                        @if($cartTotals['vendor_count'] > 1)
                            <p class="vendor-note">Items from {{ $cartTotals['vendor_count'] }} vendors</p>
                        @endif
                    </div>
                    
                    <div class="action-buttons">
                        <button onclick="window.location.href='{{ route('checkout') }}'"
                                class="checkout-button">
                            Proceed to Checkout
                        </button>
                        
                        <button wire:click="clearCart"
                                onclick="return confirm('Are you sure you want to clear your cart?')"
                                class="clear-cart-button">
                            Clear Cart
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modern Styles -->
    <style>
    .cart-container {
        position: relative;
    }

    .modern-cart-button {
        position: relative;
        background: white;
        border: 2px solid #e2e8f0;
        padding: 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }

    .modern-cart-button:hover {
        border-color: #2E5BBA;
        box-shadow: 0 4px 12px rgba(46,91,186,0.15);
    }

    .modern-cart-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    .modern-cart-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.4);
        z-index: 1000;
        display: flex;
        justify-content: flex-end;
        align-items: stretch;
    }

    .modern-cart-sidebar {
        width: 420px;
        height: 100vh;
        background: white;
        display: flex;
        flex-direction: column;
        box-shadow: -8px 0 25px rgba(0,0,0,0.15);
    }

    /* UPDATED: Orange Header Styles */
    .modern-cart-header {
        padding: 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .orange-header {
        background: linear-gradient(135deg, #FF6B47 0%, #FF8A65 100%);
        border-bottom: none;
        padding: 20px 24px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-text h3 {
        font-size: 22px;
        font-weight: 700;
        color: white;
        margin: 0 0 2px 0;
    }

    .item-count-text {
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        font-weight: 500;
        margin: 0;
    }

    .modern-close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: white;
        transition: all 0.2s;
    }

    .modern-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .modern-cart-content {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }

    .empty-cart-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-cart-icon {
        margin-bottom: 24px;
    }

    .empty-cart-state h4 {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        margin: 0 0 8px 0;
    }

    .empty-cart-state p {
        color: #6b7280;
        margin: 0;
        font-size: 14px;
    }

    .vendor-section {
        margin-bottom: 32px;
    }

    .vendor-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        padding: 12px 16px;
        background: #fff7ed;
        border-radius: 12px;
        border-left: 4px solid #FF6B47;
    }

    .vendor-icon {
        font-size: 16px;
    }

    .vendor-name {
        font-weight: 600;
        color: #FF6B47;
        font-size: 14px;
    }

    .modern-cart-item {
        display: grid;
        grid-template-columns: 48px 1fr auto auto auto;
        gap: 12px;
        align-items: center;
        padding: 16px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }

    .modern-cart-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .item-image-placeholder {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .item-details {
        min-width: 0;
    }

    .item-name {
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px 0;
        font-size: 15px;
        line-height: 1.3;
    }

    .item-price {
        color: #64748b;
        margin: 0;
        font-size: 13px;
    }

    .modern-quantity-controls {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border-radius: 8px;
        padding: 4px;
        gap: 4px;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: white;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: #64748b;
    }

    .qty-btn:hover {
        background: #f1f5f9;
        color: #374151;
    }

    .qty-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .qty-display {
        padding: 0 12px;
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
        min-width: 20px;
        text-align: center;
    }

    .remove-item-btn {
        background: #fef2f2;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #dc2626;
        transition: all 0.2s;
    }

    .remove-item-btn:hover {
        background: #fee2e2;
    }

    .item-total {
        font-weight: 700;
        color: #FF6B47;
        font-size: 15px;
        text-align: right;
    }

    .modern-cart-footer {
        border-top: 1px solid #e2e8f0;
        padding: 24px;
        background: #f8fafc;
    }

    .order-summary {
        margin-bottom: 24px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        color: #64748b;
        font-size: 14px;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 18px;
        color: #FF6B47;
    }

    .vendor-note {
        font-size: 12px;
        color: #64748b;
        margin: 8px 0 0 0;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .checkout-button {
        width: 100%;
        background: linear-gradient(135deg, #FF6B47 0%, #FF8A65 100%);
        color: white;
        padding: 16px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .checkout-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(255, 107, 71, 0.3);
    }

    .clear-cart-button {
        width: 100%;
        background: white;
        color: #64748b;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .clear-cart-button:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    </style>
</div>