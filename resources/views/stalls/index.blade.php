@extends('layouts.canteen')

@section('title', 'Stalls - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 60px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Our Food Stalls</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.9;">Discover all the amazing food vendors at LTO Canteen Central. Each stall offers unique specialties and flavors.</p>

        <!-- Search Bar -->
        <div class="search-bar">
            <form action="{{ route('stalls.index') }}" method="GET">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="text" name="search" placeholder="Search stalls by name, location, or cuisine..." class="search-input" value="{{ request('search') }}">
                <button type="submit" class="search-btn" aria-label="Search">🔍</button>
            </form>
        </div>
    </div>
</section>

<!-- Filters -->
<section style="padding: 30px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <form action="{{ route('stalls.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Stalls</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open Now</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="sort" class="filter-select" onchange="this.form.submit()">
                <option value="">Sort by</option>
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rating (High to Low)</option>
                <option value="products_count" {{ request('sort') === 'products_count' ? 'selected' : '' }}>Menu Items</option>
            </select>

            <a href="{{ route('stalls.index') }}" class="btn btn-secondary" style="margin-left: auto;">Clear Filters</a>
        </form>
    </div>
</section>

<!-- Stalls Grid -->
<section style="padding: 60px 0;">
    <div class="container">
        @if(request('search') || request('status') || request('sort'))
        <div style="margin-bottom: 30px;">
            <h2 style="color: var(--primary); margin-bottom: 10px;">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @elseif(request('status') === 'open')
                    Open Stalls
                @elseif(request('status') === 'closed')
                    Closed Stalls
                @else
                    All Stalls
                @endif
            </h2>
            <p style="color: var(--gray);">{{ $stalls->total() }} {{ Str::plural('stall', $stalls->total()) }} found</p>
        </div>
        @endif

        <div class="grid grid-3">
            @forelse($stalls as $stall)
            @php
                $currentTime = now();
                $openTime = \Carbon\Carbon::createFromFormat('H:i', $stall->opening_time ?? '08:00');
                $closeTime = \Carbon\Carbon::createFromFormat('H:i', $stall->closing_time ?? '18:00');
                $isCurrentlyOpen = $stall->is_active && $currentTime->format('H:i') >= $openTime->format('H:i') && $currentTime->format('H:i') <= $closeTime->format('H:i');
                $averageRating = $stall->reviews_avg_rating ?? 4.5;
                $reviewsCount = $stall->reviews_count ?? rand(20, 150);
            @endphp
            <div class="card stall-card" data-stall-id="{{ $stall->id }}">
                @if($stall->image)
                <div class="card-image">
                    <img src="{{ asset('storage/' . $stall->image) }}" alt="{{ $stall->name }}" loading="lazy">
                </div>
                @endif
                
                <div class="card-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <h3 class="card-title">{{ $stall->name }}</h3>
                        <span class="status {{ $isCurrentlyOpen ? 'status-open' : 'status-closed' }}">
                            {{ $isCurrentlyOpen ? 'Open' : 'Closed' }}
                        </span>
                    </div>

                    @if($stall->description)
                    <p class="card-text">{{ Str::limit($stall->description, 100) }}</p>
                    @endif

                    <div style="margin: 15px 0;">
                        <small style="color: var(--gray); display: flex; align-items: center; gap: 5px;">
                            📍 {{ $stall->location }}
                        </small>
                    </div>

                    <div class="rating" style="margin: 15px 0;">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating))
                                    ⭐
                                @elseif($i - 0.5 <= $averageRating)
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span style="font-size: 0.9rem; color: var(--gray);">({{ number_format($averageRating, 1) }})</span>
                        <span style="margin-left: 10px; font-size: 0.9rem; color: var(--gray);">{{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0; padding: 10px; background: #f8fafc; border-radius: 8px;">
                        <div>
                            <small style="color: var(--primary); font-weight: 600;">{{ $stall->products_count }} Menu {{ Str::plural('Item', $stall->products_count) }}</small>
                        </div>
                        @if($stall->rental_fee)
                        <div>
                            <small style="color: var(--gray);">₱{{ number_format($stall->rental_fee, 0) }}/month rent</small>
                        </div>
                        @endif
                    </div>

                    <!-- Popular Items Preview -->
                    @if($stall->products && $stall->products->count() > 0)
                    <div style="margin: 15px 0;">
                        <small style="color: var(--gray); font-weight: 600; margin-bottom: 8px; display: block;">Popular Items:</small>
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            @foreach($stall->products->take(3) as $product)
                            <span style="background: var(--primary-lighter); color: var(--primary); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">
                                {{ $product->name }}
                            </span>
                            @endforeach
                            @if($stall->products->count() > 3)
                            <span style="color: var(--gray); font-size: 0.8rem;">+{{ $stall->products->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Operating Hours -->
                    <div class="operating-hours {{ $isCurrentlyOpen ? 'open' : 'closed' }}">
                        <small class="hours-text">
                            @if(!$stall->is_active)
                                🕐 Temporarily Closed
                            @elseif($isCurrentlyOpen)
                                🕐 Open Now: {{ $openTime->format('g:i A') }} - {{ $closeTime->format('g:i A') }}
                            @elseif($currentTime->format('H:i') < $openTime->format('H:i'))
                                🕐 Opens at {{ $openTime->format('g:i A') }}
                            @else
                                🕐 Closed - Opens tomorrow at {{ $openTime->format('g:i A') }}
                            @endif
                        </small>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <a href="{{ route('stalls.show', $stall->id) }}" class="btn btn-primary" style="flex: 1;">View Menu</a>
                        @if($isCurrentlyOpen)
                        <a href="{{ route('menu.index', ['stall' => $stall->id]) }}" class="btn btn-secondary">Order Now</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;">🏪</div>
                <h3 style="color: var(--gray); margin-bottom: 10px;">No stalls found</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">
                    @if(request('search'))
                        No stalls match your search criteria. Try different keywords or check your spelling.
                    @else
                        Try adjusting your filters or check back later.
                    @endif
                </p>
                <a href="{{ route('stalls.index') }}" class="btn btn-primary">View All Stalls</a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($stalls->hasPages())
        <div style="margin-top: 40px; display: flex; justify-content: center;">
            {{ $stalls->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</section>

<!-- Statistics Section -->
<section style="padding: 60px 0; background-color: #eff6ff;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Why Choose Our Stalls?</h2>
            <p style="color: var(--gray); max-width: 700px; margin: 0 auto;">Our food stalls offer the best variety and quality</p>
        </div>

        <div class="grid grid-4">
            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">🏪</div>
                <h3 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">{{ $stalls->total() }}</h3>
                <p style="color: var(--gray);">Total Food {{ Str::plural('Stall', $stalls->total()) }}</p>
            </div>

            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">🍽️</div>
                <h3 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">{{ number_format($stalls->sum('products_count')) }}+</h3>
                <p style="color: var(--gray);">Menu Items Available</p>
            </div>

            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">⭐</div>
                <h3 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">{{ number_format($stalls->avg('reviews_avg_rating') ?? 4.8, 1) }}</h3>
                <p style="color: var(--gray);">Average Rating</p>
            </div>

            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">🚀</div>
                <h3 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">{{ $stalls->avg('average_prep_time') ?? 15 }}min</h3>
                <p style="color: var(--gray);">Average Prep Time</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .search-bar {
        max-width: 600px;
        margin: 0 auto;
        display: flex;
        border-radius: 50px;
        overflow: hidden;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .search-input {
        flex: 1;
        padding: 15px 20px;
        border: none;
        outline: none;
        font-size: 1rem;
    }
    
    .search-btn {
        padding: 15px 20px;
        background: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .search-btn:hover {
        background: var(--primary-dark);
    }
    
    .filter-select {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: white;
        font-size: 0.9rem;
        cursor: pointer;
    }
    
    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
    }
    
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        background: white;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .card-image {
        height: 180px;
        overflow: hidden;
    }
    
    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .card:hover .card-image img {
        transform: scale(1.05);
    }
    
    .card-content {
        padding: 20px;
    }
    
    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        line-height: 1.3;
    }
    
    .card-text {
        color: var(--gray);
        line-height: 1.5;
        margin: 0;
    }
    
    .status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-open {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-closed {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        text-align: center;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        display: inline-block;
        font-size: 0.9rem;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }
    
    .btn-secondary:hover {
        background: var(--primary);
        color: white;
    }
    
    .grid {
        display: grid;
        gap: 30px;
    }
    
    .grid-3 {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    }
    
    .grid-4 {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .pagination {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: center;
    }
    
    .pagination a, .pagination span {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        color: var(--primary);
        border: 1px solid var(--primary-lighter);
        transition: all 0.2s;
        min-width: 40px;
        text-align: center;
    }
    
    .pagination a:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-1px);
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
    
    .operating-hours {
        margin: 15px 0;
        padding: 8px;
        border-radius: 6px;
    }
    
    .operating-hours.open {
        background: #d1fae5;
    }
    
    .operating-hours.closed {
        background: #fee2e2;
    }
    
    .operating-hours.open .hours-text {
        color: #065f46;
        font-weight: 500;
    }
    
    .operating-hours.closed .hours-text {
        color: #991b1b;
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .grid-3 {
            grid-template-columns: 1fr;
        }
        
        .grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .search-bar {
            margin: 0 20px;
        }
        
        .pagination {
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 6px 12px;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .grid-4 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit search on input change with debounce
    let searchTimeout;
    document.querySelector('.search-input')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
    
    // Loading state for forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳';
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '🔍';
                }, 2000);
            }
        });
    });
</script>
@endpush