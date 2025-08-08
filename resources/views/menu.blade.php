@extends('layouts.canteen')

@section('title', 'Menu - KAJACMS')

@section('content')
<!-- Hero Section -->
<section style="padding: 40px 0; background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 20px; color: #ea580c;">KAJACMS Menu</h1>
        <p style="font-size: 1.1rem; color: #9a3412; margin-bottom: 30px;">Discover delicious food from our multi-vendor canteen</p>
        
        <!-- Search and Filters -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <form method="GET" action="{{ route('menu.index') }}">
                <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 15px; align-items: end;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Search Menu</label>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Search for dishes, cuisines, or stalls..." 
                               class="search-input" style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Category</label>
                        <select name="category" class="filter-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $key => $value)
                                <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Stall</label>
                        <select name="stall" class="filter-select">
                            <option value="">All Stalls</option>
                            @foreach($stalls as $stall)
                                <option value="{{ $stall->id }}" {{ $stallId == $stall->id ? 'selected' : '' }}>{{ $stall->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section style="padding: 40px 0; background: #fff;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 30px; color: #ea580c; font-size: 2rem;">Food Categories</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <a href="{{ route('menu.index', ['category' => 'pizza']) }}" 
               style="text-decoration: none; background: #fef3c7; padding: 20px; border-radius: 12px; text-align: center; transition: all 0.3s; {{ $category == 'pizza' ? 'background: #f59e0b; color: white;' : 'color: #92400e;' }}">
                <div style="font-size: 2rem; margin-bottom: 10px;">🍕</div>
                <div style="font-weight: 600;">Pizza</div>
            </a>
            <a href="{{ route('menu.index', ['category' => 'fast-food']) }}" 
               style="text-decoration: none; background: #fef3c7; padding: 20px; border-radius: 12px; text-align: center; transition: all 0.3s; {{ $category == 'fast-food' ? 'background: #f59e0b; color: white;' : 'color: #92400e;' }}">
                <div style="font-size: 2rem; margin-bottom: 10px;">🍔</div>
                <div style="font-weight: 600;">Fast Food</div>
            </a>
            <a href="{{ route('menu.index', ['category' => 'noodle']) }}" 
               style="text-decoration: none; background: #fef3c7; padding: 20px; border-radius: 12px; text-align: center; transition: all 0.3s; {{ $category == 'noodle' ? 'background: #f59e0b; color: white;' : 'color: #92400e;' }}">
                <div style="font-size: 2rem; margin-bottom: 10px;">🍜</div>
                <div style="font-weight: 600;">Noodle</div>
            </a>
            <a href="{{ route('menu.index', ['category' => 'dessert']) }}" 
               style="text-decoration: none; background: #fef3c7; padding: 20px; border-radius: 12px; text-align: center; transition: all 0.3s; {{ $category == 'dessert' ? 'background: #f59e0b; color: white;' : 'color: #92400e;' }}">
                <div style="font-size: 2rem; margin-bottom: 10px;">🍰</div>
                <div style="font-weight: 600;">Dessert</div>
            </a>
            <a href="{{ route('menu.index', ['category' => 'sea-food']) }}" 
               style="text-decoration: none; background: #fef3c7; padding: 20px; border-radius: 12px; text-align: center; transition: all 0.3s; {{ $category == 'sea-food' ? 'background: #f59e0b; color: white;' : 'color: #92400e;' }}">
                <div style="font-size: 2rem; margin-bottom: 10px;">🦐</div>
                <div style="font-weight: 600;">Sea Food</div>
            </a>
            <a href="{{ route('menu.index', ['category' => 'sushi']) }}" 
               style="text-decoration: none; background: #fef3c7; padding: 20px; border-radius: 12px; text-align: center; transition: all 0.3s; {{ $category == 'sushi' ? 'background: #f59e0b; color: white;' : 'color: #92400e;' }}">
                <div style="font-size: 2rem; margin-bottom: 10px;">🍣</div>
                <div style="font-weight: 600;">Sushi</div>
            </a>
            <a href="{{ route('menu.index', ['category' => 'ramen']) }}" 
               style="text-decoration: none; background: #fef3c7; padding: 20px; border-radius: 12px; text-align: center; transition: all 0.3s; {{ $category == 'ramen' ? 'background: #f59e0b; color: white;' : 'color: #92400e;' }}">
                <div style="font-size: 2rem; margin-bottom: 10px;">🍲</div>
                <div style="font-weight: 600;">Ramen</div>
            </a>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section style="padding: 40px 0;">
    <div class="container">
        @if($search || $category || $stallId)
            <div style="margin-bottom: 30px;">
                <h3 style="color: #374151; margin-bottom: 10px;">
                    Showing results 
                    @if($search) for "{{ $search }}" @endif
                    @if($category) in {{ $categories[$category] ?? $category }} @endif
                    @if($stallId) from {{ $stalls->find($stallId)->name ?? 'Selected Stall' }} @endif
                </h3>
                <p style="color: #6b7280;">{{ $products->total() }} items found</p>
            </div>
        @endif

        <div class="grid grid-4">
            @forelse($products as $product)
                <div class="card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s;">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                         alt="{{ $product->name }}" 
                         style="width: 100%; height: 200px; object-fit: cover;">
                    
                    <div style="padding: 20px;">
                        <div style="margin-bottom: 10px;">
                            <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                {{ $product->category }}
                            </span>
                        </div>
                        
                        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: #374151;">{{ $product->name }}</h3>
                        <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 15px; line-height: 1.4;">{{ Str::limit($product->description, 80) }}</p>
                        
                        <div style="margin-bottom: 15px;">
                            <span style="font-size: 0.85rem; color: #ea580c; font-weight: 600;">{{ $product->stall->name }}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <span style="font-size: 1.2rem; font-weight: 700; color: #ea580c;">₱{{ number_format($product->price, 2) }}</span>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <span style="color: #fbbf24;">⭐⭐⭐⭐⭐</span>
                                <span style="font-size: 0.8rem; color: #6b7280;">(4.8)</span>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary add-to-cart-btn" 
                                    style="flex: 1; background: #ea580c; border: none; padding: 10px; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ htmlspecialchars($product->name, ENT_QUOTES) }}"
                                    data-product-price="{{ $product->price }}"
                                    data-product-image="{{ $product->image ? htmlspecialchars($product->image, ENT_QUOTES) : '' }}">
                                Add to Cart
                            </button>
                            <a href="{{ route('menu.show', $product) }}" 
                               style="background: #fed7aa; color: #ea580c; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <div style="font-size: 3rem; margin-bottom: 20px;">🍽️</div>
                    <h3 style="color: #374151; margin-bottom: 10px;">No items found</h3>
                    <p style="color: #6b7280;">Try adjusting your search criteria or browse all categories</p>
                    <a href="{{ route('menu.index') }}" class="btn btn-primary" style="margin-top: 20px;">View All Items</a>
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

<!-- Today's Special -->
@if(!$search && !$category && !$stallId)
<section style="padding: 60px 0; background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2.5rem; color: #ea580c; margin-bottom: 15px;">Today's Special</h2>
            <p style="color: #92400e;">Don't miss these featured items from our vendors</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            @php
                $specialItems = $products->take(3);
            @endphp
            @foreach($specialItems as $special)
                <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: all 0.3s;">
                    <img src="{{ $special->image ? asset('storage/' . $special->image) : 'https://via.placeholder.com/400x250?text=Today\'s+Special' }}" 
                         alt="{{ $special->name }}" 
                         style="width: 100%; height: 250px; object-fit: cover;">
                    
                    <div style="padding: 25px;">
                        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
                            <span style="background: #dc2626; color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                Special Offer
                            </span>
                        </div>
                        
                        <h3 style="font-size: 1.3rem; font-weight: 700; color: #374151; margin-bottom: 10px;">{{ $special->name }}</h3>
                        <p style="color: #6b7280; margin-bottom: 15px;">{{ $special->description }}</p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <div>
                                <span style="font-size: 1.4rem; font-weight: 700; color: #ea580c;">₱{{ number_format($special->price, 2) }}</span>
                                <span style="font-size: 0.9rem; color: #6b7280; margin-left: 10px;">{{ $special->stall->name }}</span>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary add-to-cart-btn" 
                                style="width: 100%; background: #ea580c; border: none; padding: 12px; border-radius: 8px; color: white; font-weight: 600; font-size: 1rem;"
                                data-product-id="{{ $special->id }}"
                                data-product-name="{{ htmlspecialchars($special->name, ENT_QUOTES) }}"
                                data-product-price="{{ $special->price }}"
                                data-product-image="{{ $special->image ? htmlspecialchars($special->image, ENT_QUOTES) : '' }}">
                            Add to Cart - Special Price!
                        </button>
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
:root {
    --orange-primary: #ea580c;
    --orange-light: #fed7aa;
    --orange-lighter: #fef3c7;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.add-to-cart-btn:hover {
    background: #dc2626 !important;
    transform: translateY(-1px);
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