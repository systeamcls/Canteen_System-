@extends('layouts.canteen')

@section('title', 'Search Results - LTO Canteen Central')

@section('content')
<!-- Search Results Header -->
<section style="padding: 60px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Search Results</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.9;">
            @if($query ?? false)
                Showing results for "{{ $query }}"
            @else
                Browse all available food items
            @endif
        </p>

        <!-- Search Bar -->
        <div class="search-bar">
            <form action="/search" method="GET">
                <input type="text" name="q" placeholder="Search for food items..." class="search-input" value="{{ $query ?? '' }}">
                <button type="submit" class="search-btn">🔍</button>
            </form>
        </div>
    </div>
</section>

<!-- Results Count -->
<section style="padding: 30px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2 style="color: var(--primary); margin: 0;">{{ $products->total() }} items found</h2>
                @if($query ?? false)
                <p style="color: var(--gray); margin: 0;">for "{{ $query }}"</p>
                @endif
            </div>
            <div style="display: flex; gap: 15px;">
                <a href="/menu" class="btn btn-secondary">Browse All Menu</a>
                <a href="/stalls" class="btn btn-primary">View Stalls</a>
            </div>
        </div>
    </div>
</section>

<!-- Search Results -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="grid grid-3">
            @forelse($products as $product)
            <div class="card">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $product->name }}" class="card-img">
                <div class="card-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <h3 class="card-title">{{ $product->name }}</h3>
                        <span style="background: var(--primary-lighter); color: var(--primary); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                            {{ ucfirst(str_replace('-', ' ', $product->category ?? 'Food')) }}
                        </span>
                    </div>

                    <p class="card-text">{{ Str::limit($product->description, 80) }}</p>

                    <div style="margin: 15px 0;">
                        <small style="color: var(--gray); display: flex; align-items: center; gap: 5px;">
                            🏪 {{ $product->stall->name }}
                        </small>
                        <small style="color: var(--gray); display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                            📍 {{ $product->stall->location }}
                        </small>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span class="price">₱{{ number_format($product->price, 2) }}</span>
                        <div class="rating">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                            <span style="font-size: 0.9rem; color: var(--gray);">(4.{{ rand(6, 9) }})</span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <small style="color: var(--primary); font-weight: 600;">{{ rand(5, 25) }} orders today</small>
                        <span class="status {{ $product->stall->is_active ? 'status-open' : 'status-closed' }}" style="font-size: 0.75rem; padding: 2px 8px;">
                            {{ $product->stall->is_active ? 'Available' : 'Closed' }}
                        </span>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        @if($product->stall->is_active)
                        <button 
                            class="btn btn-primary add-to-cart-btn" 
                            style="flex: 1;"
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ htmlspecialchars($product->name, ENT_QUOTES) }}"
                            data-product-price="{{ $product->price }}"
                            data-product-image="{{ $product->image ? htmlspecialchars($product->image, ENT_QUOTES) : '' }}">
                            Add to Cart
                        </button>
                        @else
                        <button class="btn" style="flex: 1; background: #e5e7eb; color: var(--gray); cursor: not-allowed;" disabled>Unavailable</button>
                        @endif
                        <a href="/stalls/{{ $product->stall->id }}" class="btn btn-secondary">View Stall</a>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                <h3 style="color: var(--gray); margin-bottom: 10px;">No items found</h3>
                <p style="color: var(--gray); margin-bottom: 30px;">
                    @if($query ?? false)
                        Sorry, we couldn't find any items matching "{{ $query }}". Try searching for something else or browse our full menu.
                    @else
                        No items available at the moment.
                    @endif
                </p>
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <a href="/menu" class="btn btn-primary">Browse All Menu</a>
                    <a href="/stalls" class="btn btn-secondary">View All Stalls</a>
                </div>

                @if($query ?? false)
                <div style="margin-top: 30px;">
                    <h4 style="color: var(--dark); margin-bottom: 15px;">Search suggestions:</h4>
                    <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                        <a href="/search?q=chicken" class="btn" style="background: #f8fafc; color: var(--primary); border: 1px solid var(--primary-lighter);">Chicken</a>
                        <a href="/search?q=sandwich" class="btn" style="background: #f8fafc; color: var(--primary); border: 1px solid var(--primary-lighter);">Sandwich</a>
                        <a href="/search?q=coffee" class="btn" style="background: #f8fafc; color: var(--primary); border: 1px solid var(--primary-lighter);">Coffee</a>
                        <a href="/search?q=rice" class="btn" style="background: #f8fafc; color: var(--primary); border: 1px solid var(--primary-lighter);">Rice</a>
                    </div>
                </div>
                @endif
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div style="margin-top: 40px; display: flex; justify-content: center;">
            {{ $products->appends(['q' => $query ?? ''])->links() }}
        </div>
        @endif
    </div>
</section>

<!-- Popular Categories -->
@if($products->count() > 0)
<section style="padding: 60px 0; background-color: #eff6ff;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Browse by Category</h2>
            <p style="color: var(--gray); max-width: 600px; margin: 0 auto;">Find exactly what you're looking for</p>
        </div>

        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <a href="/menu?category=fresh-meals" class="btn" style="background: white; color: var(--primary); border: 2px solid var(--primary-lighter);">🍛 Fresh Meals</a>
            <a href="/menu?category=sandwiches" class="btn" style="background: white; color: var(--primary); border: 2px solid var(--primary-lighter);">🥪 Sandwiches</a>
            <a href="/menu?category=beverages" class="btn" style="background: white; color: var(--primary); border: 2px solid var(--primary-lighter);">🥤 Beverages</a>
            <a href="/menu?category=snacks" class="btn" style="background: white; color: var(--primary); border: 2px solid var(--primary-lighter);">🍪 Snacks</a>
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
    .pagination {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .pagination a, .pagination span {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        color: var(--primary);
        border: 1px solid var(--primary-lighter);
        transition: all 0.2s;
    }
    
    .pagination a:hover {
        background: var(--primary);
        color: white;
    }
    
    .pagination .current {
        background: var(--primary);
        color: white;
    }
    
    .pagination .disabled {
        color: var(--gray);
        border-color: #e5e7eb;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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