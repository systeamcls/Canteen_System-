<div>
    <!-- Categories Section -->
    <section
        style="padding: 24px 0 16px; background: #fafbfc; position: sticky; top: 60px; z-index: 50; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

            <!-- Desktop Category Pills -->
            <div class="category-desktop"
                style="display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">

                <!-- All Items Button -->
                <button wire:click="selectCategory(null)" type="button"
                    class="{{ !$selectedCategoryId ? 'cat-active' : 'cat-inactive' }}"
                    style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; font-weight: 500; transition: all 0.2s; cursor: pointer; border: 2px solid;">
                    <span style="font-size: 20px;">🍽️</span>
                    All Items
                </button>

                <!-- Category Buttons -->
                @foreach ($categories as $category)
                    <button wire:click="selectCategory({{ $category->id }})" type="button"
                        class="{{ $selectedCategoryId == $category->id ? 'cat-active' : 'cat-inactive' }}"
                        style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; font-weight: 500; transition: all 0.2s; cursor: pointer; border: 2px solid;">

                        @if ($category->image && file_exists(storage_path('app/public/' . $category->image)))
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                        @else
                            <span style="font-size: 20px;">
                                @switch(strtolower($category->name))
                                    @case('fresh meals')
                                        🍱
                                    @break

                                    @case('sandwiches')
                                        🥪
                                    @break

                                    @case('beverages')
                                        🥤
                                    @break

                                    @case('snacks')
                                        🍿
                                    @break

                                    @case('desserts')
                                        🍰
                                    @break

                                    @case('boxed meals')
                                        📦
                                    @break

                                    @default
                                        🍽️
                                @endswitch
                            </span>
                        @endif

                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Mobile Dropdown -->
            <div class="category-mobile" style="display: none;">
                <select wire:model.live="selectedCategoryId"
                    style="width: 100%; padding: 12px 20px; border-radius: 12px; border: 2px solid #e2e8f0; background: white; font-weight: 500; cursor: pointer;">
                    <option value="">🍽️ All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <!-- Title & Count -->
    <section style="padding: 16px 0 0; background: #fafbfc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: left; margin-bottom: 24px;">
                <h2 style="color: #1e293b; font-size: 2rem; font-weight: 700; margin-bottom: 8px;">
                    {{ $selectedCategoryName }}
                </h2>
                <p style="color: #64748b; font-size: 1.125rem;">
                    {{ $products->total() }} items available
                </p>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section style="padding: 0 0 80px; background: #fafbfc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

            <!-- Loading Overlay -->
            <div wire:loading wire:target="selectCategory"
                style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                <div style="text-align: center;">
                    <div
                        style="width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid #FF6B35; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 10px;">
                    </div>
                    <p style="color: #64748b; font-weight: 600;">Loading...</p>
                </div>
            </div>

            @if ($products->count() > 0)
                <div class="products-grid">
                    @foreach ($products as $product)
                        <div class="product-item">
                            @livewire(
                                'add-to-cart-button',
                                [
                                    'product' => $product,
                                    'showPrice' => true,
                                    'showQuantitySelector' => false,
                                    'buttonText' => 'Add',
                                    'buttonSize' => 'medium',
                                ],
                                key('menu-product-' . $product->id)
                            )
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($products->hasPages())
                    <div style="margin-top: 60px;">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div
                    style="text-align: center; padding: 80px 32px; background: white; border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
                    <div style="font-size: 4rem; margin-bottom: 24px; opacity: 0.3;">😔</div>
                    <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 1.5rem; font-weight: 600;">
                        No {{ $selectedCategoryName }} available
                    </h3>
                    <p style="color: #64748b; margin-bottom: 32px; font-size: 1.125rem;">
                        Try browsing other categories or check back later.
                    </p>
                    <button wire:click="selectCategory(null)" type="button"
                        style="background: #FF6B35; color: white; padding: 16px 32px; border-radius: 16px; border: none; font-weight: 600; box-shadow: 0 4px 14px rgba(255, 107, 53, 0.3); cursor: pointer;">
                        View All Items
                    </button>
                </div>
            @endif
        </div>
    </section>

    <style>
        /* Category Button States */
        .cat-active {
            background: #1e293b !important;
            color: white !important;
            border-color: #1e293b !important;
            box-shadow: 0 4px 14px rgba(30, 41, 59, 0.3) !important;
        }

        .cat-inactive {
            background: white !important;
            color: #64748b !important;
            border-color: #e2e8f0 !important;
        }

        .cat-inactive:hover {
            background: #f1f5f9 !important;
            transform: translateY(-2px);
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media (min-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
        }

        @media (min-width: 1025px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 24px;
            }
        }

        .product-item {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        /* Mobile/Desktop Toggle */
        @media (max-width: 767px) {
            .category-desktop {
                display: none !important;
            }

            .category-mobile {
                display: block !important;
            }
        }

        /* Loading Spinner */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</div>
