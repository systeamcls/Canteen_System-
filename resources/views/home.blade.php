@extends('layouts.canteen')

@section('title', 'Home - LTO Canteen Central')

@section('content')
    <!-- Hero Section -->
    <section style="padding: 60px 0; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); text-align: center;">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 20px; color: var(--primary);">Welcome to LTO Canteen Central</h1>
            <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; color: var(--gray);">Discover delicious food
                from our trusted stalls. Fresh meals, quick service, and delicious options for everyone - visitors and LTO
                employees alike.</p>

            <!-- Search Bar -->
            <div class="search-bar" style="margin-bottom: 50px;">
                <form action="/search" method="GET">
                    <input type="text" name="q" placeholder="Search for food items..." class="search-input"
                        value="{{ request('q') }}">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>

            <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 50px; flex-wrap: wrap;">
                <a href="/menu" class="btn btn-primary" style="font-size: 1.1rem;">🛒 Browse Menu</a>
                <a href="/stalls" class="btn btn-secondary" style="font-size: 1.1rem;">🏪 View Stalls</a>
            </div>
        </div>
    </section>

    <!-- Top Foods Today -->
    <section style="padding: 60px 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Top Foods Today</h2>
                <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Most popular items ordered by our customers
                </p>
            </div>

            <div class="grid grid-4">
                @forelse($topFoods as $food)
                    <div class="card">
                        <img src="{{ $food->image ? asset('storage/' . $food->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}"
                            alt="{{ $food->name }}" class="card-img">
                        <div class="card-content">
                            <h3 class="card-title">{{ $food->name }}</h3>
                            <p class="card-text">{{ Str::limit($food->description, 60) }}</p>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <span class="price">₱{{ number_format($food->price, 2) }}</span>
                                <div class="rating">
                                    <span class="stars">⭐⭐⭐⭐⭐</span>
                                    <span style="font-size: 0.9rem; color: var(--gray);">(4.8)</span>
                                </div>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <small style="color: var(--gray);">{{ $food->stall->name }}</small>
                                <small style="color: var(--primary); font-weight: 600;">{{ rand(15, 45) }} orders
                                    today</small>
                            </div>
                            <button class="btn btn-primary add-to-cart-btn" style="width: 100%;"
                                data-product-id="{{ $food->id }}"
                                data-product-name="{{ htmlspecialchars($food->name, ENT_QUOTES) }}"
                                data-product-price="{{ $food->price }}"
                                data-product-image="{{ $food->image ? htmlspecialchars($food->image, ENT_QUOTES) : '' }}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p style="color: var(--gray);">No featured foods available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Popular Stalls -->
    <section style="padding: 60px 0; background-color: #eff6ff;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Popular Stalls</h2>
                <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Our most loved food stalls</p>
            </div>

            <div class="grid grid-3">
                @forelse($popularStalls as $stall)
                    <div class="card">
                        <div class="card-content">
                            <div style="position: relative;">
                                {{-- Image / Cover --}}
                                <img src="{{ $stall->image_url }}" alt="{{ $stall->name }}"
                                    style="width:100%; height:180px; object-fit:cover; border-radius: 12px;"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($stall->name) }}&size=800&background=FF6B35&color=fff&font-size=0.33&bold=true'">
                                {{-- Real Time badge --}}
                                @if ($stall->opening_time && $stall->closing_time)
                                    <span
                                        style="position:absolute;top:16px;left:16px;background:#3b82f6;color:white;padding:5px 16px;border-radius:8px;font-size:0.8rem;font-weight:600;">
                                        {{ \Carbon\Carbon::parse($stall->opening_time)->format('g:iA') }} -
                                        {{ \Carbon\Carbon::parse($stall->closing_time)->format('g:iA') }}
                                    </span>
                                @else
                                    <span
                                        style="position:absolute;top:16px;left:16px;background:#3b82f6;color:white;padding:5px 16px;border-radius:8px;font-size:0.8rem;font-weight:600;">
                                        24H
                                    </span>
                                @endif
                                {{-- Open/Closed status --}}
                                <span
                                    style="position:absolute;top:16px;right:16px;background:{{ $stall->is_active ? '#10b981' : '#ef4444' }};color:white;padding:5px 14px;border-radius:8px;font-size:0.8rem;font-weight:600;">
                                    {{ $stall->is_active ? 'Open' : 'Closed' }}
                                </span>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <h3 class="card-title" style="font-size:1.2rem; margin-top: 15px;">{{ $stall->name }}</h3>
                            </div>
                            <p class="card-text" style="color:#64748b;">{{ Str::limit($stall->description, 80) }}</p>

                            <div style="margin: 14px 0 0;">
                                <small style="color: var(--gray);">
                                    📍 {{ $stall->location }}
                                </small>
                            </div>

                            <div style="margin: 14px 0;">
                                <small style="color: var(--primary); font-weight: 600;">
                                    {{ $stall->products->where('is_available', true)->count() }} items available
                                </small>
                            </div>

                            {{-- Popular Items (real data) --}}
                            <div style="margin: 14px 0;">
                                <small style="color:#475569;font-weight:600;">Popular Items:</small>
                                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                    @php
                                        $popularProducts = $stall
                                            ->products()
                                            ->where('is_available', true)
                                            ->orderBy('price', 'desc')
                                            ->take(3)
                                            ->get();
                                    @endphp
                                    @forelse($popularProducts as $product)
                                        <span
                                            style="background:#f1f5f9;color:#334155;padding:4px 12px;border-radius:4px;font-size:0.8rem;">
                                            {{ $product->name }}
                                        </span>
                                    @empty
                                        <span
                                            style="background:#f1f5f9;color:#94a3b8;padding:4px 12px;border-radius:4px;font-size:0.8rem;">
                                            No items yet
                                        </span>
                                    @endforelse
                                </div>
                            </div>

                            <a href="{{ route('stalls.show', $stall) }}" class="btn btn-primary"
                                style="width: 100%;background:linear-gradient(90deg,#fc7902,#fbbc05);color:white;font-weight:700;padding:12px 0;margin-top:12px;border-radius:8px;">
                                View Menu
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p style="color: var(--gray);">No stalls available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Featured Items -->
    <section style="padding: 60px 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Featured Items</h2>
                <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Hand-picked specialties from our vendors
                </p>
            </div>

            <div class="grid grid-3">
                @forelse($featuredItems as $item)
                    <div class="card">
                        <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}"
                            alt="{{ $item->name }}" class="card-img">
                        <div class="card-content">
                            <h3 class="card-title">{{ $item->name }}</h3>
                            <p class="card-text">{{ Str::limit($item->description, 60) }}</p>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <span class="price">₱{{ number_format($item->price, 2) }}</span>
                                <small style="color: var(--gray);">{{ $item->stall->name }}</small>
                            </div>
                            <button class="btn btn-primary add-to-cart-btn" style="width: 100%;"
                                data-product-id="{{ $item->id }}"
                                data-product-name="{{ htmlspecialchars($item->name, ENT_QUOTES) }}"
                                data-product-price="{{ $item->price }}"
                                data-product-image="{{ $item->image ? htmlspecialchars($item->image, ENT_QUOTES) : '' }}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p style="color: var(--gray);">No featured items available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section
        style="padding: 80px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Ready to Satisfy Your Cravings?</h2>
            <p style="max-width: 700px; margin: 0 auto 40px; font-size: 1.1rem; opacity: 0.9;">Join LTO visitors and
                employees who've discovered the easiest way to order delicious food</p>
            <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <a href="/menu" class="btn btn-secondary">Order Now</a>
                <a href="/stalls" class="btn"
                    style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">View
                    All Stalls</a>
            </div>
            <div style="margin-top: 30px; color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                ✓ No delivery fees within LTO ✓ Affordable pricing for all ✓ Quick 10-15 min pickup
            </div>
        </div>
    </section>
@endsection

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
