@extends('layouts.canteen')

@section('title', 'Home - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 60px 0; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px; color: var(--primary);">Welcome to LTO Canteen Central</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; color: var(--gray);">Discover delicious food from our trusted stalls. Fresh meals, quick service, and delicious options for everyone - visitors and LTO employees alike.</p>
        
        <!-- Search Bar -->
        <div class="search-bar" style="margin-bottom: 50px;">
            <form action="/search" method="GET">
                <input type="text" name="q" placeholder="Search for food items..." class="search-input" value="{{ request('q') }}">
                <button type="submit" class="search-btn">🔍</button>
            </form>
        </div>
        
        <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 50px; flex-wrap: wrap;">
            <a href="/menu" class="btn btn-primary" style="font-size: 1.1rem;">🛒 Browse Menu</a>
            <a href="/stalls" class="btn btn-secondary" style="font-size: 1.1rem;">🏪 View Stalls</a>
        </div>
    </div>
</section>

<!-- Top Foods Today -->
<section style="padding: 60px 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Top Foods Today</h2>
            <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Most popular items ordered by our customers</p>
        </div>
        
        <div class="grid grid-4">
            @forelse($topFoods as $food)
            <div class="card">
                <img src="{{ $food->image ? asset('storage/' . $food->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $food->name }}" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">{{ $food->name }}</h3>
                    <p class="card-text">{{ Str::limit($food->description, 60) }}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span class="price">₱{{ number_format($food->price, 2) }}</span>
                        <div class="rating">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                            <span style="font-size: 0.9rem; color: var(--gray);">(4.8)</span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <small style="color: var(--gray);">{{ $food->stall->name }}</small>
                        <small style="color: var(--primary); font-weight: 600;">{{ rand(15, 45) }} orders today</small>
                    </div>
                    <button onclick="addToCart({{ $food->id }}, '{{ $food->name }}', {{ $food->price }}, '{{ $food->image }}')" class="btn btn-primary" style="width: 100%;">Add to Cart</button>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p style="color: var(--gray);">No featured foods available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Popular Stalls -->
<section style="padding: 60px 0; background-color: #eff6ff;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Popular Stalls</h2>
            <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Our most loved food stalls</p>
        </div>
        
        <div class="grid grid-3">
            @forelse($popularStalls as $stall)
            <div class="card">
                <div class="card-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <h3 class="card-title">{{ $stall->name }}</h3>
                        <span class="status {{ $stall->is_active ? 'status-open' : 'status-closed' }}">
                            {{ $stall->is_active ? 'Open' : 'Closed' }}
                        </span>
                    </div>
                    
                    <p class="card-text">{{ Str::limit($stall->description, 80) }}</p>
                    
                    <div class="rating" style="margin: 15px 0;">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span style="font-size: 0.9rem; color: var(--gray);">(4.{{ rand(6, 9) }})</span>
                    </div>
                    
                    <div style="margin: 15px 0;">
                        <small style="color: var(--gray);">📍 {{ $stall->location }}</small>
                    </div>
                    
                    <div style="margin: 15px 0;">
                        <small style="color: var(--primary); font-weight: 600;">{{ $stall->products_count }} items available</small>
                    </div>
                    
                    <a href="/stalls/{{ $stall->id }}" class="btn btn-primary" style="width: 100%;">View Menu</a>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p style="color: var(--gray);">No stalls available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Items -->
<section style="padding: 60px 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Featured Items</h2>
            <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Hand-picked specialties from our vendors</p>
        </div>
        
        <div class="grid grid-3">
            @forelse($featuredItems as $item)
            <div class="card">
                <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $item->name }}" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">{{ $item->name }}</h3>
                    <p class="card-text">{{ Str::limit($item->description, 60) }}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span class="price">₱{{ number_format($item->price, 2) }}</span>
                        <small style="color: var(--gray);">{{ $item->stall->name }}</small>
                    </div>
                    <button onclick="addToCart({{ $item->id }}, '{{ $item->name }}', {{ $item->price }}, '{{ $item->image }}')" class="btn btn-primary" style="width: 100%;">Add to Cart</button>
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

<!-- Call to Action -->
<section style="padding: 80px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Ready to Satisfy Your Cravings?</h2>
        <p style="max-width: 700px; margin: 0 auto 40px; font-size: 1.1rem; opacity: 0.9;">Join LTO visitors and employees who've discovered the easiest way to order delicious food</p>
        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            <a href="/menu" class="btn btn-secondary">Order Now</a>
            <a href="/stalls" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">View All Stalls</a>
        </div>
        <div style="margin-top: 30px; color: rgba(255,255,255,0.8); font-size: 0.9rem;">
            ✓ No delivery fees within LTO ✓ Affordable pricing for all ✓ Quick 10-15 min pickup
        </div>
    </div>
</section>
@endsection