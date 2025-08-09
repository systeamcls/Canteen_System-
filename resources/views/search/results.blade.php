@extends('layouts.canteen')

@section('title', 'Search Results - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 60px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Search Results</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.9;">
            Results for "{{ $query }}"
        </p>

        <!-- Search Bar -->
        <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 15px; max-width: 600px; margin: 0 auto;">
            <form action="{{ route('search') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="q" value="{{ $query }}" 
                       placeholder="Search for food or stalls..." 
                       style="flex: 1; padding: 12px 15px; border: none; border-radius: 8px; font-size: 1rem;">
                
                <button type="submit" style="background: var(--secondary); color: white; padding: 12px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Search
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Results Section -->
<section style="padding: 60px 0; background: var(--light);">
    <div class="container">
        <!-- Products Results -->
        @if($products->count() > 0)
        <div style="margin-bottom: 60px;">
            <h2 style="color: var(--primary); font-size: 1.8rem; margin-bottom: 30px;">Food Items ({{ $products->total() }} found)</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
                @foreach($products as $product)
                <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden; transition: transform 0.3s;">
                    <div style="height: 150px; background: linear-gradient(45deg, var(--primary-lighter), var(--secondary)); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 3rem;">🍽️</span>
                    </div>
                    <div style="padding: 20px;">
                        <div style="color: var(--gray); font-size: 0.9rem; margin-bottom: 8px;">🏪 {{ $product->stall->name }}</div>
                        <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 8px; font-weight: 600;">{{ $product->name }}</h3>
                        <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 15px;">{{ Str::limit($product->description, 80) }}</p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 600; color: var(--primary); font-size: 1.2rem;">₱{{ number_format($product->price, 2) }}</span>
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

            <div style="margin-top: 30px; display: flex; justify-content: center;">
                {{ $products->appends(['q' => $query])->links() }}
            </div>
        </div>
        @endif

        <!-- Stalls Results -->
        @if($stalls->count() > 0)
        <div>
            <h2 style="color: var(--primary); font-size: 1.8rem; margin-bottom: 30px;">Food Stalls ({{ $stalls->count() }} found)</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
                @foreach($stalls as $stall)
                <div style="background: white; border-radius: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); overflow: hidden;">
                    <div style="height: 150px; background: linear-gradient(135deg, var(--primary-lighter) 0%, var(--primary-light) 100%); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 4rem;">🏪</span>
                    </div>
                    <div style="padding: 25px;">
                        <h3 style="font-size: 1.3rem; color: var(--primary); margin-bottom: 10px; font-weight: 600;">{{ $stall->name }}</h3>
                        <p style="color: var(--gray); line-height: 1.6; margin-bottom: 15px;">{{ $stall->description }}</p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--gray); font-size: 0.9rem;">📍 {{ $stall->location }}</span>
                            <a href="{{ route('stalls.show', $stall) }}" style="background: var(--primary); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 500;">View Menu</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- No Results -->
        @if($products->count() === 0 && $stalls->count() === 0)
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;">🔍</div>
            <h3 style="color: var(--primary); margin-bottom: 15px; font-size: 1.5rem;">No results found</h3>
            <p style="color: var(--gray); margin-bottom: 30px;">No items match your search for "{{ $query }}". Try different keywords.</p>
            <a href="{{ route('menu.index') }}" style="background: var(--primary); color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600;">Browse All Items</a>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
// Add cart functionality
document.addEventListener('DOMContentLoaded', function() {
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
    
    setTimeout(() => {
        document.body.removeChild(messageDiv);
    }, 3000);
}

updateCartCount();
</script>
@endpush