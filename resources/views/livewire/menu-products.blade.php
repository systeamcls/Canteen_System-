<div>
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp

    <!-- Categories Section -->
    <section
        style="padding: 24px 0 16px; background: #fafbfc; position: sticky; top: 60px; z-index: 50; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

            <!-- Desktop Category Pills -->
            <div class="category-desktop"
                style="display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">

                <!-- All Items Button -->
                <button wire:click="selectCategory(null, 'All Menu Items')"
                    class="filter-category {{ !$selectedCategoryId ? 'active' : '' }}"
                    style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; cursor: pointer; border: none;
                      {{ !$selectedCategoryId ? 'background: #1e293b; color: white; border: 2px solid #1e293b; box-shadow: 0 4px 14px rgba(30, 41, 59, 0.3);' : 'background: white; color: #64748b; border: 2px solid #e2e8f0;' }}"
                    onmouseover="if (!this.classList.contains('active')) { this.style.background='#f1f5f9'; this.style.transform='translateY(-2px)'; }"
                    onmouseout="if (!this.classList.contains('active')) { this.style.background='white'; this.style.transform='translateY(0)'; }">
                    <span style="font-size: 20px;">🍽️</span>
                    All Items
                </button>

                <!-- Dynamic Categories from Database -->
                @foreach ($categories as $category)
                    @php
                        $isActive = $selectedCategoryId == $category->id;
                    @endphp

                    <button wire:click="selectCategory({{ $category->id }}, '{{ $category->name }}')"
                        class="filter-category {{ $isActive ? 'active' : '' }}"
                        style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 500; transition: all 0.3s ease; cursor: pointer; border: none;
                          {{ $isActive ? 'background: #1e293b; color: white; border: 2px solid #1e293b; box-shadow: 0 4px 14px rgba(30, 41, 59, 0.3);' : 'background: white; color: #64748b; border: 2px solid #e2e8f0;' }}"
                        onmouseover="if (!this.classList.contains('active')) { this.style.background='#f1f5f9'; this.style.transform='translateY(-2px)'; }"
                        onmouseout="if (!this.classList.contains('active')) { this.style.background='white'; this.style.transform='translateY(0)'; }">

                        @if ($category->image && file_exists(storage_path('app/public/' . $category->image)))
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;"
                                onerror="this.style.display='none';">
                        @else
                            @php
                                $emoji = match (strtolower($category->name)) {
                                    'fresh meals' => '🍱',
                                    'sandwiches' => '🥪',
                                    'beverages' => '🥤',
                                    'snacks' => '🍿',
                                    'desserts' => '🍰',
                                    'boxed meals' => '📦',
                                    default => '🍽️',
                                };
                            @endphp
                            <span style="font-size: 20px;">{{ $emoji }}</span>
                        @endif

                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Mobile Category Dropdown -->
            <div class="category-mobile" style="display: none;">
                <select wire:model="selectedCategoryId"
                    wire:change="$set('selectedCategoryName', $event.target.options[$event.target.selectedIndex].text)"
                    style="width: 100%; padding: 12px 20px; border-radius: 12px; border: 2px solid #e2e8f0; background: white; font-weight: 500; cursor: pointer;">
                    <option value="">🍽️ All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <!-- Section Title -->
    <section style="padding: 16px 0 0; background: #fafbfc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: left; margin-bottom: 24px;">
                <h2
                    style="color: #1e293b; font-size: 2rem; font-weight: 700; margin-bottom: 8px; font-family: system-ui, -apple-system, sans-serif;">
                    <span>{{ $selectedCategoryName }}</span>
                </h2>
                <p style="color: #64748b; font-size: 1.125rem; margin: 0;">
                    <span>{{ $products->total() }}</span> items available
                </p>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section style="padding: 0 0 80px; background: #fafbfc;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div wire:loading.class="loading-opacity" wire:target="selectCategory,gotoPage">
                @if ($products->count() > 0)
                    <div id="menuGrid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        @foreach ($products as $product)
                            <div class="menu-item"
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
                                    key('menu-product-' . $product->id)
                                )
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 60px;">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- No Results -->
                    <div
                        style="text-align: center; padding: 80px 32px; background: white; border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.08);">
                        <div style="font-size: 4rem; margin-bottom: 24px; opacity: 0.3;">🍽️</div>
                        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 1.5rem; font-weight: 600;">
                            No {{ $selectedCategoryName }} available
                        </h3>
                        <p style="color: #64748b; margin-bottom: 32px; font-size: 1.125rem;">
                            Check back later or browse other categories.
                        </p>
                        <button wire:click="selectCategory(null, 'All Menu Items')"
                            style="display: inline-block; background: #FF6B35; color: white; padding: 16px 32px; border-radius: 16px; border: none; font-weight: 600; box-shadow: 0 4px 14px rgba(255, 107, 53, 0.3); transition: all 0.3s ease; cursor: pointer;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(255, 107, 53, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(255, 107, 53, 0.3)'">
                            View All Items
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <style>
        .loading-opacity {
            opacity: 0.6;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 767px) {
            .category-desktop {
                display: none !important;
            }

            .category-mobile {
                display: block !important;
            }
        }

        @media (min-width: 768px) {
            #menuGrid {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 20px !important;
            }
        }

        @media (min-width: 1025px) {
            #menuGrid {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 24px !important;
            }
        }
    </style>
</div>
