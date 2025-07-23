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
            <form action="/stalls" method="GET">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" placeholder="Search stalls by name, location, or cuisine..." class="search-input" value="{{ $search }}">
                <button type="submit" class="search-btn">🔍</button>
            </form>
        </div>
    </div>
</section>

<!-- Filters -->
<section style="padding: 30px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <form action="/stalls" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <input type="hidden" name="search" value="{{ $search }}">
            
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Stalls</option>
                <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open Now</option>
                <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            
            <a href="/stalls" class="btn btn-secondary" style="margin-left: auto;">Clear Filters</a>
        </form>
    </div>
</section>

<!-- Stalls Grid -->
<section style="padding: 60px 0;">
    <div class="container">
        @if($search || $status !== null)
        <div style="margin-bottom: 30px;">
            <h2 style="color: var(--primary); margin-bottom: 10px;">
                @if($search)
                    Search Results for "{{ $search }}"
                @elseif($status === 'open')
                    Open Stalls
                @elseif($status === 'closed')
                    Closed Stalls
                @else
                    All Stalls
                @endif
            </h2>
            <p style="color: var(--gray);">{{ $stalls->total() }} stalls found</p>
        </div>
        @endif
        
        <div class="grid grid-3">
            @forelse($stalls as $stall)
            <div class="card">
                <div class="card-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <h3 class="card-title">{{ $stall->name }}</h3>
                        <span class="status {{ $stall->is_active ? 'status-open' : 'status-closed' }}">
                            {{ $stall->is_active ? 'Open' : 'Closed' }}
                        </span>
                    </div>
                    
                    <p class="card-text">{{ Str::limit($stall->description, 100) }}</p>
                    
                    <div style="margin: 15px 0;">
                        <small style="color: var(--gray); display: flex; align-items: center; gap: 5px;">
                            📍 {{ $stall->location }}
                        </small>
                    </div>
                    
                    <div class="rating" style="margin: 15px 0;">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span style="font-size: 0.9rem; color: var(--gray);">(4.{{ rand(6, 9) }})</span>
                        <span style="margin-left: 10px; font-size: 0.9rem; color: var(--gray);">{{ rand(20, 150) }} reviews</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0; padding: 10px; background: #f8fafc; border-radius: 8px;">
                        <div>
                            <small style="color: var(--primary); font-weight: 600;">{{ $stall->products_count }} Menu Items</small>
                        </div>
                        <div>
                            <small style="color: var(--gray);">₱{{ number_format($stall->rental_fee, 0) }}/month rent</small>
                        </div>
                    </div>
                    
                    <!-- Popular Items Preview -->
                    @if($stall->products->count() > 0)
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
                    <div style="margin: 15px 0; padding: 8px; background: {{ $stall->is_active ? '#d1fae5' : '#fee2e2' }}; border-radius: 6px;">
                        <small style="color: {{ $stall->is_active ? '#065f46' : '#991b1b' }}; font-weight: 500;">
                            {{ $stall->is_active ? '🕐 Open: 8:00 AM - 6:00 PM' : '🕐 Closed for today' }}
                        </small>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <a href="/stalls/{{ $stall->id }}" class="btn btn-primary" style="flex: 1;">View Menu</a>
                        @if($stall->is_active)
                        <a href="/menu?stall={{ $stall->id }}" class="btn btn-secondary">Order Now</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <h3 style="color: var(--gray); margin-bottom: 10px;">No stalls found</h3>
                <p style="color: var(--gray); margin-bottom: 20px;">Try adjusting your search or filters</p>
                <a href="/stalls" class="btn btn-primary">View All Stalls</a>
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
                <p style="color: var(--gray);">Total Food Stalls</p>
            </div>
            
            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">🍽️</div>
                <h3 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">{{ $stalls->sum('products_count') }}+</h3>
                <p style="color: var(--gray);">Menu Items Available</p>
            </div>
            
            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">⭐</div>
                <h3 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">4.8</h3>
                <p style="color: var(--gray);">Average Rating</p>
            </div>
            
            <div style="text-align: center; padding: 30px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">🚀</div>
                <h3 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">15min</h3>
                <p style="color: var(--gray);">Average Prep Time</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .pagination {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .pagination a, .pagination span {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        color: var(--primary);
        border: 1px solid var(--primary-lighter);
        transition: all 0.2s;
    }
    
    .pagination a:hover {
        background: var(--primary);
        color: white;
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
</style>
@endpush