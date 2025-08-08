@extends('layouts.canteen')

@section('title', 'Search Results - KAJACMS')

@section('content')
<section style="padding: 40px 0;">
    <div class="container">
        <!-- Search Header -->
        <div style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-size: 2.5rem; color: #ea580c; margin-bottom: 15px;">Search Results</h1>
            @if($query)
                <p style="font-size: 1.2rem; color: #6b7280;">
                    Showing results for "<strong style="color: #374151;">{{ $query }}</strong>"
                </p>
                <p style="color: #6b7280;">{{ $products->total() }} items found</p>
            @endif
        </div>

        <!-- Search Again -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 40px;">
            <form method="GET" action="{{ route('search') }}">
                <div style="display: flex; gap: 15px; align-items: end;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #374151;">Search Again</label>
                        <input type="text" name="q" value="{{ $query }}" 
                               placeholder="Search for dishes, cuisines, stalls..." 
                               class="search-input" style="width: 100%;">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>

        <!-- Results Grid -->
        @if($products->count() > 0)
            <div class="grid grid-4">
                @foreach($products as $product)
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
                            
                            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                {{ $product->name }}
                            </h3>
                            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 15px; line-height: 1.4;">
                                {{ Str::limit($product->description, 80) }}
                            </p>
                            
                            <div style="margin-bottom: 15px;">
                                <span style="font-size: 0.85rem; color: #ea580c; font-weight: 600;">
                                    🏪 {{ $product->stall->name }}
                                </span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <span style="font-size: 1.2rem; font-weight: 700; color: #ea580c;">
                                    ₱{{ number_format($product->price, 2) }}
                                </span>
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
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div style="margin-top: 40px; display: flex; justify-content: center;">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- No Results -->
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                <h3 style="color: #374151; margin-bottom: 10px; font-size: 1.5rem;">No results found</h3>
                <p style="color: #6b7280; margin-bottom: 30px;">
                    We couldn't find any items matching "{{ $query }}". Try searching with different keywords.
                </p>
                
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <a href="{{ route('menu.index') }}" class="btn btn-primary">Browse All Menu</a>
                    <a href="{{ route('stalls.index') }}" class="btn btn-secondary">View Stalls</a>
                </div>
                
                <!-- Search Suggestions -->
                <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #e5e7eb;">
                    <h4 style="color: #374151; margin-bottom: 15px;">Try searching for:</h4>
                    <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                        <a href="{{ route('search', ['q' => 'pizza']) }}" 
                           style="background: #fef3c7; color: #92400e; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                            Pizza
                        </a>
                        <a href="{{ route('search', ['q' => 'burger']) }}" 
                           style="background: #fef3c7; color: #92400e; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                            Burger
                        </a>
                        <a href="{{ route('search', ['q' => 'noodles']) }}" 
                           style="background: #fef3c7; color: #92400e; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                            Noodles
                        </a>
                        <a href="{{ route('search', ['q' => 'dessert']) }}" 
                           style="background: #fef3c7; color: #92400e; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                            Dessert
                        </a>
                        <a href="{{ route('search', ['q' => 'rice']) }}" 
                           style="background: #fef3c7; color: #92400e; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-weight: 500; transition: all 0.3s;">
                            Rice
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
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