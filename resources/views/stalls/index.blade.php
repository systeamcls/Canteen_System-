@extends('layouts.canteen')

@section('title', 'Stalls - CuisiCourt')

@section('content')
<!-- Header Section -->
<section style="padding: 40px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <div style="text-align: left; margin-bottom: 30px;">
            <h1 style="font-size: 2.8rem; color: var(--dark); margin-bottom: 15px;">Our Food Stalls</h1>
            <p style="color: var(--gray); max-width: 600px; font-size: 1.1rem;">Explore delicious offerings from our vendors</p>
        </div>
        
        <!-- Search and Filter Bar -->
        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <form action="/stalls" method="GET" style="display: flex; gap: 15px; align-items: center; flex: 1;">
                <input type="text" name="search" placeholder="Search stalls or products..." 
                       style="flex: 1; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;" 
                       value="{{ $search }}">
                       
                <select name="status" style="padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; background: white; min-width: 150px;">
                    <option value="">All Categories</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open Now</option>
                    <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                
                <button type="submit" style="background: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer;">
                    🔍
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Stalls Grid -->
<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        @if($search || $status !== null)
        <div style="margin-bottom: 30px;">
            <h2 style="color: var(--dark); margin-bottom: 10px; font-size: 1.8rem;">
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
            <div class="card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <!-- Stall Image Placeholder -->
                <div style="height: 180px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); position: relative; display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center;">
                        <h3 style="font-size: 1.8rem; color: var(--primary); margin: 0 0 10px 0; font-weight: 700;">{{ $stall->name }}</h3>
                        <span style="background: {{ $stall->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $stall->is_active ? '#166534' : '#dc2626' }}; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                            {{ $stall->is_active ? 'Open' : 'Closed' }}
                        </span>
                    </div>
                </div>
                
                <div style="padding: 25px;">
                    <!-- Menu Items Preview -->
                    @if($stall->products->count() > 0)
                    <div style="margin-bottom: 20px;">
                        @foreach($stall->products->take(3) as $index => $product)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; {{ $index < 2 ? 'border-bottom: 1px solid #f1f5f9;' : '' }}">
                            <div>
                                <h4 style="margin: 0; font-size: 0.95rem; color: var(--dark);">{{ $product->name }}</h4>
                                <p style="margin: 0; font-size: 0.8rem; color: var(--gray);">{{ Str::limit($product->description, 30) }}</p>
                            </div>
                            <div style="text-align: right;">
                                <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->image }}')" 
                                        style="background: var(--primary); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem;">
                                    Add
                                </button>
                                <div style="font-size: 0.9rem; font-weight: 600; color: var(--primary); margin-top: 2px;">₱{{ number_format($product->price, 2) }}</div>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($stall->products->count() > 3)
                        <div style="text-align: center; margin-top: 10px;">
                            <small style="color: var(--gray);">+{{ $stall->products->count() - 3 }} more items</small>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Description -->
                    <p style="color: var(--gray); margin: 0 0 15px 0; font-size: 0.9rem; line-height: 1.4;">{{ Str::limit($stall->description, 100) }}</p>
                    
                    <!-- Location -->
                    <div style="margin: 15px 0;">
                        <small style="color: var(--gray); display: flex; align-items: center; gap: 5px;">
                            📍 {{ $stall->location }}
                        </small>
                    </div>
                    
                    <!-- Rating and Stats -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0; padding: 10px; background: #f8fafc; border-radius: 8px;">
                        <div style="display: flex; align-items: center;">
                            @for($i = 1; $i <= 5; $i++)
                                <span style="color: #fbbf24; font-size: 0.9rem;">{{ $i <= 4 ? '★' : '☆' }}</span>
                            @endfor
                            <span style="margin-left: 5px; font-size: 0.9rem; color: var(--gray);">4.{{ rand(6, 9) }}</span>
                        </div>
                        <div>
                            <small style="color: var(--primary); font-weight: 600;">{{ $stall->products_count }} items</small>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <a href="/stalls/{{ $stall->id }}" class="btn btn-primary" style="width: 100%; text-align: center; padding: 12px; font-weight: 600;">
                        View Full Menu
                    </a>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">🏪</div>
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