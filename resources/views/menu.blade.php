@extends('layouts.canteen')

@section('title', 'Menu - CuisiCourt')

@section('content')
<!-- Header Section -->
<section style="padding: 40px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: inline-flex; align-items: center; background: var(--primary); color: white; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; margin-bottom: 20px;">
                ⭐ Complete Menu
            </div>
            <h1 style="font-size: 2.8rem; color: var(--dark); margin-bottom: 15px;">Find The Best Food</h1>
            <p style="color: var(--gray); max-width: 600px; margin: 0 auto; font-size: 1.1rem;">Discover delicious dishes from all our partner stalls in one place.</p>
        </div>
        
        <!-- Search Bar -->
        <div style="max-width: 600px; margin: 0 auto; position: relative;">
            <form action="/menu" method="GET">
                <input type="hidden" name="category" value="{{ $category }}">
                <input type="hidden" name="stall" value="{{ $stallId }}">
                <input type="text" name="search" placeholder="Search dishes, cuisines, or stalls..." 
                       style="width: 100%; padding: 15px 50px 15px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem;" 
                       value="{{ $search }}">
                <button type="submit" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: var(--primary); color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer;">
                    🔍
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Category Filters -->
<section style="padding: 30px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <form action="/menu" method="GET" style="display: contents;">
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="stall" value="{{ $stallId }}">
                
                <button type="submit" name="category" value="" class="filter-btn {{ !$category ? 'active' : '' }}" style="background: {{ !$category ? 'var(--primary)' : 'white' }}; color: {{ !$category ? 'white' : 'var(--dark)' }}; border: 2px solid {{ !$category ? 'var(--primary)' : '#e5e7eb' }}; padding: 10px 20px; border-radius: 25px; cursor: pointer; font-weight: 500; transition: all 0.3s;">
                    All
                </button>
                
                @foreach(['pizza' => 'Pizza', 'fast-food' => 'Fast Food', 'noodle' => 'Noodle', 'dessert' => 'Dessert', 'sea-food' => 'Sea Food', 'sushi' => 'Sushi', 'ramen' => 'Ramen'] as $key => $name)
                <button type="submit" name="category" value="{{ $key }}" class="filter-btn {{ $category === $key ? 'active' : '' }}" style="background: {{ $category === $key ? 'var(--primary)' : 'white' }}; color: {{ $category === $key ? 'white' : 'var(--dark)' }}; border: 2px solid {{ $category === $key ? 'var(--primary)' : '#e5e7eb' }}; padding: 10px 20px; border-radius: 25px; cursor: pointer; font-weight: 500; transition: all 0.3s;">
                    {{ $name }}
                </button>
                @endforeach
            </form>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        @if($search || $category || $stallId)
        <div style="margin-bottom: 30px; text-align: center;">
            <h2 style="color: var(--dark); margin-bottom: 10px; font-size: 1.8rem;">
                @if($search)
                    Search Results for "{{ $search }}"
                @elseif($category)
                    {{ ucfirst(str_replace(['-', '_'], ' ', $category)) }} Dishes
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
            <div class="card" style="overflow: hidden; border-radius: 16px; background: white;">
                <div style="position: relative;">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $product->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                    <button onclick="toggleFavorite({{ $product->id }})" style="position: absolute; top: 15px; right: 15px; background: white; border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        ♡
                    </button>
                </div>
                <div style="padding: 25px;">
                    <h3 style="font-size: 1.2rem; margin: 0 0 10px 0; color: var(--dark);">{{ $product->name }}</h3>
                    <p style="color: var(--gray); margin: 0 0 15px 0; font-size: 0.9rem; line-height: 1.4;">{{ Str::limit($product->description, 80) }}</p>
                    
                    <div style="margin-bottom: 15px;">
                        <small style="color: var(--gray); background: #f1f5f9; padding: 4px 8px; border-radius: 12px;">{{ $product->stall->name }}</small>
                    </div>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                        @for($i = 1; $i <= 5; $i++)
                            <span style="color: #fbbf24; font-size: 0.9rem;">{{ $i <= 4 ? '★' : '☆' }}</span>
                        @endfor
                        <span style="margin-left: 8px; font-size: 0.9rem; color: var(--gray);">4.{{ rand(6, 9) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--primary);">₱{{ number_format($product->price, 2) }}</div>
                        <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->image }}')" class="btn btn-primary" style="padding: 10px 20px;">
                            Add to cart
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">🍽️</div>
                <h3 style="color: var(--gray); margin-bottom: 10px;">No dishes found</h3>
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
    .filter-btn:hover {
        background: var(--primary) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }
    
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
function toggleFavorite(itemId) {
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