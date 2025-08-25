@extends('layouts.canteen')

@section('title', 'Menu - LTO Canteen Central')

@section('content')
<!-- Hero Section with Burger Image -->
<section style="padding: 100px 0 60px; background: linear-gradient(135deg, #2E5BBA 0%, #3b82f6 100%); position: relative; overflow: hidden;">
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 40px;">
            <!-- Left Content -->
            <div style="flex: 1; min-width: 300px;">
                <div style="background: rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 25px; display: inline-block; margin-bottom: 20px;">
                    <span style="color: white; font-size: 14px; font-weight: 600;">🍽️ Fresh & Delicious</span>
                </div>
                <h1 style="color: white; font-size: 3.5rem; font-weight: 800; margin: 0 0 20px 0; line-height: 1.1;">Our Menu</h1>
                <p style="color: rgba(255,255,255,0.9); font-size: 1.2rem; margin: 0 0 30px 0; line-height: 1.6;">
                    Discover amazing food from multiple vendors in one place. Fresh ingredients, bold flavors, and tasty options for everyone.
                </p>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; color: white; font-size: 14px; font-weight: 500;">
                        ⚡ Fast Service
                    </span>
                    <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; color: white; font-size: 14px; font-weight: 500;">
                        🥘 Multiple Cuisines
                    </span>
                    <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; color: white; font-size: 14px; font-weight: 500;">
                        💰 Great Prices
                    </span>
                </div>
            </div>
            
            <!-- Right Content - Burger Image -->
            <div style="flex: 1; text-align: center; position: relative;">
                <div style="position: relative; display: inline-block;">
                    <!-- Main Burger Image -->
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDMwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxjaXJjbGUgY3g9IjE1MCIgY3k9IjE1MCIgcj0iMTQwIiBmaWxsPSIjRkY2QjM1Ii8+CjxyZWN0IHg9IjUwIiB5PSI4MCIgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMCIgcng9IjEwIiBmaWxsPSIjQjQ1MzA5Ii8+CjxyZWN0IHg9IjUwIiB5PSIxMTAiIHdpZHRoPSIyMDAiIGhlaWdodD0iMzAiIHJ4PSIxNSIgZmlsbD0iIzIyQzU1RSIvPgo8cmVjdCB4PSI1MCIgeT0iMTUwIiB3aWR0aD0iMjAwIiBoZWlnaHQ9IjQwIiByeD0iMjAiIGZpbGw9IiM5MjQwMEQiLz4KPHJlY3QgeD0iNTAiIHk9IjIwMCIgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMCIgcng9IjEwIiBmaWxsPSIjQjQ1MzA5Ii8+Cjx0ZXh0IHg9IjE1MCIgeT0iMjUwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSJ3aGl0ZSIgZm9udC1zaXplPSIyNCIgZm9udC13ZWlnaHQ9ImJvbGQiPvCfjYQ8L3RleHQ+Cjwvc3ZnPgo=" 
                         alt="Delicious Burger" 
                         style="width: 280px; height: 280px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                    
                    <!-- Floating elements -->
                    <div style="position: absolute; top: 20px; right: -20px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px; border-radius: 15px; animation: float 3s ease-in-out infinite;">
                        <span style="font-size: 24px;">🔥</span>
                    </div>
                    <div style="position: absolute; bottom: 30px; left: -30px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px; border-radius: 15px; animation: float 3s ease-in-out infinite reverse;">
                        <span style="font-size: 24px;">⭐</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Background Pattern -->
    <div style="position: absolute; top: 0; right: 0; width: 100%; height: 100%; background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="1"/></g></svg>'); opacity: 0.3;"></div>
</section>

<!-- Categories Section -->
<section style="padding: 40px 0 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <!-- Category Navigation -->
        <div style="display: flex; gap: 15px; overflow-x: auto; padding: 0 0 20px 0; scrollbar-width: none; -ms-overflow-style: none;">
            <a href="{{ route('menu.index') }}" 
               style="min-width: 80px; text-align: center; padding: 15px 20px; background: {{ !request('category') ? '#2E5BBA' : 'white' }}; color: {{ !request('category') ? 'white' : '#2E5BBA' }}; border-radius: 20px; text-decoration: none; font-weight: 600; border: 2px solid #2E5BBA; transition: all 0.3s; flex-shrink: 0; box-shadow: {{ !request('category') ? '0 4px 15px rgba(46,91,186,0.3)' : '0 2px 8px rgba(0,0,0,0.06)' }};">
                <div style="font-size: 24px; margin-bottom: 8px;">🍽️</div>
                <div style="font-size: 12px; line-height: 1.2; font-weight: 600;">All Items</div>
            </a>
            
            <!-- Update these to use actual category filtering from your MenuController -->
            <a href="{{ route('menu.index', ['category' => 'breakfast']) }}" 
               style="min-width: 80px; text-align: center; padding: 15px 20px; background: {{ request('category') === 'breakfast' ? '#2E5BBA' : 'white' }}; color: {{ request('category') === 'breakfast' ? 'white' : '#2E5BBA' }}; border-radius: 20px; text-decoration: none; font-weight: 600; border: 2px solid #2E5BBA; transition: all 0.3s; flex-shrink: 0; box-shadow: {{ request('category') === 'breakfast' ? '0 4px 15px rgba(46,91,186,0.3)' : '0 2px 8px rgba(0,0,0,0.06)' }};">
                <div style="font-size: 24px; margin-bottom: 8px;">🥞</div>
                <div style="font-size: 12px; line-height: 1.2; font-weight: 600;">Breakfast</div>
            </a>
            
            <a href="{{ route('menu.index', ['category' => 'rice']) }}" 
               style="min-width: 80px; text-align: center; padding: 15px 20px; background: {{ request('category') === 'rice' ? '#2E5BBA' : 'white' }}; color: {{ request('category') === 'rice' ? 'white' : '#2E5BBA' }}; border-radius: 20px; text-decoration: none; font-weight: 600; border: 2px solid #2E5BBA; transition: all 0.3s; flex-shrink: 0; box-shadow: {{ request('category') === 'rice' ? '0 4px 15px rgba(46,91,186,0.3)' : '0 2px 8px rgba(0,0,0,0.06)' }};">
                <div style="font-size: 24px; margin-bottom: 8px;">🍚</div>
                <div style="font-size: 12px; line-height: 1.2; font-weight: 600;">Rice Meals</div>
            </a>
            
            <a href="{{ route('menu.index', ['category' => 'pasta']) }}" 
               style="min-width: 80px; text-align: center; padding: 15px 20px; background: {{ request('category') === 'pasta' ? '#2E5BBA' : 'white' }}; color: {{ request('category') === 'pasta' ? 'white' : '#2E5BBA' }}; border-radius: 20px; text-decoration: none; font-weight: 600; border: 2px solid #2E5BBA; transition: all 0.3s; flex-shrink: 0; box-shadow: {{ request('category') === 'pasta' ? '0 4px 15px rgba(46,91,186,0.3)' : '0 2px 8px rgba(0,0,0,0.06)' }};">
                <div style="font-size: 24px; margin-bottom: 8px;">🍝</div>
                <div style="font-size: 12px; line-height: 1.2; font-weight: 600;">Pasta & Noodles</div>
            </a>
            
            <a href="{{ route('menu.index', ['category' => 'snacks']) }}" 
               style="min-width: 80px; text-align: center; padding: 15px 20px; background: {{ request('category') === 'snacks' ? '#2E5BBA' : 'white' }}; color: {{ request('category') === 'snacks' ? 'white' : '#2E5BBA' }}; border-radius: 20px; text-decoration: none; font-weight: 600; border: 2px solid #2E5BBA; transition: all 0.3s; flex-shrink: 0; box-shadow: {{ request('category') === 'snacks' ? '0 4px 15px rgba(46,91,186,0.3)' : '0 2px 8px rgba(0,0,0,0.06)' }};">
                <div style="font-size: 24px; margin-bottom: 8px;">🍟</div>
                <div style="font-size: 12px; line-height: 1.2; font-weight: 600;">Snacks</div>
            </a>
            
            <a href="{{ route('menu.index', ['category' => 'beverages']) }}" 
               style="min-width: 80px; text-align: center; padding: 15px 20px; background: {{ request('category') === 'beverages' ? '#2E5BBA' : 'white' }}; color: {{ request('category') === 'beverages' ? 'white' : '#2E5BBA' }}; border-radius: 20px; text-decoration: none; font-weight: 600; border: 2px solid #2E5BBA; transition: all 0.3s; flex-shrink: 0; box-shadow: {{ request('category') === 'beverages' ? '0 4px 15px rgba(46,91,186,0.3)' : '0 2px 8px rgba(0,0,0,0.06)' }};">
                <div style="font-size: 24px; margin-bottom: 8px;">🥤</div>
                <div style="font-size: 12px; line-height: 1.2; font-weight: 600;">Beverages</div>
            </a>
        </div>

        <!-- Search Bar -->
        <div style="background: white; border-radius: 15px; padding: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
            <form action="{{ route('menu.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <!-- Preserve current category in search -->
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search for food items..." 
                           style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='#2E5BBA'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
                
                <select name="stall" style="padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; background: white; min-width: 150px; font-size: 14px; outline: none;">
                    <option value="">All Stalls</option>
                    @foreach(App\Models\Stall::where('is_active', true)->get() as $stall)
                        <option value="{{ $stall->id }}" {{ request('stall') == $stall->id ? 'selected' : '' }}>
                            {{ $stall->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" style="background: #2E5BBA; color: white; padding: 12px 20px; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#1e40af'"
                        onmouseout="this.style.background='#2E5BBA'">
                    Search
                </button>

                @if(request()->hasAny(['search', 'category', 'stall']))
                    <a href="{{ route('menu.index') }}" style="background: #f1f5f9; color: #64748b; padding: 12px 20px; border-radius: 12px; text-decoration: none; font-weight: 500; transition: all 0.2s;"
                       onmouseover="this.style.background='#e2e8f0'"
                       onmouseout="this.style.background='#f1f5f9'">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Results Header -->
        <div style="margin-bottom: 25px;">
            <h2 style="color: #1e293b; font-size: 24px; font-weight: 700; margin: 0 0 6px 0;">
                @if(request('category'))
                    {{ ucfirst(str_replace('-', ' ', request('category'))) }} Items
                @elseif(request('search'))
                    Search Results
                @else
                    All Menu Items
                @endif
            </h2>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0;">{{ $products->total() }} items available</p>
                
                @if(session('user_type') === 'employee')
                    <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        🎉 Employee Discounts Active
                    </span>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section style="padding: 20px 0 60px; background: #f8fafc;">
    <div class="container">
        @if($products->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                @foreach($products as $product)
                    @livewire('add-to-cart-button', [
                        'product' => $product,
                        'showPrice' => true,
                        'showQuantitySelector' => false,
                        'buttonText' => 'Add',
                        'buttonSize' => 'medium'
                    ], key('hero-menu-product-'.$product->id))
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 50px; display: flex; justify-content: center;">
                <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <!-- No Results -->
            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div style="font-size: 64px; margin-bottom: 24px; opacity: 0.3;">🔍</div>
                <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">No items found</h3>
                <p style="color: #64748b; margin-bottom: 32px;">Try adjusting your search or browse all available items.</p>
                <a href="{{ route('menu.index') }}" style="background: #2E5BBA; color: white; padding: 14px 28px; border-radius: 25px; text-decoration: none; font-weight: 600;">
                    View All Items
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Animations -->
<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

div::-webkit-scrollbar {
    height: 0px;
    background: transparent;
}
</style>
@endsection