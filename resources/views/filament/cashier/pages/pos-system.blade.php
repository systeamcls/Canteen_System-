{{-- resources/views/filament/cashier/pages/pos-system.blade.php --}}
<div class="pos-system-wrapper" wire:ignore.self>
    <style>
        /* White, Red, Orange color scheme */
        .pos-container {
            display: flex;
            height: 100vh;
            background: #fefefe;
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            color: #1a1a1a;
        }

        .dark .pos-container {
            background: #1a1a1a;
            color: #fefefe;
        }

        .products-panel {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #f0f0f0;
            min-width: 0;
        }

        .dark .products-panel {
            background: #2a2a2a;
            border-right-color: #404040;
        }

        .header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .dark .header {
            border-bottom-color: #404040;
        }

        .search-bar {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            border: 2px solid #f0f0f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            background: #fafafa;
            color: #1a1a1a;
            transition: all 0.2s;
        }

        .dark .search-input {
            border-color: #505050;
            background: #3a3a3a;
            color: #fefefe;
        }

        .search-input:focus {
            outline: none;
            border-color: #dc2626;
            background: #ffffff;
        }

        .dark .search-input:focus {
            background: #2a2a2a;
            border-color: #ef4444;
        }

        .search-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666666;
        }

        .stock-alert {
            background: #fed7d7;
            color: #c53030;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid #fc8181;
        }

        .dark .stock-alert {
            background: #4a1a1a;
            color: #fc8181;
            border-color: #e53e3e;
        }

        .categories {
            display: flex;
            gap: 0.5rem;
            padding: 0 1.5rem 1.25rem;
            overflow-x: auto;
        }

        .category-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 1.25rem;
            background: #f5f5f5;
            color: #666666;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .dark .category-btn {
            background: #3a3a3a;
            color: #b0b0b0;
        }

        .category-btn.active,
        .category-btn:hover {
            background: #dc2626;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        }

        .products-content {
            flex: 1;
            overflow-y: auto;
            padding: 0 1.5rem 1.5rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
        }

        .dark .section-title {
            color: #fefefe;
        }

        .products-count {
            color: #666666;
            font-size: 0.875rem;
            background: #f5f5f5;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
        }

        .dark .products-count {
            background: #3a3a3a;
            color: #b0b0b0;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid #f0f0f0;
            border-radius: 0.75rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .dark .product-card {
            background: #2a2a2a;
            border-color: #404040;
        }

        .product-card:hover {
            border-color: #dc2626;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.15);
        }

        .dark .product-card:hover {
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3);
        }

        .product-image {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            margin: 0 auto 0.75rem;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background-size: cover;
            background-position: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .dark .product-image {
            background: #3a3a3a;
            width: 5rem;
            height: 5rem;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.25rem;
        }

        .dark .product-name {
            color: #fefefe;
        }

        .product-price {
            font-size: 1rem;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 0.5rem;
        }

        .product-availability {
            font-size: 0.75rem;
            color: #059669;
            background: #d1fae5;
            padding: 0.125rem 0.5rem;
            border-radius: 0.75rem;
            display: inline-block;
        }

        .dark .product-availability {
            background: #064e3b;
            color: #6ee7b7;
        }

        .cart-panel {
            width: 400px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            border-left: 1px solid #f0f0f0;
        }

        .dark .cart-panel {
            background: #2a2a2a;
            border-left-color: #404040;
        }

        .cart-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .dark .cart-header {
            border-bottom-color: #404040;
        }

        .order-number {
            font-size: 0.875rem;
            color: #666666;
        }

        .order-id {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 1rem 0;
        }

        .dark .order-id {
            color: #fefefe;
        }

        .order-actions {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .order-type-btn {
            flex: 1;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            background: #f5f5f5;
            color: #666666;
            transition: all 0.2s;
        }

        .dark .order-type-btn {
            background: #3a3a3a;
            color: #b0b0b0;
        }

        .order-type-btn.active {
            background: #ea580c;
            color: #ffffff;
            transform: scale(1.02);
        }

        .customer-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            display: block;
        }

        .dark .customer-label {
            color: #fefefe;
        }

        .customer-input {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid #f0f0f0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background: #fafafa;
            color: #1a1a1a;
            transition: all 0.2s;
        }

        .dark .customer-input {
            border-color: #505050;
            background: #3a3a3a;
            color: #fefefe;
        }

        .customer-input:focus {
            outline: none;
            border-color: #dc2626;
            background: #ffffff;
        }

        .dark .customer-input:focus {
            background: #2a2a2a;
        }

        .cart-content {
            flex: 1;
            overflow-y: auto;
            padding: 0 1.5rem;
        }

        .empty-cart {
            text-align: center;
            padding: 2.5rem 1.25rem;
            color: #666666;
        }

        .empty-cart-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .dark .cart-item {
            border-bottom-color: #3a3a3a;
        }

        .item-image {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            overflow: hidden;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
        }

        .dark .item-image {
            background: #3a3a3a;
            width: 3rem;
            height: 3rem;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .item-details {
            flex: 1;
            min-width: 0;
        }

        .item-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 0.125rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dark .item-name {
            color: #fefefe;
        }

        .item-price {
            font-size: 0.75rem;
            color: #666666;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qty-btn {
            width: 1.75rem;
            height: 1.75rem;
            border: 1px solid #f0f0f0;
            background: #ffffff;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.875rem;
            color: #666666;
            transition: all 0.2s;
        }

        .dark .qty-btn {
            border-color: #505050;
            background: #3a3a3a;
            color: #b0b0b0;
        }

        .qty-btn:hover {
            border-color: #dc2626;
            color: #dc2626;
            background: #fef2f2;
        }

        .dark .qty-btn:hover {
            background: #2a2a2a;
            border-color: #ef4444;
            color: #ef4444;
        }

        .qty-display {
            min-width: 1.5rem;
            text-align: center;
            font-weight: 500;
            color: #1a1a1a;
        }

        .dark .qty-display {
            color: #fefefe;
        }

        .remove-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
            color: #666666;
            transition: all 0.2s;
        }

        .remove-btn:hover {
            color: #dc2626;
        }

        .cart-summary {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .dark .cart-summary {
            border-top-color: #404040;
            background: #3a3a3a;
        }

        .payment-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 0.75rem;
        }

        .dark .payment-label {
            color: #fefefe;
        }

        .payment-methods {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .payment-btn {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #f0f0f0;
            background: #ffffff;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            color: #666666;
            transition: all 0.2s;
        }

        .dark .payment-btn {
            border-color: #505050;
            background: #2a2a2a;
            color: #b0b0b0;
        }

        .payment-btn.active {
            border-color: #ea580c;
            background: #fff7ed;
            color: #c2410c;
        }

        .dark .payment-btn.active {
            background: #3a1a0a;
            color: #fb923c;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.125rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.25rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f0f0f0;
        }

        .dark .summary-total {
            color: #fefefe;
            border-top-color: #505050;
        }

        .place-order-btn {
            width: 100%;
            padding: 1rem;
            background: #dc2626;
            color: #ffffff;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .place-order-btn:hover:not(:disabled) {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }

        .place-order-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .dark .place-order-btn:disabled {
            background: #4b5563;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
        }

        .modal {
            background: #ffffff;
            border-radius: 0.75rem;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .dark .modal {
            background: #080414;
        }
       .modal:has([style*="font-family: 'Courier New'"]) {
            background: #ffffff !important;
        }

        .modal-header {
            padding: 1.5rem 1.5rem 0;
            text-align: center;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
        }

        .dark .modal-title {
            color: #fefefe;
        }

        .modal-content {
            padding: 1.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            color: #1a1a1a;
        }

        .dark .detail-row {
            color: #fefefe;
        }

        .detail-value {
            font-weight: 600;
            text-transform: capitalize;
            color: #f05c0c;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .dark .order-item {
            border-bottom-color: #3a3a3a;
        }

        .modal-footer {
            padding: 0 1.5rem 1.5rem;
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary {
            background: #666666;
            color: #ffffff;
        }

        .btn-secondary:hover {
            background: #4a4a4a;
        }

        .btn-primary {
            background: #dc2626;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #b91c1c;
        }

        @media (max-width: 768px) {
            .pos-container {
                flex-direction: column;
            }
            .cart-panel {
                width: 100%;
                height: 50vh;
            }
            .products-panel {
                height: 50vh;
            }
        }

        /* Print styles for receipts */
        @media print {
            body * {
                visibility: hidden;
            }
            .modal-overlay, .modal-overlay * {
                visibility: visible;
            }
            .modal-overlay {
                position: static;
                background: white !important;
            }
            .modal {
                box-shadow: none !important;
                max-width: none !important;
                width: 100% !important;
            }
            .modal-footer {
                display: none !important;
            }
        }
    </style>

    <div class="pos-container">
        <!-- Left Panel - Products -->
        <div class="products-panel">
            <div class="header">
                <div class="search-bar">
                    <input 
                        type="text" 
                        class="search-input" 
                        placeholder="Search category or menu"
                        wire:model.live="searchTerm"
                    >
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                
                @if(isset($this->outOfStockCount) && $this->outOfStockCount > 0)
                    <div class="stock-alert">
                        <span>⚠️</span>
                        <span>{{ $this->outOfStockCount }} items out of stock</span>
                    </div>
                @endif
            </div>

            <div class="categories">
                <button 
                    class="category-btn {{ $selectedCategoryId === null ? 'active' : '' }}" 
                    wire:click="selectCategory(null)"
                >
                    🍽️ All Products
                </button>
                
                @foreach($categories as $category)
                    <button 
                        class="category-btn {{ $selectedCategoryId === $category->id ? 'active' : '' }}" 
                        wire:click="selectCategory({{ $category->id }})"
                    >
                        🍕 {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="products-content">
                <div class="section-header">
                    <h2 class="section-title">
                        @if($selectedCategoryId)
                            {{ $categories->find($selectedCategoryId)?->name ?? 'Products' }}
                        @else
                            All Products
                        @endif
                    </h2>
                    <span class="products-count">
                        {{ $filteredProducts->count() }} Products Available
                    </span>
                </div>

                <div class="products-grid">
                    @forelse($this->getFilteredProductsProperty() as $product)
                        <div class="product-card" wire:click="addToCart({{ $product->id }})">
                            <div class="product-image">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}"
                                         style="width: 5rem; height: 5rem; object-fit: cover; border-radius: 50%;"
                                         onerror="this.style.display='none'; this.parentNode.innerHTML='🍽️'; this.parentNode.style.fontSize='2rem'; this.parentNode.style.display='flex'; this.parentNode.style.alignItems='center'; this.parentNode.style.justifyContent='center';">
                                @else
                                    🍽️
                                @endif
                            </div>
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-price">₱{{ number_format($product->price / 100, 2) }}</div>
                            <div class="product-availability">Available</div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem; color: #666666;">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🔍</div>
                            <p>No products found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Panel - Cart -->
        <div class="cart-panel">
            <div class="cart-header">
                <div class="order-info">
                    <span class="order-number">Current Orders</span>
                </div>
                
                <div class="order-id">{{ $orderNumber }}</div>
                
                <div class="order-actions">
                    <button 
                        wire:click="$set('orderType', 'dine-in')"
                        class="order-type-btn {{ $orderType === 'dine-in' ? 'active' : '' }}"
                    >
                        Dine In
                    </button>
                    <button 
                        wire:click="$set('orderType', 'take-away')"
                        class="order-type-btn {{ $orderType === 'take-away' ? 'active' : '' }}"
                    >
                        Take Away
                    </button>
                </div>

                <div class="customer-section">
                    <label class="customer-label">Customer Name</label>
                    <input 
                        type="text" 
                        class="customer-input" 
                        placeholder="Enter customer name (optional)"
                        wire:model="customerName"
                    >
                </div>
            </div>

            <div class="cart-content">
                @if(empty($cart))
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <p>No items in cart</p>
                        <p style="font-size: 0.75rem; margin-top: 0.5rem;">Add products to get started</p>
                    </div>
                @else
                    <div class="cart-items" style="margin-bottom: 1.25rem;">
                        @foreach($cart as $key => $item)
                            <div class="cart-item">
                                <div class="item-image">
                                    @php
                                        $product = collect($this->products)->firstWhere('id', $item['id']);
                                        $imageUrl = $product && $product->image ? asset('storage/' . $product->image) : null;
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $item['name'] }}" onerror="this.style.display='none'; this.parentNode.innerHTML='🍽️';">
                                    @else
                                        🍽️
                                    @endif
                                </div>
                                <div class="item-details">
                                    <div class="item-name">{{ $item['name'] }}</div>
                                    <div class="item-price">₱{{ number_format($item['price'], 2) }}</div>
                                </div>
                                <div class="quantity-controls">
                                    <button wire:click="updateCartQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" class="qty-btn">−</button>
                                    <span class="qty-display">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateCartQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" class="qty-btn">+</button>
                                </div>
                                <button wire:click="removeFromCart('{{ $key }}')" class="remove-btn">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="cart-summary">
                <div class="payment-section">
                    <div class="payment-label">Payment Method</div>
                    <div class="payment-methods">
                        <button 
                            wire:click="$set('paymentMethod', 'cash')"
                            class="payment-btn {{ $paymentMethod === 'cash' ? 'active' : '' }}"
                        >
                            💰 Cash
                        </button>
                        <button 
                            wire:click="$set('paymentMethod', 'gcash')"
                            class="payment-btn {{ $paymentMethod === 'gcash' ? 'active' : '' }}"
                        >
                            📱 GCash
                        </button>
                    </div>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>₱{{ number_format($cartTotal, 2) }}</span>
                </div>

                <button 
                    wire:click="showCashInputModal"
                    class="place-order-btn"
                    {{ empty($cart) ? 'disabled' : '' }}
                >
                    Place Order
                </button>
            </div>
        </div>
    </div>

    <!-- Order Summary Modal -->
    @if($showOrderSummary)
        <div class="modal-overlay" wire:click="closeOrderSummary">
            <div class="modal" wire:click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Order Summary</h3>
                </div>
                <div class="modal-content">
                    <div style="margin-bottom: 1.25rem;">
                        <h4 class="section-title">Order Details</h4>
                        <div class="detail-row">
                            <span>Order Number:</span>
                            <span class="detail-value">{{ $orderNumber }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Customer:</span>
                            <span class="detail-value">{{ $customerName ?: 'Walk-in Customer' }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Order Type:</span>
                            <span class="detail-value">{{ str_replace('-', ' ', $orderType) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Payment:</span>
                            <span class="detail-value">{{ $paymentMethod === 'gcash' ? 'GCash' : 'Cash' }}</span>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <h4 class="section-title">Items Ordered</h4>
                        @foreach($cart as $item)
                            <div class="order-item">
                                <div>
                                    <div style="font-weight: 500;">{{ $item['name'] }}</div>
                                    <div style="color: #666666; font-size: 0.75rem;">₱{{ number_format($item['price'], 2) }} × {{ $item['quantity'] }}</div>
                                </div>
                                <div style="font-weight: 600;">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="padding-top: 0.75rem; border-top: 2px solid #f0f0f0;">
                        <div style="display: flex; justify-content: space-between; font-size: 1.125rem; font-weight: 700; color:#FFFF;">
                            <span>Total Amount:</span>
                            <span>₱{{ number_format($cartTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeOrderSummary" class="btn btn-secondary">Cancel</button>
                    <button wire:click="confirmOrder" class="btn btn-primary">Confirm Order</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Cash Input Modal -->
@if($showCashInput)
    <div class="modal-overlay" wire:click="closeCashInput">
        <div class="modal" wire:click.stop>
            <div class="modal-header">
                <h3 class="modal-title">Cash Payment</h3>
            </div>
            <div class="modal-content">
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: #1a1a1a;">
                        <span>Total Amount:</span>
                        <span>₱{{ number_format($cartTotal, 2) }}</span>
                    </div>
                    
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #1a1a1a;">Cash Received:</label>
                    <input 
                        type="number" 
                        inputmode="decimal"
                        wire:model.live="cashReceived"
                        style="width: 100%; padding: 0.75rem; border: 2px solid #f0f0f0; border-radius: 0.5rem; font-size: 1.125rem; text-align: right; background: #fafafa; color: #1a1a1a;"
                        placeholder="Enter amount received"
                        autofocus
                    >
                    
                    @if($cashReceived > 0)
                        <div style="margin-top: 1rem; padding: 1rem; background: #f0f9ff; border-radius: 0.5rem; border: 1px solid #0ea5e9;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #1a1a1a;">
                                <span>Cash Received:</span>
                                <span>₱{{ number_format($cashReceived, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 1.125rem; font-weight: 700; color: {{ $changeAmount >= 0 ? '#059669' : '#dc2626' }};">
                                <span>Change:</span>
                                <span>₱{{ number_format($changeAmount, 2) }}</span>
                            </div>
                            @if($cashReceived < $cartTotal)
                                <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">
                                    Insufficient amount. Need ₱{{ number_format($cartTotal - $cashReceived, 2) }} more.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="closeCashInput" class="btn btn-secondary">Cancel</button>
                <button 
    wire:click="processCashPayment" 
    class="btn btn-primary"
    {{ $cashReceived < $cartTotal ? 'disabled' : '' }}
>
    Confirm Payment
</button>
            </div>
        </div>
    </div>
@endif

    <!-- Receipt Modal -->
    @if($showReceipt)
        <div class="modal-overlay" wire:click="closeReceipt">
            <div class="modal receipt-modal" wire:click.stop>
                <div style="padding: 1.5rem; font-family: 'Courier New', monospace; line-height: 1.4;">
                    
                    <!-- Header with dotted lines -->
                    <div style="text-align: center; margin-bottom: 1rem;">
                        <div style="border-bottom: 1px dotted #000; margin-bottom: 0.5rem;"></div>
                        <h2 style="font-size: 1rem; font-weight: bold; margin: 0.5rem 0; color: #000;">LTO CANTEEN CENTRAL</h2>
                        <p style="font-size: 0.8rem; margin: 0; color: #000;">Quezon, METRO MANILA</p>
                        <div style="border-bottom: 1px dotted #000; margin: 0.5rem 0;"></div>
                    </div>

                    <!-- Receipt title -->
                    <div style="text-align: center; margin: 1rem 0;">
                        <h3 style="font-size: 0.9rem; font-weight: bold; margin: 0; color: #000;">*** RECEIPT ***</h3>
                    </div>

                    <!-- Order details section -->
                    <div style="font-size: 0.8rem; margin: 1rem 0; color: #000;">
                        @if(!empty($lastOrder))
                            <div>CASHIER #1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $lastOrder['created_at'] ?? now()->format('d/m/Y H:i') }} </div>
                            <div style="border-bottom: 1px dotted #000; margin: 0.5rem 0;"></div>
                            <div style="margin: 0.3rem 0;">ORDER #: {{ $lastOrder['order_number'] ?? 'POS-68C807FD29E0E' }}</div>
                            <div style="margin: 0.3rem 0;">CUSTOMER: {{ strtoupper($lastOrder['customer_name'] ?? 'WALK-IN CUSTOMER') }}</div>
                            <div style="margin: 0.3rem 0;">TYPE: {{ strtoupper(str_replace('-', ' ', $lastOrder['order_type'] ?? 'DINE IN')) }}</div>
                            <div style="margin: 0.3rem 0;">PAYMENT: {{ strtoupper($lastOrder['payment_method'] === 'gcash' ? 'GCASH' : 'CASH') }}</div>
                        @endif
                        <div style="border-bottom: 1px dotted #000; margin: 0.5rem 0;"></div>
                    </div>

                    <!-- Items section -->
                    <div style="font-size: 0.8rem; color: #000; margin: 1.5rem 0;">
                        @if(!empty($lastOrder['items']))
                            @foreach($lastOrder['items'] as $item)
                                <div style="margin: 0.8rem 0;">
                                    <div style="display: flex; justify-content: space-between;">
                                        <span>{{ strtoupper($item['name']) }}</span>
                                        <span>{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                    </div>
                                    <div style="margin-left: 1rem; font-size: 0.75rem; color: #666;">
                                        QTY: {{ $item['quantity'] }} x {{ number_format($item['price'], 2) }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div style="border-bottom: 1px dotted #000; margin: 1rem 0;"></div>

                    <!-- Total section -->
                    <div style="font-size: 0.8rem; color: #000;">
                        <div style="display: flex; justify-content: space-between; margin: 0.3rem 0;">
                            <span>SUBTOTAL</span>
                            <span>{{ number_format($lastOrder['total'] ?? 0, 2) }}</span>
                        </div>
                        <div style="border-bottom: 1px dotted #000; margin: 0.5rem 0;"></div>
                        <div style="display: flex; justify-content: space-between; margin: 0.3rem 0; font-weight: bold;">
                            <span>TOTAL AMOUNT</span>
                            <span>P{{ number_format($lastOrder['total'] ?? 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin: 0.3rem 0;">
                            <span>CASH</span>
                                <span>P{{ number_format($lastOrder['cash_received'] ?? $lastOrder['total'] ?? 0, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin: 0.3rem 0;">
                            <span>CHANGE</span>
                                <span>P{{ number_format($lastOrder['change_amount'] ?? 0, 2) }}</span>
                    </div>
                        <div style="display: flex; justify-content: space-between; margin: 0.3rem 0;">

                        </div>
                        <div style="border-bottom: 1px dotted #000; margin: 0.5rem 0;"></div>
                    </div>

                    <!-- Thank you message -->
                    <div style="text-align: center; margin: 1.5rem 0; font-size: 0.8rem; color: #000;">
                        <div style="font-weight: bold;">THANK YOU FOR ORDERING!</div>
                    </div>

                    <div style="border-bottom: 1px dotted #000; margin: 1rem 0;"></div>

                    <!-- Buttons replacing barcode -->
                    <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                        <button wire:click="closeReceipt" style="flex: 1; padding: 0.875rem 1rem; background: #6b7280; color: #ffffff; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; font-size: 0.9rem;">
                            Close
                        </button>
                        <button onclick="window.print()" style="flex: 1; padding: 0.875rem 1rem; background: #ea580c; color: #ffffff; border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer; font-size: 0.9rem;">
                            Print Receipt
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>