@extends('layouts.canteen')

@section('title', 'Stalls - Kajacms')

@section('content')
<!-- Header Section -->
<section style="padding: 40px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 2.8rem; color: var(--dark); margin-bottom: 15px;">Our Food Stalls</h1>
            <p style="color: var(--gray); max-width: 600px; font-size: 1.1rem;">Explore delicious offerings from our vendors</p>
        </div>
        
        <!-- Search and Filter Bar -->
        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <form action="/stalls" method="GET" style="display: flex; gap: 15px; align-items: center; flex: 1;">
                <input type="text" name="search" placeholder="Search stalls or products..." 
                       style="flex: 1; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" 
                       value="{{ $search }}">
                       
                <select name="category" style="padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; background: white; min-width: 150px;">
                    <option value="">All Categories</option>
                    <option value="fresh-meals" {{ request('category') === 'fresh-meals' ? 'selected' : '' }}>Fresh Meals</option>
                    <option value="sandwiches" {{ request('category') === 'sandwiches' ? 'selected' : '' }}>Sandwiches</option>
                    <option value="beverages" {{ request('category') === 'beverages' ? 'selected' : '' }}>Beverages</option>
                    <option value="snacks" {{ request('category') === 'snacks' ? 'selected' : '' }}>Snacks</option>
                </select>
                
                <button type="submit" style="background: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer;">
                    🔍
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Food Items Grid -->
<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        <div class="grid grid-3">
            @php
                // Get all products across all stalls for the stalls page
                $allProducts = \App\Models\Product::with('stall')
                    ->when(request('search'), function($query, $search) {
                        return $query->where('name', 'like', "%{$search}%")
                                   ->orWhere('description', 'like', "%{$search}%")
                                   ->orWhereHas('stall', function($q) use ($search) {
                                       $q->where('name', 'like', "%{$search}%");
                                   });
                    })
                    ->when(request('category'), function($query, $category) {
                        return $query->where('category', $category);
                    })
                    ->get();
            @endphp
            
            @forelse($allProducts as $product)
            <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative;">
                <!-- Product Image -->
                <div style="position: relative;">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=' . urlencode($product->name) }}" 
                         alt="{{ $product->name }}" 
                         style="width: 100%; height: 200px; object-fit: cover;">
                    
                    <!-- Stall Name Tag -->
                    <div style="position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.9); color: var(--dark); padding: 4px 8px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                        {{ $product->stall->name }}
                    </div>
                </div>
                
                <div style="padding: 20px;">
                    <!-- Product Name -->
                    <h3 style="font-size: 1.1rem; margin: 0 0 8px 0; color: var(--dark); font-weight: 600;">{{ $product->name }}</h3>
                    
                    <!-- Price -->
                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--primary); margin-bottom: 15px;">₱{{ number_format($product->price, 2) }}</div>
                    
                    <!-- Description -->
                    <p style="color: var(--gray); margin: 0 0 15px 0; font-size: 0.9rem; line-height: 1.4;">{{ Str::limit($product->description, 60) }}</p>
                    
                    <!-- Special Items Labels -->
                    @if($product->category === 'fresh-meals')
                        <div style="margin-bottom: 15px;">
                            <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Daily Special Meal</span>
                        </div>
                    @elseif($product->category === 'beverages')
                        <div style="margin-bottom: 15px;">
                            <span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Homemade Lemonade</span>
                        </div>
                    @elseif($product->category === 'sandwiches')
                        <div style="margin-bottom: 15px;">
                            <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Chef's Special Drink</span>
                        </div>
                    @endif
                    
                    <!-- Add Button -->
                    <button onclick="addToCart({{ $product->id }}, {{ json_encode($product->name) }}, {{ $product->price }}, {{ json_encode($product->image ?: '') }})" 
                            style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%;">
                        Add
                    </button>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">🍽️</div>
                <h3 style="color: var(--gray); margin-bottom: 10px;">No food items found</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">Try adjusting your search or filters</p>
                <a href="/stalls" class="btn btn-primary">View All Items</a>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection