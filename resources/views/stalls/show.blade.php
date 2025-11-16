@extends('layouts.canteen')

@section('title', $stall->name . ' - LTO Canteen Central')

@section('content')
    <!-- Breadcrumb -->
    <div style="padding: 24px 0 0; background: #f8fafc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; margin-bottom: 24px;">
                <a href="/" style="color: #64748b; text-decoration: none; transition: color 0.2s;"
                    onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#64748b'">Store</a>
                <span>▸</span>
                <span style="color: #1e293b; font-weight: 500;">{{ $stall->name }}</span>
            </div>
        </div>
    </div>

    <!-- 🔥 REDUCED HEIGHT Hero Section -->
    <section style="padding: 0 0 32px; background: #f8fafc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div
                style="position: relative; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
                        background: linear-gradient(135deg, rgba(50, 50, 70, 0.95) 0%, rgba(70, 70, 90, 0.95) 100%);">

                <!-- Background Image -->
                @if ($stall->image)
                    <div
                        style="position: absolute; inset: 0; background-image: url('{{ asset('storage/' . $stall->image) }}'); 
                                background-size: cover; background-position: center; opacity: 0.3;">
                    </div>
                @else
                    <!-- Decorative background -->
                    <div style="position: absolute; inset: 0; opacity: 0.15;">
                        <div
                            style="position: absolute; right: 15%; top: 20%; width: 120px; height: 120px; border-radius: 50%; 
                                    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);">
                        </div>
                        <div
                            style="position: absolute; left: 20%; top: 30%; width: 80px; height: 80px; border-radius: 50%; 
                                    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);">
                        </div>
                        <div
                            style="position: absolute; right: 35%; bottom: 25%; width: 100px; height: 100px; border-radius: 50%; 
                                    background: radial-gradient(circle, rgba(255,255,255,0.25) 0%, transparent 70%);">
                        </div>
                    </div>
                @endif

                <!-- Content - REDUCED PADDING -->
                <div
                    style="position: relative; display: flex; align-items: center; justify-content: space-between; 
                            padding: 36px 40px; gap: 40px; flex-wrap: wrap;">

                    <!-- Left Section - Logo and Title -->
                    <div style="display: flex; align-items: center; gap: 20px; flex: 1; min-width: 300px;">
                        <!-- Logo - SLIGHTLY SMALLER -->
                        <div
                            style="width: 70px; height: 70px; border-radius: 50%; flex-shrink: 0;
                                    background: {{ $stall->logo ? 'white' : 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)' }}; 
                                    display: flex; align-items: center; justify-content: center; 
                                    box-shadow: 0 8px 20px rgba(0,0,0,0.2); overflow: hidden;">
                            @if ($stall->logo)
                                <img src="{{ asset('storage/' . $stall->logo) }}" alt="{{ $stall->name }}"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="text-align: center;">
                                    <div style="font-size: 13px; font-weight: 700; color: #1e293b; letter-spacing: 0.5px;">
                                        {{ strtoupper(substr($stall->name, 0, 4)) }}
                                    </div>
                                    <div style="width: 35px; height: 2px; background: #1e293b; margin: 3px auto;"></div>
                                    <div style="font-size: 9px; font-weight: 600; color: #1e293b; font-style: italic;">
                                        STALL
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Title and Subtitle -->
                        <div>
                            <h1
                                style="font-size: clamp(1.5rem, 3.5vw, 2rem); font-weight: 700; color: white; 
                                       margin: 0 0 6px 0; line-height: 1.2;">
                                {{ $stall->name }}
                            </h1>
                            <p style="font-size: 13px; color: rgba(255,255,255,0.85); margin: 0; line-height: 1.4;">
                                {{ $stall->description ?: 'Fresh & healthy food recipe' }}
                            </p>
                            <div style="margin-top: 10px;">
                                <span style="font-size: 11px; color: rgba(255,255,255,0.7);">
                                    {{ $stall->tenant ? 'Operated by:' : '' }}
                                </span>
                                <span style="font-size: 12px; color: #fbbf24; font-weight: 600; margin-left: 4px;">
                                    {{ $stall->tenant?->name ?? "Admin's Store" }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section - Stats - SMALLER -->
                    <div style="display: flex; align-items: center; gap: 32px; flex-wrap: wrap;">
                        <!-- Total Items -->
                        <div style="text-align: center;">
                            <div
                                style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 4px; line-height: 1;">
                                {{ str_pad($stall->products->count(), 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div style="font-size: 11px; color: rgba(255,255,255,0.75); font-weight: 500;">
                                Total Items
                            </div>
                        </div>

                        <!-- Categories -->
                        <div style="text-align: center;">
                            <div
                                style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 4px; line-height: 1;">
                                {{ str_pad($stall->products->filter(fn($p) => $p->category)->pluck('category_id')->unique()->count(), 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div style="font-size: 11px; color: rgba(255,255,255,0.75); font-weight: 500;">
                                Categories
                            </div>
                        </div>

                        <!-- Available Today -->
                        <div style="text-align: center;">
                            <div
                                style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 4px; line-height: 1;">
                                {{ str_pad($stall->products->where('is_available', true)->count(), 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div style="font-size: 11px; color: rgba(255,255,255,0.75); font-weight: 500;">
                                Available
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    @php
        // 🔥 Get categories from the stall's products
$uniqueCategories = $stall->products
    ->filter(fn($p) => $p->category)
    ->pluck('category.name')
            ->unique()
            ->sort()
            ->values();
    @endphp

    @if ($uniqueCategories->count() > 0)
        <section id="categories-section"
            style="padding: 24px 0; background: white; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: -12px;">
            <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h3 style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0;">Categories</h3>
                </div>
                <div
                    style="display: flex; gap: 12px; flex-wrap: nowrap; justify-content: center; align-items: center; overflow-x: auto; padding: 0 10px; scrollbar-width: none; -ms-overflow-style: none;">

                    <!-- All Items Button -->
                    <button class="filter-category active" data-category="all"
                        style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 25px; background: #f97316; color: white; border: none; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(249, 115, 22, 0.25); white-space: nowrap; flex-shrink: 0;">
                        <span style="font-size: 16px;">🍽️</span>
                        <span>All Items</span>
                    </button>

                    <!-- 🔥 DYNAMIC Categories from Database -->
                    @foreach ($uniqueCategories as $categoryName)
                        <button class="filter-category"
                            data-category="{{ strtolower(str_replace(' ', '-', $categoryName)) }}"
                            style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 25px; background: #f8fafc; color: #64748b; border: 2px solid #e2e8f0; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; white-space: nowrap; flex-shrink: 0;"
                            onmouseover="if(!this.classList.contains('active')) { this.style.borderColor='#f97316'; this.style.color='#f97316' }"
                            onmouseout="if(!this.classList.contains('active')) { this.style.borderColor='#e2e8f0'; this.style.color='#64748b' }">
                            <span style="font-size: 16px;">
                                @php
                                    $categoryLower = strtolower($categoryName);
                                @endphp
                                @if (str_contains($categoryLower, 'meal') || str_contains($categoryLower, 'rice'))
                                    🍱
                                @elseif(str_contains($categoryLower, 'beverage') || str_contains($categoryLower, 'drink'))
                                    🥤
                                @elseif(str_contains($categoryLower, 'sandwich'))
                                    🥪
                                @elseif(str_contains($categoryLower, 'dessert') || str_contains($categoryLower, 'sweet'))
                                    🍰
                                @elseif(str_contains($categoryLower, 'snack'))
                                    🍟
                                @else
                                    🍽️
                                @endif
                            </span>
                            <span>{{ $categoryName }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Menu Section -->
    <section id="menu" style="padding: 60px 0; background: #f8fafc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 48px;">
                <h2 style="font-size: 2.5rem; color: #1e293b; margin-bottom: 16px; font-weight: 800;">Our Menu</h2>
                <p style="color: #64748b; max-width: 600px; margin: 0 auto; font-size: 18px; line-height: 1.6;">
                    Freshly prepared dishes made with quality ingredients and served with love
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;"
                id="menuGrid">
                @forelse($stall->products->unique('id') as $product)
                    <div class="menu-item"
                        data-category="{{ optional($product->category)->name ? strtolower(str_replace(' ', '-', optional($product->category)->name)) : 'all' }}"
                        style="opacity: 1; transform: translateY(0); transition: all 0.3s ease;">
                        @livewire(
                            'add-to-cart-button',
                            [
                                'product' => $product,
                                'showPrice' => true,
                                'showQuantitySelector' => false,
                                'buttonText' => 'Add to Cart',
                                'buttonSize' => 'large',
                            ],
                            key('stall-product-' . $product->id)
                        )
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px;">
                        <div style="font-size: 64px; margin-bottom: 24px; opacity: 0.3;">🍽️</div>
                        <h3 style="color: #64748b; margin-bottom: 16px; font-size: 24px; font-weight: 600;">No menu items
                            available</h3>
                        <p style="color: #64748b;">This stall hasn't added any items to their menu yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section style="padding: 80px 0; background: white;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
                <!-- Left Content -->
                <div>
                    <h2 style="font-size: 2.5rem; color: #1e293b; margin-bottom: 24px; font-weight: 800;">
                        About {{ $stall->name }}
                    </h2>
                    <p style="color: #64748b; margin-bottom: 32px; line-height: 1.7; font-size: 16px;">
                        {{ $stall->description ?: 'We are dedicated to serving fresh, delicious food made with the finest ingredients and prepared with care.' }}
                    </p>

                    <div style="display: grid; gap: 24px;">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div
                                style="background: linear-gradient(135deg, #f97316, #ea580c); color: white; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                📍
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 4px; font-size: 16px;">Location
                                </h4>
                                <p style="color: #64748b; margin: 0; font-size: 15px;">
                                    {{ $stall->location ?: 'Main Canteen' }}</p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div
                                style="background: linear-gradient(135deg, #f97316, #ea580c); color: white; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                🕐
                            </div>
                            <div>
                                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 4px; font-size: 16px;">
                                    Operating Hours</h4>
                                <p style="color: #64748b; margin: 0; font-size: 15px;">Monday - Friday: 8:00 AM - 6:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Statistics Card -->
                <div>
                    <div
                        style="background: linear-gradient(135deg, #FFF5F5, #FFF0E6); border: 1px solid #FFE4CC; border-radius: 20px; padding: 40px; text-align: center;">
                        <h3 style="color: #1e293b; margin-bottom: 32px; font-size: 24px; font-weight: 700;">Stall Statistics
                        </h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; color: #f97316; font-weight: 800; margin-bottom: 8px;">
                                    {{ $stall->products->count() }}
                                </div>
                                <div style="color: #64748b; font-size: 14px; font-weight: 500;">Menu Items</div>
                            </div>

                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; color: #f97316; font-weight: 800; margin-bottom: 8px;">
                                    4.{{ rand(6, 9) }}
                                </div>
                                <div style="color: #64748b; font-size: 14px; font-weight: 500;">Average Rating</div>
                            </div>

                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; color: #f97316; font-weight: 800; margin-bottom: 8px;">
                                    {{ rand(50, 200) }}
                                </div>
                                <div style="color: #64748b; font-size: 14px; font-weight: 500;">Orders This Week</div>
                            </div>

                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; color: #f97316; font-weight: 800; margin-bottom: 8px;">
                                    {{ $stall->products->where('is_available', true)->count() }}
                                </div>
                                <div style="color: #64748b; font-size: 14px; font-weight: 500;">Available Today</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.filter-category');
            const menuItems = document.querySelectorAll('.menu-item');

            categoryButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Update active button
                    categoryButtons.forEach(btn => {
                        btn.querySelector('div').style.transform = 'scale(1)';
                        btn.querySelector('div').style.boxShadow = 'none';
                        btn.classList.remove('active');
                    });

                    this.querySelector('div').style.transform = 'scale(1.1)';
                    this.querySelector('div').style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
                    this.classList.add('active');

                    // Filter menu items
                    const selectedCategory = this.dataset.category;

                    menuItems.forEach(item => {
                        if (selectedCategory === 'all' || item.dataset.category ===
                            selectedCategory) {
                            item.style.display = 'block';
                            item.style.opacity = '0';
                            item.style.transform = 'translateY(20px)';

                            setTimeout(() => {
                                item.style.transition = 'all 0.3s ease';
                                item.style.opacity = '1';
                                item.style.transform = 'translateY(0)';
                            }, 50);
                        } else {
                            item.style.transition = 'all 0.3s ease';
                            item.style.opacity = '0';
                            item.style.transform = 'translateY(-20px)';

                            setTimeout(() => {
                                item.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });
        });
    </script>

    <style>
        @media (min-width: 768px) {
            .categories-section .container>div:last-child {
                grid-template-columns: repeat(6, 1fr) !important;
            }
        }

        .filter-category:hover div {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .categories-section {
                padding: 2rem 0 !important;
                margin-top: -32px !important;
            }

            .container>div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
                gap: 40px !important;
            }

            h1[style*="clamp"] {
                font-size: 1.5rem !important;
            }
        }

        @media (max-width: 480px) {
            .container>div[style*="grid-template-columns: repeat(auto-fill"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush
