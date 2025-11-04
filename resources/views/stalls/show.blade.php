@extends('layouts.canteen')

@section('title', $stall->name . ' - LTO Canteen Central')

@section('content')
    <!-- Toned Down Stall Header -->
    <section style="padding: 40px 0; background: #f8fafc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div
                style="background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%); border-radius: 24px; padding: 48px; color: white; position: relative; overflow: hidden; box-shadow: 0 20px 40px rgba(220, 38, 38, 0.12);">
                <!-- Content -->
                <div style="position: relative; z-index: 2; max-width: 900px;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px; flex-wrap: wrap; gap: 20px;">
                        <h1
                            style="font-size: clamp(2.5rem, 5vw, 4rem); margin: 0; font-weight: 800; line-height: 1.1; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">
                            {{ $stall->name }}
                        </h1>
                        <span
                            style="background: {{ $stall->is_active ? 'rgba(34, 197, 94, 0.9)' : 'rgba(107, 114, 128, 0.9)' }}; color: white; padding: 12px 24px; border-radius: 25px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                            {{ $stall->is_active ? 'Open Now' : 'Closed' }}
                        </span>
                    </div>

                    <p style="font-size: 1.25rem; margin-bottom: 32px; opacity: 0.95; line-height: 1.6; max-width: 600px;">
                        {{ $stall->description ?: 'Delicious food made with love and quality ingredients.' }}
                    </p>

                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 20px;">📍</span>
                            </div>
                            <span style="font-weight: 500;">{{ $stall->location ?: 'Main Canteen' }}</span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 20px;">⭐</span>
                            </div>
                            <span style="font-weight: 500;">4.{{ rand(6, 9) }} ({{ rand(20, 150) }} reviews)</span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 20px;">👥</span>
                            </div>
                            <span style="font-weight: 500;">{{ $stall->products->count() }} menu items</span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2);">
                                <span style="font-size: 20px;">🕐</span>
                            </div>
                            <span
                                style="font-weight: 500;">{{ $stall->is_active ? '8:00 AM - 6:00 PM' : 'Closed for today' }}</span>
                        </div>
                    </div>

                    @if ($stall->is_active)
                        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                            <a href="#menu"
                                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); color: white; padding: 16px 32px; border-radius: 25px; text-decoration: none; font-weight: 600; border: 2px solid rgba(255,255,255,0.2); transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"
                                onmouseover="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(-2px)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
                                View Menu
                            </a>
                            <a href="/menu?stall={{ $stall->id }}"
                                style="background: white; color: #f97316; padding: 16px 32px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'">
                                Order Now
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Subtle Background Pattern -->
                <div
                    style="position: absolute; top: -50%; right: -50%; width: 100%; height: 100%; background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px); background-size: 40px 40px; opacity: 0.4;">
                </div>
            </div>
        </div>
    </section>

    <!-- Improved Categories Section with Better Spacing -->
    @php
        // Get unique categories from products - FIXED: Remove duplicates properly
        $uniqueCategories = $stall->products
            ->whereNotNull('category_id')
            ->pluck('category.name')
            ->filter()
            ->unique()
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
                    <button class="filter-category active" data-category="all"
                        style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 25px; background: #f97316; color: white; border: none; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(249, 115, 22, 0.25); white-space: nowrap; flex-shrink: 0;">
                        <span style="font-size: 16px;">🍽️</span>
                        <span>All Items</span>
                    </button>

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

    <!-- Modern Menu Section -->
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
                        data-category="{{ $product->category ? strtolower(str_replace(' ', '-', $product->category->name)) : 'uncategorized' }}"
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

    <!-- Modern About Section -->
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
                        <h3 style="color: #1e293b; margin-bottom: 32px; font-size: 24px; font-weight: 700;">Stall
                            Statistics</h3>

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
                        btn.style.background = '#f8fafc';
                        btn.style.color = '#64748b';
                        btn.style.borderColor = '#e2e8f0';
                        btn.style.boxShadow = 'none';
                        btn.classList.remove('active');
                    });

                    this.style.background = '#f97316';
                    this.style.color = 'white';
                    this.style.borderColor = '#f97316';
                    this.style.boxShadow = '0 2px 8px rgba(249, 115, 22, 0.25)';
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
        /* Hide scrollbar for category buttons */
        div[style*="overflow-x: auto"]::-webkit-scrollbar {
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #categories-section {
                padding: 20px 0 !important;
                margin-top: -8px !important;
            }

            .container>div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
                gap: 40px !important;
            }

            h1[style*="clamp"] {
                font-size: 2.5rem !important;
            }
        }

        @media (max-width: 480px) {
            .container>div[style*="grid-template-columns: repeat(auto-fill"] {
                grid-template-columns: 1fr !important;
            }

            #categories-section div[style*="flex-wrap: nowrap"] {
                justify-content: flex-start !important;
            }
        }
    </style>
@endpush
