@extends('layouts.canteen')

@section('title', 'Menu - LTO Canteen Central')

@section('content')
    <!-- Hero Section - ELEVATED VERSION -->
    <section
        style="padding: 100px 20px 140px; background: linear-gradient(135deg, #FF6B35 0%, #FF4500 50%, #DC2626 100%); position: relative; overflow: hidden; border-radius: 0 0 48px 48px;">
        <!-- Animated Background Pattern -->
        <div
            style="position: absolute; inset: 0; opacity: 0.1; background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px; animation: backgroundMove 20s linear infinite;">
        </div>

        <!-- Floating Food Elements -->
        <div
            style="position: absolute; top: 15%; left: 8%; font-size: 48px; opacity: 0.3; animation: float 6s ease-in-out infinite;">
            🍕</div>
        <div
            style="position: absolute; top: 60%; left: 12%; font-size: 40px; opacity: 0.25; animation: float 7s ease-in-out infinite 1s;">
            🍜</div>
        <div
            style="position: absolute; top: 25%; right: 10%; font-size: 52px; opacity: 0.3; animation: float 5s ease-in-out infinite 2s;">
            🍰</div>
        <div
            style="position: absolute; bottom: 20%; right: 15%; font-size: 44px; opacity: 0.25; animation: float 6.5s ease-in-out infinite 1.5s;">
            🥗</div>

        <div class="container" style="max-width: 1200px; margin: 0 auto; position: relative; z-index: 2;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 60px;">

                <!-- Left Content -->
                <div style="flex: 1; min-width: 320px; max-width: 600px;">
                    <h1
                        style="color: white; font-size: clamp(3rem, 8vw, 5rem); font-weight: 900; margin-bottom: 24px; line-height: 1.1; text-shadow: 0 4px 20px rgba(0,0,0,0.3); font-family: system-ui, -apple-system, sans-serif; letter-spacing: -0.02em;">
                        Discover Your<br>
                        <span
                            style="background: linear-gradient(to right, #FFF, #FFE4CC); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Next
                            Favorite</span>
                    </h1>

                    <p
                        style="color: rgba(255,255,255,0.95); font-size: 1.25rem; margin-bottom: 32px; line-height: 1.7; font-weight: 400; text-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                        Explore amazing dishes from multiple stalls. Fresh ingredients, bold flavors, and delicious options
                        for everyone.
                    </p>

                    <!-- Stats Pills -->
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <div
                            style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px 20px; border-radius: 50px; color: white; font-weight: 600; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <span style="font-size: 24px;">⚡</span>
                            <span>Fast Service</span>
                        </div>
                        <div
                            style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px 20px; border-radius: 50px; color: white; font-weight: 600; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <span style="font-size: 24px;">🌐</span>
                            <span>{{ App\Models\Stall::where('is_active', true)->count() }} Stalls</span>
                        </div>
                        <div
                            style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 12px 20px; border-radius: 50px; color: white; font-weight: 600; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <span style="font-size: 24px;">💰</span>
                            <span>Great Prices</span>
                        </div>
                    </div>
                </div>

                <!-- Right Visual - Image Carousel -->
                <div
                    style="flex: 1; display: flex; justify-content: center; align-items: center; position: relative; min-height: 400px; min-width: 320px;">
                    <!-- Main Circle Container -->
                    <div
                        style="width: 400px; height: 400px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; backdrop-filter: blur(20px); border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 20px 60px rgba(0,0,0,0.3);">

                        <!-- Rotating Food Images -->
                        <div id="foodCarousel"
                            style="position: relative; width: 280px; height: 280px; border-radius: 50%; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.4);">
                            <!-- Food Slide 1 -->
                            <div class="food-slide active"
                                style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 140px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); transition: opacity 1s ease;">
                                🍔
                            </div>
                            <!-- Food Slide 2 -->
                            <div class="food-slide"
                                style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 140px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); opacity: 0; transition: opacity 1s ease;">
                                🍕
                            </div>
                            <!-- Food Slide 3 -->
                            <div class="food-slide"
                                style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 140px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); opacity: 0; transition: opacity 1s ease;">
                                🍜
                            </div>
                            <!-- Food Slide 4 -->
                            <div class="food-slide"
                                style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 140px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); opacity: 0; transition: opacity 1s ease;">
                                🥗
                            </div>
                        </div>

                        <!-- Decorative Rings -->
                        <div
                            style="position: absolute; inset: -20px; border: 3px dashed rgba(255,255,255,0.3); border-radius: 50%; animation: rotate 20s linear infinite;">
                        </div>
                        <div
                            style="position: absolute; inset: -40px; border: 2px dotted rgba(255,255,255,0.2); border-radius: 50%; animation: rotate 30s linear infinite reverse;">
                        </div>
                    </div>

                    <!-- Floating Stats Badges -->
                    <div
                        style="position: absolute; top: 10%; right: 5%; background: white; color: #1e293b; padding: 16px 24px; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.3); animation: float 4s ease-in-out infinite;">
                        <div style="font-size: 28px; font-weight: 800; color: #FF4500;">
                            {{ App\Models\Product::where('is_available', true)->count() }}+</div>
                        <div style="font-size: 12px; color: #64748b; font-weight: 600;">Menu Items</div>
                    </div>

                    <div
                        style="position: absolute; bottom: 15%; left: 0; background: white; color: #1e293b; padding: 12px 20px; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.3); animation: float 5s ease-in-out infinite 1s;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: #fbbf24; font-size: 20px;">⭐</span>
                            <div>
                                <div style="font-size: 20px; font-weight: 800; color: #FF4500;">4.8</div>
                                <div style="font-size: 10px; color: #64748b; font-weight: 600;">Rating</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Search Card -->
    <section style="position: relative; z-index: 10; margin-top: -60px; padding: 0 20px 40px;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <div
                style="background: white; border-radius: 24px; padding: 32px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <form action="{{ route('menu.index') }}" method="GET"
                    style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    <!-- Search Input -->
                    <div style="position: relative; flex: 1; min-width: 300px;">
                        <svg style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #9ca3af; z-index: 1;"
                            width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search for delicious food items..."
                            style="width: 100%; padding: 16px 20px 16px 52px; border: 2px solid #f1f5f9; border-radius: 16px; font-size: 16px; background: #fafafa; transition: all 0.3s ease; outline: none;"
                            onfocus="this.style.borderColor='#FF6B35'; this.style.background='white'"
                            onblur="this.style.borderColor='#f1f5f9'; this.style.background='#fafafa'">
                    </div>

                    <!-- Stall Dropdown -->
                    <select name="stall"
                        style="padding: 16px 20px; border: 2px solid #f1f5f9; border-radius: 16px; background: #fafafa; min-width: 160px; font-size: 16px; font-weight: 500; transition: all 0.3s ease; outline: none;"
                        onfocus="this.style.borderColor='#FF6B35'; this.style.background='white'"
                        onblur="this.style.borderColor='#f1f5f9'; this.style.background='#fafafa'">
                        <option value="">All Stalls</option>
                        @foreach (App\Models\Stall::where('is_active', true)->get() as $stall)
                            <option value="{{ $stall->id }}" {{ request('stall') == $stall->id ? 'selected' : '' }}>
                                {{ $stall->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Search Button -->
                    <button type="submit"
                        style="background: #FF6B35; color: white; padding: 16px 32px; border: none; border-radius: 16px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 4px 14px rgba(255, 107, 53, 0.3);"
                        onmouseover="this.style.background='#FF4500'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 107, 53, 0.4)'"
                        onmouseout="this.style.background='#FF6B35'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(255, 107, 53, 0.3)'">
                        Search
                    </button>

                    <!-- Clear Button -->
                    @if (request()->hasAny(['search', 'category', 'stall']))
                        <a href="{{ route('menu.index') }}"
                            style="background: #f8fafc; color: #64748b; padding: 16px 24px; border-radius: 16px; font-weight: 500; border: 2px solid #f1f5f9; transition: all 0.3s ease; text-decoration: none;"
                            onmouseover="this.style.background='#f1f5f9'; this.style.color='#374151'"
                            onmouseout="this.style.background='#f8fafc'; this.style.color='#64748b'">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </section>

    @livewire('menu-products', [
        'selectedCategoryId' => request('category_id'),
        'search' => request('search'),
        'stall' => request('stall'),
    ])


    <!-- Section Title - Moved outside sticky area -->
    <section style="padding: 16px 0 0; background: #fafbfc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: left; margin-bottom: 24px;">
                <h2
                    style="color: #1e293b; font-size: 2rem; font-weight: 700; margin-bottom: 8px; font-family: system-ui, -apple-system, sans-serif;">
                    <span id="category-title">All Menu Items</span>
                </h2>
                <p style="color: #64748b; font-size: 1.125rem; margin: 0;"><span
                        id="items-count">{{ $products->total() }}</span> items available</p>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section style="padding: 0 0 80px; background: #fafbfc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            @if ($products->count() > 0)
                <div id="menuGrid">
                    @foreach ($products as $product)
                        <div class="menu-item"
                            data-category="{{ strtolower(str_replace(' ', '-', $product->category->name ?? 'uncategorized')) }}"
                            style="opacity: 1; transform: translateY(0); transition: all 0.3s ease;">
                            @livewire(
                                'add-to-cart-button',
                                [
                                    'product' => $product,
                                    'showPrice' => true,
                                    'showQuantitySelector' => false,
                                    'buttonText' => 'Add',
                                    'buttonSize' => 'medium',
                                ],
                                key('hero-menu-product-' . $product->id)
                            )
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div style="margin-top: 60px; display: flex; justify-content: center;">
                    <div
                        style="background: white; padding: 12px 16px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 4px;">
                        @if ($products->onFirstPage())
                            <span
                                style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #d1d5db; font-size: 13px; cursor: not-allowed;">‹</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}"
                                style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 3px; color: #6b7280; text-decoration: none; font-size: 13px; background: white;">‹</a>
                        @endif

                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if ($page == $products->currentPage())
                                <span
                                    style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: #FF6B35; color: white; border-radius: 3px; font-size: 13px; font-weight: 500;">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 3px; color: #6b7280; text-decoration: none; font-size: 13px; background: white;">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}"
                                style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; border-radius: 3px; color: #6b7280; text-decoration: none; font-size: 13px; background: white;">›</a>
                        @else
                            <span
                                style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; color: #d1d5db; font-size: 13px; cursor: not-allowed;">›</span>
                        @endif
                    </div>
                </div>
            @else
                <!-- No Results -->
                <div
                    style="text-align: center; padding: 80px 32px; background: white; border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
                    <div style="font-size: 4rem; margin-bottom: 24px; opacity: 0.3;">🍔</div>
                    <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 1.5rem; font-weight: 600;">No items found
                    </h3>
                    <p style="color: #64748b; margin-bottom: 32px; font-size: 1.125rem;">Try adjusting your search or
                        browse all available items.</p>
                    <a href="{{ route('menu.index') }}"
                        style="display: inline-block; background: #FF6B35; color: white; padding: 16px 32px; border-radius: 16px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 14px rgba(255, 107, 53, 0.3); transition: all 0.3s ease;"
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

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
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

            section:first-child>div>div {
                flex-direction: column !important;
                text-align: center !important;
                gap: 40px !important;
            }

            /* Hide burger visual on very small screens */
            section:first-child>div>div>div:last-child {
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

            section:nth-child(2)>div {
                max-width: 100% !important;
            }

            section:nth-child(2) form {
                flex-direction: column !important;
                gap: 16px !important;
            }

            section:nth-child(2) form>div:first-child {
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

            section:nth-child(3)>div {
                padding: 0 16px !important;
            }

            /* Products grid mobile */
            section:last-child>div {
                padding: 0 16px !important;
            }

            section:last-child div[style*="grid"] {
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

            section:first-child>div>div {
                text-align: center !important;
                gap: 50px !important;
            }

            /* Show smaller burger on larger phones */
            section:first-child>div>div>div:last-child {
                display: flex !important;
            }

            section:first-child>div>div>div:last-child>div {
                width: 200px !important;
                height: 200px !important;
            }

            section:first-child>div>div>div:last-child>div>div {
                font-size: 80px !important;
            }

            /* Search optimizations */
            section:nth-child(2) form {
                flex-wrap: wrap !important;
                justify-content: center !important;
            }

            section:nth-child(2) form>div:first-child {
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
            section:nth-child(2)>div {
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

            button,
            a,
            select,
            input {
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

        #menuGrid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media (min-width: 768px) {
            #menuGrid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
        }

        @media (min-width: 1025px) {
            #menuGrid {
                grid-template-columns: repeat(4, 1fr);
                gap: 24px;
            }
        }


        /* Compact Pagination Styles */

        .pagination * {
            all: unset !important;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px;
            margin: 0;
            padding: 0;

        }

        .pagination .page-item {
            margin: 0;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
            margin: 0 2px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            background: white;
        }

        .pagination .page-link:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #374151;
        }

        .pagination .page-item.active .page-link {
            background: #FF6B35;
            border-color: #FF6B35;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #d1d5db;
            cursor: not-allowed;
            background: #f9fafb;
        }

        .pagination .page-item.disabled .page-link:hover {
            background: #f9fafb;
            border-color: #e5e7eb;
        }

        /* Arrow icons - make them smaller */
        .pagination .page-link svg {
            width: 14px;
            height: 14px;
        }

        /* Previous/Next text - hide on small screens */
        @media (max-width: 480px) {
            .pagination .page-link {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .pagination .page-link svg {
                width: 12px;
                height: 12px;
            }
        }

        /* Container styling */
        .pagination-container {
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            border: 1px solid #f1f5f9;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes backgroundMove {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 40px 40px;
            }
        }

        @media (max-width: 768px) {
            .category-desktop {
                display: none !important;
            }

            .category-mobile {
                display: block !important;
            }
        }

        /* Smooth transitions for menu grid */
        #menuGrid {
            transition: opacity 0.3s ease;
        }

        .menu-item {
            animation: fadeInUp 0.4s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation for items */
        .menu-item:nth-child(1) {
            animation-delay: 0.05s;
        }

        .menu-item:nth-child(2) {
            animation-delay: 0.1s;
        }

        .menu-item:nth-child(3) {
            animation-delay: 0.15s;
        }

        .menu-item:nth-child(4) {
            animation-delay: 0.2s;
        }

        .menu-item:nth-child(5) {
            animation-delay: 0.25s;
        }

        .menu-item:nth-child(6) {
            animation-delay: 0.3s;
        }

        .menu-item:nth-child(7) {
            animation-delay: 0.35s;
        }

        .menu-item:nth-child(8) {
            animation-delay: 0.4s;
        }

        /* Loading state */
        #menuGrid.loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>

    <!-- JavaScript for Category Filtering -->
    <script>
        // Food Carousel Auto-rotate
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.food-slide');
            let currentSlide = 0;

            function nextSlide() {
                slides[currentSlide].style.opacity = '0';
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].style.opacity = '1';
            }

            // Change slide every 3 seconds
            setInterval(nextSlide, 3000);
        });
    </script>


@endsection
