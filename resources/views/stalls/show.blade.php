@extends('layouts.canteen')

@section('title', $stall->name . ' - LTO Canteen Central')

@section('content')
<!-- Stall Header -->
<section style="padding: 60px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white;">
    <div class="container">
        <div style="max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h1 style="font-size: 3rem; margin: 0;">{{ $stall->name }}</h1>
                <span class="status {{ $stall->is_active ? 'status-open' : 'status-closed' }}" style="font-size: 1rem; padding: 8px 16px;">
                    {{ $stall->is_active ? 'Open Now' : 'Closed' }}
                </span>
            </div>
            
            <p style="font-size: 1.2rem; margin-bottom: 20px; opacity: 0.9;">{{ $stall->description }}</p>
            
            <div style="display: flex; gap: 30px; flex-wrap: wrap; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span>📍</span>
                    <span>{{ $stall->location }}</span>
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span>⭐</span>
                    <span>4.{{ rand(6, 9) }} ({{ rand(20, 150) }} reviews)</span>
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span>🍽️</span>
                    <span>{{ $stall->products->count() }} menu items</span>
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span>🕐</span>
                    <span>{{ $stall->is_active ? '8:00 AM - 6:00 PM' : 'Closed for today' }}</span>
                </div>
            </div>
            
            @if($stall->is_active)
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="#menu" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3);">View Menu</a>
                <a href="/menu?stall={{ $stall->id }}" class="btn btn-secondary">Order Now</a>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Menu Categories -->
@if($categories->count() > 0)
<section style="padding: 30px 0; background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="container">
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <span style="font-weight: 600; color: var(--dark);">Categories:</span>
            <a href="#menu" class="filter-category active" data-category="all" style="padding: 8px 16px; border-radius: 20px; background: var(--primary); color: white; text-decoration: none; font-size: 0.9rem;">All Items</a>
            @foreach($categories as $category)
            <a href="#menu" class="filter-category" data-category="{{ $category }}" style="padding: 8px 16px; border-radius: 20px; background: #f8fafc; color: var(--gray); text-decoration: none; font-size: 0.9rem; border: 1px solid #e5e7eb;">{{ ucfirst(str_replace('-', ' ', $category)) }}</a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Stall Menu -->
<section id="menu" style="padding: 60px 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">Our Menu</h2>
            <p style="color: var(--gray); max-width: 600px; margin: 0 auto;">Freshly prepared dishes made with quality ingredients</p>
        </div>
        
        <div class="grid grid-3" id="menuGrid">
            @forelse($stall->products as $product)
            <div class="card menu-item" data-category="{{ $product->category }}">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $product->name }}" class="card-img">
                <div class="card-content">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <h3 class="card-title">{{ $product->name }}</h3>
                        @if($product->category)
                        <span style="background: var(--primary-lighter); color: var(--primary); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                            {{ ucfirst(str_replace('-', ' ', $product->category)) }}
                        </span>
                        @endif
                    </div>
                    
                    <p class="card-text">{{ $product->description }}</p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0;">
                        <span class="price">₱{{ number_format($product->price, 2) }}</span>
                        <div class="rating">
                            <span class="stars">⭐⭐⭐⭐⭐</span>
                            <span style="font-size: 0.9rem; color: var(--gray);">(4.{{ rand(6, 9) }})</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <small style="color: var(--primary); font-weight: 600;">{{ rand(5, 25) }} orders today</small>
                        <small style="color: var(--gray);">⏱️ {{ rand(10, 20) }} min prep</small>
                    </div>
                    
                    @if($stall->is_active)
                    <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->image }}')" class="btn btn-primary" style="width: 100%;">Add to Cart</button>
                    @else
                    <button class="btn" style="width: 100%; background: #e5e7eb; color: var(--gray); cursor: not-allowed;" disabled>Stall Closed</button>
                    @endif
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                <h3 style="color: var(--gray); margin-bottom: 10px;">No menu items available</h3>
                <p style="color: var(--gray);">This stall hasn't added any items to their menu yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- About Stall -->
<section style="padding: 60px 0; background-color: #eff6ff;">
    <div class="container">
        <div class="grid grid-2" style="align-items: center;">
            <div>
                <h2 style="font-size: 2.2rem; color: var(--primary); margin-bottom: 20px;">About {{ $stall->name }}</h2>
                <p style="color: var(--gray); margin-bottom: 20px; line-height: 1.6;">{{ $stall->description }}</p>
                
                <div style="display: grid; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">📍</div>
                        <div>
                            <strong style="color: var(--dark);">Location</strong>
                            <p style="color: var(--gray); margin: 0;">{{ $stall->location }}</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">💰</div>
                        <div>
                            <strong style="color: var(--dark);">Monthly Rent</strong>
                            <p style="color: var(--gray); margin: 0;">₱{{ number_format($stall->rental_fee, 2) }}</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: var(--primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">🕐</div>
                        <div>
                            <strong style="color: var(--dark);">Operating Hours</strong>
                            <p style="color: var(--gray); margin: 0;">Monday - Friday: 8:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <div style="background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <h3 style="color: var(--primary); margin-bottom: 20px;">Stall Statistics</h3>
                    
                    <div style="display: grid; gap: 20px;">
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">{{ $stall->products->count() }}</div>
                            <div style="color: var(--gray); font-size: 0.9rem;">Menu Items</div>
                        </div>
                        
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">4.{{ rand(6, 9) }}</div>
                            <div style="color: var(--gray); font-size: 0.9rem;">Average Rating</div>
                        </div>
                        
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">{{ rand(50, 200) }}</div>
                            <div style="color: var(--gray); font-size: 0.9rem;">Orders This Week</div>
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
// Category filtering
document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('.filter-category');
    const menuItems = document.querySelectorAll('.menu-item');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active button
            categoryButtons.forEach(btn => {
                btn.style.background = '#f8fafc';
                btn.style.color = 'var(--gray)';
                btn.classList.remove('active');
            });
            
            this.style.background = 'var(--primary)';
            this.style.color = 'white';
            this.classList.add('active');
            
            // Filter menu items
            const selectedCategory = this.dataset.category;
            
            menuItems.forEach(item => {
                if (selectedCategory === 'all' || item.dataset.category === selectedCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush