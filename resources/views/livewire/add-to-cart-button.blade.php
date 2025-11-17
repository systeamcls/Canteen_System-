<div class="menu-product-card">
    <!-- Product Image -->
    <div class="product-image-container">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image"
            onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop'">

        <!-- Status Badge -->
        <div class="product-badges">
            @if (!$product->is_available)
                <span class="badge-unavailable">Unavailable</span>
            @elseif($product->id % 2 == 0)
                {{-- Example logic for popular items --}}
                <span class="badge-popular">Popular</span>
            @else
                <span class="badge-available">Available</span>
            @endif
        </div>
    </div>

    <!-- Product Content -->
    <div class="product-content">
        <!-- Stall Name -->
        <div class="stall-name">{{ strtoupper($product->stall->name) }}</div>

        <!-- Product Name -->
        <h3 class="product-name">{{ $product->name }}</h3>

        <!-- Product Description -->
        <p class="product-description">
            {{ Str::limit($product->description ?: 'Delicious and freshly prepared with quality ingredients.', 80) }}
        </p>

        <!-- Price and Add Button Row -->
        <div class="product-footer">
            <div class="price-section">
                @if ($showPrice)
                    <span class="product-price">{{ $this->formatPrice($product->price) }}</span>
                @endif
            </div>

            <!-- Stock Status Badge -->
            @if ($this->isOutOfStock())
                <div class="stock-badge out-of-stock">
                    🚫 Out of Stock
                </div>
            @elseif($this->isLowStock())
                <div class="stock-badge low-stock">
                    ⚠️ Only {{ $product->stock_quantity }} left!
                </div>
            @else
                <div class="stock-badge in-stock">
                    ✅ In Stock
                </div>
            @endif

            <!-- Add to Cart Controls -->
            <div class="cart-controls">
                @if ($product->is_available)
                    @if ($showQuantitySelector)
                        <!-- Quantity Selector Mode -->
                        <div class="quantity-controls">
                            <div class="quantity-selector">
                                <button wire:click="decrementQuantity" class="qty-btn qty-minus"
                                    {{ $quantity <= 1 ? 'disabled' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="3">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                                <span class="qty-display">{{ $quantity }}</span>
                                <button wire:click="incrementQuantity" class="qty-btn qty-plus"
                                    {{ $quantity >= 99 ? 'disabled' : '' }}>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="3">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                            </div>
                            <button wire:click="addToCart" class="add-to-cart-btn full-width"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="addToCart">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Add to Cart
                                </span>
                                <span wire:loading wire:target="addToCart">Adding...</span>
                            </button>
                        </div>
                    @else
                        <!-- Simple Add Button -->
                        <button wire:click="toggleQuantitySelector" class="add-to-cart-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add
                        </button>
                    @endif
                @else
                    <button class="unavailable-btn" disabled>
                        Unavailable
                    </button>
                @endif
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if ($message && $messageType === 'success')
            <div class="success-alert">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="20,6 9,17 4,12"></polyline>
                </svg>
                {{ $message }}
            </div>
        @endif

        @if ($message && $messageType === 'error')
            <div class="error-alert">
                {{ $message }}
            </div>
        @endif
    </div>

    <!-- Inline Styles for Component -->
    <style>
        /* Menu Product Card Styles - Matching Reference Design */
        .menu-product-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .menu-product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        /* Product Image */
        .product-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f8fafc;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .menu-product-card:hover .product-image {
            transform: scale(1.05);
        }

        /* Product Badges */
        .product-badges {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 10;
        }

        .badge-popular {
            background: #FF6B35;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-available {
            background: #10B981;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-unavailable {
            background: #EF4444;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Product Content */
        .product-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .stall-name {
            color: #9CA3AF;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .product-name {
            color: #1F2937;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-description {
            color: #6B7280;
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 20px 0;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Product Footer */
        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: auto;
        }

        .price-section {
            flex: 1;
        }

        .product-price {
            color: #FF6B35;
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        /* Cart Controls */
        .cart-controls {
            flex-shrink: 0;
        }

        .add-to-cart-btn {
            background: #FF6B35;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .add-to-cart-btn:hover {
            background: #E55B2B;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }

        .add-to-cart-btn:active {
            transform: translateY(0);
        }

        .add-to-cart-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .add-to-cart-btn.full-width {
            width: 100%;
            justify-content: center;
            margin-top: 8px;
            padding: 12px;
        }

        .unavailable-btn {
            background: #F3F4F6;
            color: #9CA3AF;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: not-allowed;
        }

        /* Quantity Controls */
        .quantity-controls {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            background: #F8FAFC;
            border-radius: 12px;
            padding: 4px;
            align-self: flex-end;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: white;
            color: #6B7280;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .qty-btn:hover:not(:disabled) {
            background: #F1F5F9;
            color: #374151;
        }

        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .qty-display {
            padding: 0 16px;
            font-weight: 600;
            color: #1F2937;
            font-size: 14px;
            min-width: 24px;
            text-align: center;
        }

        /* Alert Messages */
        .success-alert {
            margin-top: 12px;
            padding: 10px 12px;
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-alert {
            margin-top: 12px;
            padding: 10px 12px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Mobile Responsiveness */
        @media (max-width: 640px) {
            .product-image-container {
                height: 160px;
            }

            .product-content {
                padding: 16px;
            }

            .product-name {
                font-size: 16px;
                margin-bottom: 16px;
            }

            .product-description {
                display: none;
            }

            .product-price {
                font-size: 18px;
            }

            .add-to-cart-btn {
                padding: 8px 16px;
                font-size: 13px;
            }

            .add-to-cart-btn.full-width {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .product-image-container {
                height: 140px;
            }

            .product-content {
                padding: 14px;
            }

            .product-footer {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .add-to-cart-btn {
                width: 100%;
                justify-content: center;
            }

            .quantity-selector {
                align-self: stretch;
                justify-content: center;
            }
        }

        /* Ultra-small screens */
        @media (max-width: 360px) {
            .product-image-container {
                height: 120px;
            }

            .product-content {
                padding: 12px;
            }

            .stall-name {
                font-size: 11px;
            }

            .product-name {
                font-size: 15px;
            }

            .product-description {
                font-size: 12px;
            }

            .product-price {
                font-size: 16px;
            }
        }

        /* Stock Status Badges */
        .stock-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin: 8px 0;
        }

        .stock-badge.out-of-stock {
            background: #FEE2E2;
            color: #DC2626;
        }

        .stock-badge.low-stock {
            background: #FEF3C7;
            color: #D97706;
        }

        .stock-badge.in-stock {
            background: #DCFCE7;
            color: #16A34A;
        }
    </style>
</div>
