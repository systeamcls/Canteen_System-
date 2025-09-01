@extends('layouts.canteen')

@section('title', 'Menu - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 80px 20px 120px; background: linear-gradient(135deg, #FF6B35 0%, #FF4500 50%, #DC2626 100%); position: relative; overflow: hidden; border-radius: 0 0 48px 48px;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; position: relative; z-index: 2;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 60px;">
            <!-- Left Content -->
            <div style="flex: 1; min-width: 320px; max-width: 500px;">
                <h1 style="color: white; font-size: 3rem; font-weight: 800; margin-bottom: 24px; line-height: 1.1; text-shadow: 0 4px 12px rgba(0,0,0,0.2); font-family: system-ui, -apple-system, sans-serif;">
                    Our Menu
                </h1>
                <p style="color: rgba(255,255,255,0.9); font-size: 1.125rem; margin-bottom: 32px; line-height: 1.6; font-weight: 400;">
                    Discover amazing food from multiple vendors in one place. Fresh ingredients, bold flavors, and tasty options for everyone.
                </p>
                
                <!-- Feature Pills -->
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 50px; color: white; font-size: 14px; font-weight: 500;">
                        ⚡ Fast Service
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 50px; color: white; font-size: 14px; font-weight: 500;">
                        🌐 Multiple Cuisines
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 50px; color: white; font-size: 14px; font-weight: 500;">
                        💰 Great Prices
                    </div>
                </div>
            </div>

            <!-- Right Visual -->
            <div style="flex: 1; display: flex; justify-content: center; align-items: center; position: relative; min-height: 300px;">
                <div style="width: 320px; height: 320px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; backdrop-filter: blur(20px);">
                    <!-- Burger Emoji - Made Bigger -->
                    <div style="font-size: 120px; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.2));">🍔</div>
                </div>
                
                <!-- Floating Elements -->
                <div style="position: absolute; top: 10%; right: 10%; background: rgba(16, 185, 129, 0.2); backdrop-filter: blur(10px); padding: 12px; border-radius: 50%; animation: float 3s ease-in-out infinite;">
                    <div style="font-size: 24px;">⭐</div>
                </div>
                <div style="position: absolute; bottom: 20%; left: 5%; background: rgba(239, 68, 68, 0.2); backdrop-filter: blur(10px); padding: 12px; border-radius: 50%; animation: float 3s ease-in-out infinite reverse;">
                    <div style="font-size: 24px;">🔥</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating Search Card -->
<section style="position: relative; z-index: 10; margin-top: -60px; padding: 0 20px 40px;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <div style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.2);">
            <form action="{{ route('menu.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <!-- Search Input -->
                <div style="position: relative; flex: 1; min-width: 300px;">
                    <svg style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #9ca3af; z-index: 1;" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search for delicious food items..."
                           style="width: 100%; padding: 16px 20px 16px 52px; border: 2px solid #f1f5f9; border-radius: 16px; font-size: 16px; background: #fafafa; transition: all 0.3s ease; outline: none;"
                           onfocus="this.style.borderColor='#FF6B35'; this.style.background='white'"
                           onblur="this.style.borderColor='#f1f5f9'; this.style.background='#fafafa'">
                </div>

                <!-- Stall Dropdown -->
                <select name="stall" style="padding: 16px 20px; border: 2px solid #f1f5f9; border-radius: 16px; background: #fafafa; min-width: 160px; font-size: 16px; font-weight: 500; transition: all 0.3s ease; outline: none;"
                        onfocus="this.style.borderColor='#FF6B35'; this.style.background='white'"
                        onblur="this.style.borderColor='#f1f5f9'; this.style.background='#fafafa'">
                    <option value="">All Stalls</option>
                    @foreach(App\Models\Stall::where('is_active', true)->get() as $stall)
                        <option value="{{ $stall->id }}" {{ request('stall') == $stall->id ? 'selected' : '' }}>
                            {{ $stall->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Search Button -->
                <button type="submit" style="background: #FF6B35; color: white; padding: 16px 32px; border: none; border-radius: 16px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 4px 14px rgba(255, 107, 53, 0.3);"
                        onmouseover="this.style.background='#FF4500'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 107, 53, 0.4)'"
                        onmouseout="this.style.background='#FF6B35'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(255, 107, 53, 0.3)'">
                    Search
                </button>

                <!-- Clear Button -->
                @if(request()->hasAny(['search', 'category', 'stall']))
                    <a href="{{ route('menu.index') }}" style="background: #f8fafc; color: #64748b; padding: 16px 24px; border-radius: 16px; font-weight: 500; border: 2px solid #f1f5f9; transition: all 0.3s ease; text-decoration: none;"
                       onmouseover="this.style.background='#f1f5f9'; this.style.color='#374151'"
                       onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b'">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section style="padding: 24px 0 16px; background: #fafbfc; position: sticky; top: 60px; z-index: 50; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        
        <!-- Desktop Category Pills -->
        <div class="category-desktop" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            <!-- All Items -->
            <button class="filter-category active" data-category="all" 
               style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: 2px solid transparent; background: #1e293b; color: white; box-shadow: 0 4px 14px rgba(30, 41, 59, 0.3); cursor: pointer;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                </svg>
                All Items
            </button>

            <!-- Fresh Meats -->
            <button class="filter-category" data-category="fresh-meals"
               style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: 2px solid #e2e8f0; background: white; color: #64748b; cursor: pointer;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                Fresh Meats
            </button>

            <!-- Sandwiches -->
            <button class="filter-category" data-category="sandwiches"
               style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: 2px solid #e2e8f0; background: white; color: #64748b; cursor: pointer;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"/>
                </svg>
                Sandwiches
            </button>

            <!-- Beverages -->
            <button class="filter-category" data-category="beverages"
               style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: 2px solid #e2e8f0; background: white; color: #64748b; cursor: pointer;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                Beverages
            </button>

            <!-- Snacks -->
            <button class="filter-category" data-category="snacks"
               style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: 2px solid #e2e8f0; background: white; color: #64748b; cursor: pointer;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 2a1 1 0 000 2h1.17l.313 1.249L8.58 10.52A1 1 0 009.553 11H16a1 1 0 00.894-1.447L18.618 6H16V4a2 2 0 00-2-2H5zM9 16a2 2 0 11-4 0 2 2 0 014 0zM19 16a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Snacks
            </button>

            <!-- Desserts -->
            <button class="filter-category" data-category="desserts"
               style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; border: 2px solid #e2e8f0; background: white; color: #64748b; cursor: pointer;"
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm4 14a4 4 0 100-8 4 4 0 000 8z" clip-rule="evenodd"/>
                </svg>
                Desserts
            </button>
        </div>

        <!-- Mobile Category Dropdown -->
        <div class="category-mobile" style="display: none; margin-bottom: 16px; text-align: center;">
            <select id="category-dropdown" style="width: 100%; max-width: 300px; padding: 16px 20px; border: 2px solid #e2e8f0; border-radius: 16px; background: white; font-size: 16px; font-weight: 500; color: #1e293b; outline: none; cursor: pointer;">
                <option value="all">🍽️ All Items</option>
                <option value="fresh-meats">🥩 Fresh Meats</option>
                <option value="sandwiches">🥪 Sandwiches</option>
                <option value="beverages">🥤 Beverages</option>
                <option value="snacks">🍟 Snacks</option>
                <option value="desserts">🍰 Desserts</option>
            </select>
        </div>
    </div>
</section>

<!-- Section Title - Moved outside sticky area -->
<section style="padding: 16px 0 0; background: #fafbfc;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="text-align: left; margin-bottom: 24px;">
            <h2 style="color: #1e293b; font-size: 2rem; font-weight: 700; margin-bottom: 8px; font-family: system-ui, -apple-system, sans-serif;">
                <span id="category-title">All Menu Items</span>
            </h2>
            <p style="color: #64748b; font-size: 1.125rem; margin: 0;"><span id="items-count">{{ $products->total() }}</span> items available</p>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section style="padding: 0 0 80px; background: #fafbfc;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        @if($products->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;" id="menuGrid">
                @foreach($products as $product)
                    <div class="menu-item" data-category="{{ strtolower(str_replace(' ', '-', $product->category->name ?? 'uncategorized')) }}" style="opacity: 1; transform: translateY(0); transition: all 0.3s ease;">
                        @livewire('add-to-cart-button', [
                            'product' => $product,
                            'showPrice' => true,
                            'showQuantitySelector' => false,
                            'buttonText' => 'Add',
                            'buttonSize' => 'medium'
                        ], key('hero-menu-product-'.$product->id))
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-top: 60px; display: flex; justify-content: center;">
                <div style="background: white; padding: 20px; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <!-- No Results -->
            <div style="text-align: center; padding: 80px 32px; background: white; border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
                <div style="font-size: 4rem; margin-bottom: 24px; opacity: 0.3;">🍔</div>
                <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 1.5rem; font-weight: 600;">No items found</h3>
                <p style="color: #64748b; margin-bottom: 32px; font-size: 1.125rem;">Try adjusting your search or browse all available items.</p>
                <a href="{{ route('menu.index') }}" style="display: inline-block; background: #FF6B35; color: white; padding: 16px 32px; border-radius: 16px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 14px rgba(255, 107, 53, 0.3); transition: all 0.3s ease;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 107, 53, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(255, 107, 53, 0.3)'">
                    View All Items
                </a>
            </div>
        @endif
    </div>
</section>

<!-- CSS Animations and JavaScript -->
<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Show/hide category elements based on screen size */
@media (max-width: 767px) {
    .category-desktop {
        display: none !important;
    }
    
    .category-mobile {
        display: block !important;
    }
}

@media (min-width: 768px) {
    .category-desktop {
        display: flex !important;
    }
    
    .category-mobile {
        display: none !important;
    }
}

/* McDonald's-level Mobile Responsiveness */
/* Small phones (320px - 480px) */
@media (max-width: 480px) {
    section:first-child {
        padding: 40px 16px 80px !important;
        border-radius: 0 0 24px 24px !important;
    }
    
    section:first-child h1 {
        font-size: 2rem !important;
        margin-bottom: 16px !important;
        text-align: center;
    }
    
    section:first-child p {
        font-size: 1rem !important;
        margin-bottom: 24px !important;
        text-align: center;
    }
    
    section:first-child > div > div {
        flex-direction: column !important;
        text-align: center !important;
        gap: 40px !important;
    }
    
    /* Hide burger visual on very small screens */
    section:first-child > div > div > div:last-child {
        display: none !important;
    }
    
    /* Make pills stack better */
    section:first-child .hero-pills {
        justify-content: center !important;
        gap: 8px !important;
    }
    
    /* Search card mobile optimization */
    section:nth-child(2) {
        margin-top: -40px !important;
        padding: 0 16px 24px !important;
    }
    
    section:nth-child(2) > div {
        max-width: 100% !important;
    }
    
    section:nth-child(2) form {
        flex-direction: column !important;
        gap: 16px !important;
    }
    
    section:nth-child(2) form > div:first-child {
        width: 100% !important;
        min-width: unset !important;
    }
    
    section:nth-child(2) select {
        width: 100% !important;
        min-width: unset !important;
    }
    
    section:nth-child(2) button,
    section:nth-child(2) a {
        width: 100% !important;
        min-height: 48px !important;
        justify-content: center !important;
    }
    
    /* Category section mobile */
    section:nth-child(3) {
        padding: 40px 0 24px !important;
    }
    
    section:nth-child(3) > div {
        padding: 0 16px !important;
    }
    
    /* Products grid mobile */
    section:last-child > div {
        padding: 0 16px !important;
    }
    
    section:last-child div[style*="grid"] {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }
}

/* Large phones (481px - 767px) */
@media (min-width: 481px) and (max-width: 767px) {
    section:first-child {
        padding: 60px 20px 100px !important;
        border-radius: 0 0 32px 32px !important;
    }
    
    section:first-child h1 {
        font-size: 2.5rem !important;
    }
    
    section:first-child > div > div {
        text-align: center !important;
        gap: 50px !important;
    }
    
    /* Show smaller burger on larger phones */
    section:first-child > div > div > div:last-child {
        display: flex !important;
    }
    
    section:first-child > div > div > div:last-child > div {
        width: 200px !important;
        height: 200px !important;
    }
    
    section:first-child > div > div > div:last-child > div > div {
        font-size: 80px !important;
    }
    
    /* Search optimizations */
    section:nth-child(2) form {
        flex-wrap: wrap !important;
        justify-content: center !important;
    }
    
    section:nth-child(2) form > div:first-child {
        flex: 1 1 100% !important;
        margin-bottom: 16px !important;
    }
    
    section:nth-child(2) select {
        flex: 1 !important;
        min-width: 140px !important;
    }
    
    /* Products grid */
    section:last-child div[style*="grid"] {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }
}

/* Tablets (768px - 1024px) */
@media (min-width: 768px) and (max-width: 1024px) {
    section:first-child {
        padding: 70px 20px 110px !important;
    }
    
    section:first-child h1 {
        font-size: 3rem !important;
    }
    
    /* Search form on tablets */
    section:nth-child(2) > div {
        max-width: 900px !important;
    }
    
    /* Products grid */
    section:last-child div[style*="grid"] {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

/* Large screens (1025px+) */
@media (min-width: 1025px) {
    section:first-child {
        border-radius: 0 0 48px 48px !important;
    }
    
    /* Products grid - 4 columns on desktop */
    section:last-child div[style*="grid"] {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 24px !important;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    button, a, select, input {
        min-height: 44px !important;
    }
    
    .filter-category {
        min-height: 48px !important;
        padding: 12px 20px !important;
    }
}

/* Custom scrollbar */
*::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

*::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

*::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

*::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
* {
    box-sizing: border-box;
}


button:focus,
input:focus,
select:focus {
    outline: 2px solid #FF6B35;
    outline-offset: 2px;
}
</style>

<!-- JavaScript for Category Filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('.filter-category');
    const menuItems = document.querySelectorAll('.menu-item');
    const categoryDropdown = document.getElementById('category-dropdown');
    const categoryTitle = document.getElementById('category-title');
    const itemsCount = document.getElementById('items-count');
    
    // Category name mapping
    const categoryNames = {
        'all': 'All Menu Items',
        'fresh-meals': 'Fresh Meals',
        'sandwiches': 'Sandwiches', 
        'beverages': 'Beverages', 
        'snacks': 'Snacks',
        'desserts': 'Desserts'
    };

    // Function to filter items
    function filterItems(selectedCategory) {
        let visibleCount = 0;
        
        menuItems.forEach(item => {
            if (selectedCategory === 'all' || item.dataset.category === selectedCategory) {
                item.style.display = 'block';
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 50);
                
                visibleCount++;
            } else {
                item.style.opacity = '0';
                item.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        });
        
        // Update title and count
        categoryTitle.textContent = categoryNames[selectedCategory] || 'All Menu Items';
        itemsCount.textContent = visibleCount;
    }

    // Function to update button states
    function updateButtonStates(selectedCategory) {
        categoryButtons.forEach(btn => {
            if (btn.dataset.category === selectedCategory) {
                btn.style.background = '#1e293b';
                btn.style.color = 'white';
                btn.style.borderColor = '#1e293b';
                btn.style.boxShadow = '0 4px 14px rgba(30, 41, 59, 0.3)';
                btn.classList.add('active');
            } else {
                btn.style.background = 'white';
                btn.style.color = '#64748b';
                btn.style.borderColor = '#e2e8f0';
                btn.style.boxShadow = 'none';
                btn.classList.remove('active');
            }
        });
    }

    // Desktop category button clicks
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedCategory = this.dataset.category;
            
            updateButtonStates(selectedCategory);
            filterItems(selectedCategory);
            
            // Update dropdown to match
            categoryDropdown.value = selectedCategory;
        });
    });

    // Mobile dropdown change
    categoryDropdown.addEventListener('change', function() {
        const selectedCategory = this.value;
        
        updateButtonStates(selectedCategory);
        filterItems(selectedCategory);
    });
});
</script>
@endsection