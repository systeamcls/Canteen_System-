@extends('layouts.canteen')

@section('title', 'Home - Kajacms')

@section('content')
<!-- Hero Section -->
<section style="padding: 80px 0; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:%23f3f4f6;stop-opacity:0.8"/><stop offset="100%" style="stop-color:%23e5e7eb;stop-opacity:0.9"/></linearGradient></defs><rect width="1200" height="600" fill="url(%23bg)"/></svg>') center/cover; position: relative; overflow: hidden;">
    <!-- Background overlay with food images -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text x="10" y="20" font-size="12">🍕</text><text x="70" y="30" font-size="10">🍜</text><text x="30" y="50" font-size="8">🥗</text><text x="80" y="70" font-size="11">🍔</text><text x="20" y="80" font-size="9">🍰</text></svg>'); background-repeat: repeat;"></div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 40px; align-items: center;">
            <!-- Left Content -->
            <div>
                <div style="display: inline-flex; align-items: center; background: var(--primary); color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; margin-bottom: 20px;">
                    ⭐ Premium Dining Experience
                </div>
                
                <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 20px; color: var(--dark);">
                    Discover <span style="color: var(--primary);">Flavors</span><br>
                    from Around the<br>
                    World
                </h1>
                
                <p style="font-size: 1.1rem; color: var(--gray); margin-bottom: 40px; max-width: 500px; line-height: 1.6;">
                    Experience culinary excellence with our diverse collection of authentic stalls, featuring fresh ingredients and traditional recipes from master chefs.
                </p>
                
                <div style="display: flex; gap: 20px; margin-bottom: 40px;">
                    <a href="/menu" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 30px;">
                        Explore Menu →
                    </a>
                    <a href="/stalls" class="btn" style="background: transparent; color: var(--dark); border: 2px solid var(--dark); font-size: 1.1rem; padding: 15px 30px;">
                        View Stalls
                    </a>
                </div>
                
                <!-- Statistics -->
                <div style="display: flex; gap: 40px; margin-top: 40px;">
                    <div style="text-align: center;">
                        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 5px;">
                            <span style="font-size: 1.5rem;">🕐</span>
                        </div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary); margin-bottom: 5px;">15+</div>
                        <div style="font-size: 0.9rem; color: var(--gray);">Food Stalls</div>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 5px;">
                            <span style="font-size: 1.5rem;">📍</span>
                        </div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary); margin-bottom: 5px;">500+</div>
                        <div style="font-size: 0.9rem; color: var(--gray);">Daily Meals</div>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 5px;">
                            <span style="font-size: 1.5rem;">⭐</span>
                        </div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary); margin-bottom: 5px;">4.8</div>
                        <div style="font-size: 0.9rem; color: var(--gray);">Rating</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Sidebar - Today's Special -->
            <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="font-size: 1.4rem; color: var(--dark); margin: 0;">Today's Special</h3>
                    <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Available Now</span>
                </div>
                
                @forelse($topFoods->take(3) as $food)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <h4 style="color: var(--dark); margin: 0 0 5px 0; font-size: 1rem;">{{ $food->name }}</h4>
                        <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">{{ $food->stall->name }}</p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.1rem; font-weight: 700; color: var(--primary);">₱{{ number_format($food->price, 2) }}</div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 20px; color: var(--gray);">
                    <p>No specials available today</p>
                </div>
                @endforelse
                
                @if($topFoods->count() > 0)
                <div style="margin-top: 20px;">
                    <a href="/menu" class="btn btn-primary" style="width: 100%; text-align: center;">View All Menu</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Popular Food Items -->
<section style="padding: 80px 0; background: white;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 2.5rem; color: var(--dark); margin-bottom: 15px;">Popular Dishes</h2>
            <p style="color: var(--gray); max-width: 600px; margin: 0 auto; font-size: 1.1rem;">Discover our most loved dishes from various cuisines</p>
        </div>
        
        <div class="grid grid-3">
            @forelse($featuredItems as $item)
            <div class="card" style="overflow: hidden; border-radius: 16px;">
                <div style="position: relative;">
                    <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $item->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                    <button onclick="toggleFavorite({{ $item->id }})" style="position: absolute; top: 15px; right: 15px; background: white; border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        ♡
                    </button>
                </div>
                <div class="card-content" style="padding: 25px;">
                    <h3 style="font-size: 1.2rem; margin: 0 0 10px 0; color: var(--dark);">{{ $item->name }}</h3>
                    <p style="color: var(--gray); margin: 0 0 15px 0; font-size: 0.9rem; line-height: 1.4;">{{ Str::limit($item->description, 60) }}</p>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        @for($i = 1; $i <= 5; $i++)
                            <span style="color: #fbbf24; font-size: 0.9rem;">{{ $i <= 4 ? '★' : '☆' }}</span>
                        @endfor
                        <span style="margin-left: 8px; font-size: 0.9rem; color: var(--gray);">4.{{ rand(6, 9) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--primary);">₱{{ number_format($item->price, 2) }}</div>
                        <button onclick="addToCart({{ $item->id }}, {{ json_encode($item->name) }}, {{ $item->price }}, {{ json_encode($item->image ?: '') }})" class="btn btn-primary" style="padding: 10px 20px;">
                            Add to cart
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p style="color: var(--gray);">No featured items available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function toggleFavorite(itemId) {
    // Toggle favorite functionality
    const button = event.target;
    if (button.textContent === '♡') {
        button.textContent = '♥';
        button.style.color = 'var(--primary)';
    } else {
        button.textContent = '♡';
        button.style.color = '#000';
    }
}
</script>
@endpush