@extends('layouts.canteen')

@section('title', 'Menu - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 60px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Our Complete Menu</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.9;">Discover amazing food from multiple vendors in one place. Fresh meals, quick service, and delicious options for everyone.</p>

        <!-- Search and Filter Bar -->
        <div style="background: rgba(255,255,255,0.15); padding: 25px; border-radius: 15px; max-width: 800px; margin: 0 auto;">
            <form action="{{ route('menu.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center; justify-content: center;">
                <!-- Search Input -->
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search for food items..." 
                           style="width: 100%; padding: 12px 15px; border: none; border-radius: 8px; font-size: 1rem;">
                </div>

                <!-- Category Filter -->
                <select name="category" style="padding: 12px 15px; border: none; border-radius: 8px; background: white; min-width: 150px;">
                    <option value="">All Categories</option>
                    <option value="fresh-meals" {{ request('category') === 'fresh-meals' ? 'selected' : '' }}>Fresh Meals</option>
                    <option value="sandwiches" {{ request('category') === 'sandwiches' ? 'selected' : '' }}>Sandwiches</option>
                    <option value="beverages" {{ request('category') === 'beverages' ? 'selected' : '' }}>Beverages</option>
                    <option value="snacks" {{ request('category') === 'snacks' ? 'selected' : '' }}>Snacks</option>
                </select>

                <!-- Stall Filter -->
                <select name="stall" style="padding: 12px 15px; border: none; border-radius: 8px; background: white; min-width: 150px;">
                    <option value="">All Stalls</option>
                    @foreach(App\Models\Stall::where('is_active', true)->get() as $stall)
                        <option value="{{ $stall->id }}" {{ request('stall') == $stall->id ? 'selected' : '' }}>
                            {{ $stall->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Search Button -->
                <button type="submit" style="background: var(--secondary); color: white; padding: 12px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Search
                </button>

                <!-- Reset Button -->
                @if(request()->hasAny(['search', 'category', 'stall']))
                <a href="{{ route('menu.index') }}" style="background: rgba(255,255,255,0.3); color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>
</section>

<!-- Menu Items Section -->
<section style="padding: 60px 0; background: var(--light);">
    <div class="container">
        <!-- Results Info -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap;">
            <div>
                <h2 style="color: var(--primary); font-size: 1.5rem; margin-bottom: 5px;">
                    @if(request()->hasAny(['search', 'category', 'stall']))
                        Search Results
                    @else
                        All Menu Items
                    @endif
                </h2>
                <p style="color: var(--gray);">{{ $products->total() }} items found</p>
            </div>

            <!-- User Type Badge -->
            <div style="background: white; padding: 10px 20px; border-radius: 25px; border: 2px solid var(--primary-lighter);">
                @if(session('user_type') === 'guest')
                    <span style="color: var(--primary);">👤 Guest User</span>
                @else
                    <span style="color: var(--success);">👨‍💼 Employee (Discounts Available)</span>
                @endif
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
                @foreach($products as $product)
                <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden; transition: transform 0.3s;"
                     onmouseover="this.style.transform='translateY(-5px)'" 
                     onmouseout="this.style.transform='translateY(0)'">
                    
                    <!-- Product Image Placeholder -->
                    <div style="height: 200px; background: linear-gradient(45deg, var(--primary-lighter), var(--secondary)); display: flex; align-items: center; justify-content: center; position: relative;">
                        <span style="font-size: 4rem;">🍽️</span>
                        
                        <!-- Category Badge -->
                        <div style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600; color: var(--primary);">
                            {{ ucfirst(str_replace('-', ' ', $product->category)) }}
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div style="padding: 20px;">
                        <!-- Stall Name -->
                        <div style="color: var(--gray); font-size: 0.9rem; margin-bottom: 8px; font-weight: 500;">
                            🏪 {{ $product->stall->name }}
                        </div>

                        <!-- Product Name -->
                        <h3 style="font-size: 1.2rem; color: var(--primary); margin-bottom: 10px; font-weight: 600;">
                            {{ $product->name }}
                        </h3>

                        <!-- Description -->
                        <p style="color: var(--gray); line-height: 1.5; margin-bottom: 15px; font-size: 0.95rem;">
                            {{ $product->description }}
                        </p>

                        <!-- Price and Add to Cart -->
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 1.3rem; font-weight: 700; color: var(--primary);">
                                    ₱{{ number_format($product->price, 2) }}
                                </span>
                                @if(session('user_type') === 'employee')
                                    <div style="font-size: 0.8rem; color: var(--success); font-weight: 600;">
                                        Employee Discount Available
                                    </div>
                                @endif
                            </div>
                            
                            <button class="add-to-cart-btn" 
                                    data-product-id="{{ $product->id }}" 
                                    data-product-name="{{ $product->name }}" 
                                    data-product-price="{{ $product->price }}"
                                    style="background: var(--primary); color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;"
                                    onmouseover="this.style.background='var(--primary-light)'"
                                    onmouseout="this.style.background='var(--primary)'">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 40px; display: flex; justify-content: center;">
                {{ $products->links() }}
            </div>
        @else
            <!-- No Results -->
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;">🔍</div>
                <h3 style="color: var(--primary); margin-bottom: 15px; font-size: 1.5rem;">No items found</h3>
                <p style="color: var(--gray); margin-bottom: 30px;">Try adjusting your search criteria or browse all items.</p>
                <a href="{{ route('menu.index') }}" style="background: var(--primary); color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    View All Items
                </a>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
// Enhanced cart functionality with event delegation
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for all "Add to Cart" buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-cart-btn')) {
            e.preventDefault();
            
            const button = e.target;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const productPrice = button.getAttribute('data-product-price');
            
            addToCart(productId, productName, productPrice, '');
        }
    });
});

function addToCart(productId, name, price, image) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    const existingItem = cart.find(item => item.id == productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: parseFloat(price),
            image: image || '',
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    
    // Show success message
    showSuccessMessage(name + ' added to cart!');
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
    }
}

function showSuccessMessage(message) {
    // Create a temporary success message
    const messageDiv = document.createElement('div');
    messageDiv.innerHTML = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        font-weight: 600;
    `;
    
    document.body.appendChild(messageDiv);
    
    // Remove message after 3 seconds
    setTimeout(() => {
        document.body.removeChild(messageDiv);
    }, 3000);
}

// Initialize cart count on page load
updateCartCount();
</script>
@endpush