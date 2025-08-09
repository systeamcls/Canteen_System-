@extends('layouts.canteen')

@section('title', 'Home - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 80px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Welcome to LTO Canteen Central</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.9;">
            @if(session('user_type') === 'guest')
                Browse our amazing food selection from multiple stalls and place your order!
            @else
                Welcome back! Enjoy exclusive employee discounts and benefits.
            @endif
        </p>
        
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-bottom: 60px;">
            <a href="{{ route('menu.index') }}" style="background: white; color: var(--primary); padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: 600; transition: transform 0.3s; display: inline-block;">
                🍽️ Browse Menu
            </a>
            <a href="{{ route('stalls.index') }}" style="background: rgba(255,255,255,0.2); color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: 600; transition: transform 0.3s; display: inline-block; border: 2px solid white;">
                🏪 View Stalls
            </a>
        </div>

        <!-- User Type Badge -->
        <div style="display: inline-flex; align-items: center; background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 25px; font-size: 0.9rem;">
            @if(session('user_type') === 'guest')
                👤 Browsing as Guest
            @else
                👨‍💼 Logged in as Employee
            @endif
        </div>
    </div>
</section>

<!-- Featured Stalls Section -->
<section style="padding: 80px 0; background: white;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 2.5rem; color: var(--primary); margin-bottom: 20px;">Featured Food Stalls</h2>
            <p style="font-size: 1.1rem; color: var(--gray); max-width: 600px; margin: 0 auto;">Discover unique flavors from our carefully selected food vendors</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            @foreach(App\Models\Stall::where('is_active', true)->take(3)->get() as $stall)
            <div style="background: white; border-radius: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.3s; cursor: pointer;" 
                 onmouseover="this.style.transform='translateY(-10px)'" 
                 onmouseout="this.style.transform='translateY(0)'">
                <div style="height: 200px; background: linear-gradient(135deg, var(--primary-lighter) 0%, var(--primary-light) 100%); display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 4rem;">🏪</span>
                </div>
                <div style="padding: 25px;">
                    <h3 style="font-size: 1.3rem; color: var(--primary); margin-bottom: 10px; font-weight: 600;">{{ $stall->name }}</h3>
                    <p style="color: var(--gray); line-height: 1.6; margin-bottom: 20px;">{{ $stall->description }}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--gray); font-size: 0.9rem;">📍 {{ $stall->location }}</span>
                        <a href="{{ route('stalls.show', $stall) }}" style="background: var(--primary); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 500;">View Menu</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Trending Items Section -->
<section style="padding: 80px 0; background: var(--light);">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 2.5rem; color: var(--primary); margin-bottom: 20px;">Trending Food Items</h2>
            <p style="font-size: 1.1rem; color: var(--gray); max-width: 600px; margin: 0 auto;">Popular choices from our customers</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
            @foreach(App\Models\Product::where('is_available', true)->with('stall')->take(6)->get() as $product)
            <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden; transition: transform 0.3s;">
                <div style="height: 150px; background: linear-gradient(45deg, var(--primary-lighter), var(--secondary)); display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 3rem;">🍽️</span>
                </div>
                <div style="padding: 20px;">
                    <h4 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 8px; font-weight: 600;">{{ $product->name }}</h4>
                    <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 12px;">{{ Str::limit($product->description, 80) }}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-weight: 600; color: var(--primary); font-size: 1.1rem;">₱{{ number_format($product->price, 2) }}</span>
                            <div style="font-size: 0.8rem; color: var(--gray);">{{ $product->stall->name }}</div>
                        </div>
                        
                        <button class="add-to-cart-btn" 
                                data-product-id="{{ $product->id }}" 
                                data-product-name="{{ $product->name }}" 
                                data-product-price="{{ $product->price }}"
                                style="background: var(--success); color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer;">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('menu.index') }}" style="background: var(--primary); color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block;">
                View All Menu Items
            </a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section style="padding: 80px 0; background: white;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 2.5rem; color: var(--primary); margin-bottom: 20px;">How It Works</h2>
            <p style="font-size: 1.1rem; color: var(--gray);">Simple steps to get your favorite food</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--primary-lighter); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem;">
                    🔍
                </div>
                <h3 style="color: var(--primary); margin-bottom: 15px; font-size: 1.3rem;">1. Browse</h3>
                <p style="color: var(--gray); line-height: 1.6;">Explore our diverse menu from multiple food stalls</p>
            </div>

            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--primary-lighter); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem;">
                    🛒
                </div>
                <h3 style="color: var(--primary); margin-bottom: 15px; font-size: 1.3rem;">2. Order</h3>
                <p style="color: var(--gray); line-height: 1.6;">Add your favorite items to cart and checkout</p>
            </div>

            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--primary-lighter); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem;">
                    💳
                </div>
                <h3 style="color: var(--primary); margin-bottom: 15px; font-size: 1.3rem;">3. Pay</h3>
                <p style="color: var(--gray); line-height: 1.6;">Secure payment options available</p>
            </div>

            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--primary-lighter); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem;">
                    🍽️
                </div>
                <h3 style="color: var(--primary); margin-bottom: 15px; font-size: 1.3rem;">4. Enjoy</h3>
                <p style="color: var(--gray); line-height: 1.6;">Pick up your fresh, hot meal and enjoy!</p>
            </div>
        </div>
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