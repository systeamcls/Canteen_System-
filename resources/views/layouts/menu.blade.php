@extends('layouts.canteen')

@section('title', 'Menu - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 60px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Our Menu</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.9;">Discover amazing food from multiple vendors in one place. Fresh meals, quick service, and delicious options for everyone.</p>

        <!-- Search Bar -->
        <div class="search-bar">
            <form action="/menu" method="GET">
                <input type="hidden" name="category" value="{{ $category }}">
                <input type="hidden" name="stall" value="{{ $stallId }}">
                <input type="text" name="search" placeholder="Search for food items..." class="search-input" value="{{ $search }}">
                <button type="submit" class="search-btn">🔍</button>
            </form>
        </div>
    </div>
</section>

<!-- Filters -->
<section style="padding: 30px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <form action="/menu" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <input type="hidden" name="search" value="{{ $search }}">

            <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $key => $name)
                    <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <select name="stall" class="filter-select" onchange="this.form.submit()">
                <option value="">All Stalls</option>
                @foreach($stalls as $stall)
                    <option value="{{ $stall->id }}" {{ $stallId == $stall->id ? 'selected' : '' }}>{{ $stall->name }}</option>
                @endforeach
            </select>

            <a href="/menu" class="btn btn-secondary" style="margin-left: auto;">Clear Filters</a>
        </form>
    </div>
</section>

<!-- Food Categories Overview -->
@if(!$search && !$category && !$stallId)
<section style="padding: 60px 0; background-color: #eff6ff;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Food Categories</h2>
            <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Browse our variety of food options</p>
        </div>

        <div class="grid grid-3">
            <a href="/menu?category=fresh-meals" class="card" style="text-decoration: none; color: inherit;">
                <div style="padding: 40px; text-align: center;">
                    <span style="font-size: 4rem; margin-bottom: 20px; display: block;">🍛</span>
                    <h3 class="card-title">Fresh Meals</h3>
                    <p class="card-text">Hot, Fresh, and Nutritious Meals</p>
                </div>
            </a>

            <a href="/menu?category=sandwiches" class="card" style="text-decoration: none; color: inherit;">
                <div style="padding: 40px; text-align: center;">
                    <span style="font-size: 4rem; margin-bottom: 20px; display: block;">🥪</span>
                    <h3 class="card-title">Sandwiches</h3>
                    <p class="card-text">Fresh ingredients, endless combinations</p>
                </div>
            </a>

            <a href="/menu?category=beverages" class="card" style="text-decoration: none; color: inherit;">
                <div style="padding: 40px; text-align: center;">
                    <span style="font-size: 4rem; margin-bottom: 20px; display: block;">🥤</span>
                    <h3 class="card-title">Beverages</h3>
                    <p class="card-text">Different Variation of Drinks</p>
                </div>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Products Grid -->
<section style="padding: 60px 0;">
    <div class="container">
        @if($search || $category || $stallId)
        <div style="margin-bottom: 30px;">
            <h2 style="color: var(--primary); margin-bottom: 10px;">
                @if($search)
                    Search Results for "{{ $search }}"
                @elseif($category)
                    {{ $categories[$category ?? ''] ?? 'Category' }}
                @elseif($stallId)
                    {{ $stalls->find($stallId)->name ?? 'Stall' }} Menu
                @else
                    All Menu Items
                @endif
            </h2>
            <p style="color: var(--gray);">{{ $products->total() }} items found</p>
        </div>
        @endif

        <div class="grid grid-3">
            @forelse($products as $product)
            <div class="card">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $product->name }}" class="card-img">
                <div class="card-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <h3 class="card-title">{{ $product->name }}</h3>
                        <span style="background: var(--primary-lighter); color: var(--primary); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                            {{ $categories[$product->category] ?? 'Food' }}
                        </span>
                    </div>

                    <p class="card-text">{{ Str::limit($product->description, 80) }}</p>

                    <div style="margin: 15px 0;">
                        <small style="color: var(--gray);">🏪 {{ $product->stall->name }}</small>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span class="price">₱{{ number_format($product->price, 2) }}</span>
                        <div class="rating">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                            <span style="font-size: 0.9rem; color: var(--gray);">(4.{{ rand(6, 9) }})</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button 
                            class="btn btn-primary add-to-cart-btn" 
                            style="flex: 1;"
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ htmlspecialchars($product->name, ENT_QUOTES) }}"
                            data-product-price="{{ $product->price }}"
                            data-product-image="{{ $product->image ? htmlspecialchars($product->image, ENT_QUOTES) : '' }}">
                            Add to Cart
                        </button>
                        <a href="/menu/{{ $product->id }}" class="btn btn-secondary">View</a>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <h3 style="color: var(--gray); margin-bottom: 10px;">No items found</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">Try adjusting your search or filters</p>
                <a href="/menu" class="btn btn-primary">View All Items</a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div style="margin-top: 40px; display: flex; justify-content: center;">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</section>
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