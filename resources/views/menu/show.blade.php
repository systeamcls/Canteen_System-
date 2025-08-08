@extends('layouts.canteen')

@section('title', $product->name . ' - KAJACMS')

@section('content')
<section style="padding: 40px 0;">
    <div class="container">
        <!-- Breadcrumb -->
        <nav style="margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 10px; color: #6b7280; font-size: 0.9rem;">
                <a href="{{ route('menu.index') }}" style="color: #ea580c; text-decoration: none;">Menu</a>
                <span>→</span>
                <a href="{{ route('stalls.show', $product->stall) }}" style="color: #ea580c; text-decoration: none;">{{ $product->stall->name }}</a>
                <span>→</span>
                <span>{{ $product->name }}</span>
            </div>
        </nav>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
            <!-- Product Image -->
            <div>
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x400?text=No+Image' }}" 
                     alt="{{ $product->name }}" 
                     style="width: 100%; height: 400px; object-fit: cover; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
            </div>

            <!-- Product Details -->
            <div>
                <!-- Category Badge -->
                <div style="margin-bottom: 15px;">
                    <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                        {{ ucfirst(str_replace('-', ' ', $product->category)) }}
                    </span>
                </div>

                <!-- Product Name -->
                <h1 style="font-size: 2.5rem; font-weight: 700; color: #374151; margin-bottom: 15px;">{{ $product->name }}</h1>

                <!-- Stall Info -->
                <div style="margin-bottom: 20px;">
                    <a href="{{ route('stalls.show', $product->stall) }}" 
                       style="color: #ea580c; text-decoration: none; font-weight: 600; font-size: 1.1rem;">
                        🏪 {{ $product->stall->name }}
                    </a>
                </div>

                <!-- Rating -->
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <span style="color: #fbbf24; font-size: 1.2rem;">⭐⭐⭐⭐⭐</span>
                        <span style="font-weight: 600; color: #374151;">4.8</span>
                        <span style="color: #6b7280;">(124 reviews)</span>
                    </div>
                </div>

                <!-- Price -->
                <div style="margin-bottom: 30px;">
                    <span style="font-size: 2.5rem; font-weight: 700; color: #ea580c;">₱{{ number_format($product->price, 2) }}</span>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 1.2rem; font-weight: 600; color: #374151; margin-bottom: 10px;">Description</h3>
                    <p style="color: #6b7280; line-height: 1.6;">{{ $product->description }}</p>
                </div>

                <!-- Availability -->
                <div style="margin-bottom: 30px;">
                    @if($product->is_available)
                        <span style="background: #d1fae5; color: #065f46; padding: 8px 15px; border-radius: 20px; font-weight: 600;">
                            ✅ Available
                        </span>
                    @else
                        <span style="background: #fee2e2; color: #991b1b; padding: 8px 15px; border-radius: 20px; font-weight: 600;">
                            ❌ Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Add to Cart Section -->
                @if($product->is_available)
                    <div style="background: #fff7ed; padding: 25px; border-radius: 12px; border: 2px solid #fed7aa;">
                        <h3 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 15px;">Add to Cart</h3>
                        
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                            <label style="font-weight: 600; color: #374151;">Quantity:</label>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <button onclick="decreaseQuantity()" 
                                        style="background: #fed7aa; border: none; width: 35px; height: 35px; border-radius: 6px; color: #ea580c; font-weight: 600; cursor: pointer;">
                                    -
                                </button>
                                <input type="number" id="quantity" value="1" min="1" max="10" 
                                       style="width: 60px; text-align: center; border: 2px solid #fed7aa; border-radius: 6px; padding: 8px; font-weight: 600;">
                                <button onclick="increaseQuantity()" 
                                        style="background: #fed7aa; border: none; width: 35px; height: 35px; border-radius: 6px; color: #ea580c; font-weight: 600; cursor: pointer;">
                                    +
                                </button>
                            </div>
                        </div>

                        <button onclick="addToCartWithQuantity()" 
                                class="btn btn-primary" 
                                style="width: 100%; background: #ea580c; border: none; padding: 15px; border-radius: 10px; color: white; font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: all 0.3s;">
                            🛒 Add to Cart - ₱<span id="totalPrice">{{ number_format($product->price, 2) }}</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
<section style="padding: 60px 0; background: #f9fafb;">
    <div class="container">
        <h2 style="text-align: center; font-size: 2rem; color: #ea580c; margin-bottom: 40px;">More from {{ $product->stall->name }}</h2>
        
        <div class="grid grid-4">
            @foreach($relatedProducts as $related)
                <div class="card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s;">
                    <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                         alt="{{ $related->name }}" 
                         style="width: 100%; height: 200px; object-fit: cover;">
                    
                    <div style="padding: 20px;">
                        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: #374151;">{{ $related->name }}</h3>
                        <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 15px;">{{ Str::limit($related->description, 60) }}</p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <span style="font-size: 1.2rem; font-weight: 700; color: #ea580c;">₱{{ number_format($related->price, 2) }}</span>
                            <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                {{ $related->category }}
                            </span>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary add-to-cart-btn" 
                                    style="flex: 1; background: #ea580c; border: none; padding: 10px; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;"
                                    data-product-id="{{ $related->id }}"
                                    data-product-name="{{ htmlspecialchars($related->name, ENT_QUOTES) }}"
                                    data-product-price="{{ $related->price }}"
                                    data-product-image="{{ $related->image ? htmlspecialchars($related->image, ENT_QUOTES) : '' }}">
                                Add to Cart
                            </button>
                            <a href="{{ route('menu.show', $related) }}" 
                               style="background: #fed7aa; color: #ea580c; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn:hover {
    transform: translateY(-1px);
}

#quantity::-webkit-outer-spin-button,
#quantity::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

#quantity[type=number] {
    -moz-appearance: textfield;
}

@media (max-width: 768px) {
    .container > div {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
const productPrice = {{ $product->price }};

function updateTotalPrice() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const total = (productPrice * quantity).toFixed(2);
    document.getElementById('totalPrice').textContent = total.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue < 10) {
        quantityInput.value = currentValue + 1;
        updateTotalPrice();
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
        updateTotalPrice();
    }
}

function addToCartWithQuantity() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const productId = {{ $product->id }};
    const productName = "{{ htmlspecialchars($product->name, ENT_QUOTES) }}";
    const productImage = "{{ $product->image ? htmlspecialchars($product->image, ENT_QUOTES) : '' }}";
    
    // Add multiple quantities to cart
    for (let i = 0; i < quantity; i++) {
        addToCart(productId, productName, productPrice, productImage);
    }
    
    // Show success message with quantity
    alert(`${quantity} × ${productName} added to cart!`);
}

document.addEventListener('DOMContentLoaded', function() {
    // Update total price when quantity changes
    document.getElementById('quantity').addEventListener('input', updateTotalPrice);
    
    // Handle related products add to cart
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;
            const productImage = this.dataset.productImage;
            
            addToCart(productId, productName, productPrice, productImage);
        });
    });
});
</script>
@endpush