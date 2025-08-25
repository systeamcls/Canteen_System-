@extends('layouts.canteen')

@section('title', 'Food Stalls - LTO Canteen Central')

@section('content')
<!-- Hero Section -->
<section style="padding: 60px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Our Food Stalls</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; opacity: 0.9;">Discover all the amazing food vendors at LTO Canteen Central. Each stall offers unique specialties and flavors.</p>

        <!-- Search Bar -->
        <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 15px; max-width: 600px; margin: 0 auto;">
            <form action="{{ route('stalls.index') }}" method="GET" style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search stalls..." 
                       style="flex: 1; padding: 12px 15px; border: none; border-radius: 8px; font-size: 1rem;">
                
                <button type="submit" style="background: var(--secondary); color: white; padding: 12px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Search
                </button>

                @if(request('search'))
                <a href="{{ route('stalls.index') }}" style="background: rgba(255,255,255,0.3); color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>
</section>

<!-- Stalls Section -->
<section style="padding: 60px 0; background: var(--light);">
    <div class="container">
        <!-- Results Info -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap;">
            <div>
                <h2 style="color: var(--primary); font-size: 1.5rem; margin-bottom: 5px;">
                    @if(request('search'))
                        Search Results for "{{ request('search') }}"
                    @else
                        All Active Stalls
                    @endif
                </h2>
                <p style="color: var(--gray);">{{ $stalls->count() }} stalls available</p>
            </div>

            <!-- User Status -->
            <div style="background: white; padding: 10px 20px; border-radius: 25px; border: 2px solid var(--primary-lighter);">
                @if(session('user_type') === 'guest')
                    <span style="color: var(--primary);">👤 Browsing as Guest</span>
                @else
                    <span style="color: var(--success);">👨‍💼 Employee Access</span>
                @endif
            </div>
        </div>

        <!-- Stalls Grid -->
        @if($stalls->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
                @foreach($stalls as $stall)
                <div style="background: white; border-radius: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.3s;"
                     onmouseover="this.style.transform='translateY(-10px)'" 
                     onmouseout="this.style.transform='translateY(0)'">
                    
                    <!-- Stall Header Image -->
                    <div style="height: 220px; background: linear-gradient(135deg, var(--primary-lighter) 0%, var(--primary-light) 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                        <span style="font-size: 5rem; margin-bottom: 10px;">🏪</span>
                        
                        <!-- Status Badge -->
                        <div style="position: absolute; top: 15px; right: 15px; background: var(--success); color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                            Active
                        </div>
                    </div>

                    <!-- Stall Info -->
                    <div style="padding: 25px;">
                        <!-- Stall Name -->
                        <h3 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 10px; font-weight: 700;">
                            {{ $stall->name }}
                        </h3>

                        <!-- Description -->
                        <p style="color: var(--gray); line-height: 1.6; margin-bottom: 20px;">
                            {{ $stall->description }}
                        </p>

                        <!-- Location -->
                        <div style="display: flex; align-items: center; margin-bottom: 20px; color: var(--gray);">
                            <span style="margin-right: 8px;">📍</span>
                            <span>{{ $stall->location }}</span>
                        </div>

                        <!-- Product Count -->
                        <div style="display: flex; align-items: center; margin-bottom: 25px; color: var(--gray);">
                            <span style="margin-right: 8px;">🍽️</span>
                            <span>{{ $stall->products->where('is_available', true)->count() }} available items</span>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 15px;">
                            <a href="{{ route('stalls.show', $stall) }}" 
                               style="flex: 1; background: var(--primary); color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; transition: background 0.3s;">
                                View Menu
                            </a>
                            
                            <a href="{{ route('menu.index', ['stall' => $stall->id]) }}" 
                               style="background: rgba(46, 91, 186, 0.1); color: var(--primary); padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; border: 2px solid var(--primary-lighter);">
                                Quick Order
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- No Results -->
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;">🏪</div>
                <h3 style="color: var(--primary); margin-bottom: 15px; font-size: 1.5rem;">No stalls found</h3>
                <p style="color: var(--gray); margin-bottom: 30px;">
                    @if(request('search'))
                        No stalls match your search criteria. Try a different search term.
                    @else
                        No active stalls are currently available.
                    @endif
                </p>
                @if(request('search'))
                <a href="{{ route('stalls.index') }}" style="background: var(--primary); color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    View All Stalls
                </a>
                @endif
            </div>
        @endif
    </div>
</section>

<!-- Quick Stats Section -->
<section style="padding: 60px 0; background: white;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;">Stall Statistics</h2>
            <p style="color: var(--gray);">Overview of our food stall ecosystem</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
            <div style="text-align: center; padding: 30px; background: var(--light); border-radius: 12px;">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">🏪</div>
                <h3 style="font-size: 2rem; color: var(--primary); margin-bottom: 5px; font-weight: 700;">
                    {{ App\Models\Stall::where('is_active', true)->count() }}
                </h3>
                <p style="color: var(--gray);">Active Stalls</p>
            </div>

            <div style="text-align: center; padding: 30px; background: var(--light); border-radius: 12px;">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">🍽️</div>
                <h3 style="font-size: 2rem; color: var(--primary); margin-bottom: 5px; font-weight: 700;">
                    {{ App\Models\Product::where('is_available', true)->count() }}
                </h3>
                <p style="color: var(--gray);">Total Menu Items</p>
            </div>

            <div style="text-align: center; padding: 30px; background: var(--light); border-radius: 12px;">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">🏷️</div>
                <h3 style="font-size: 2rem; color: var(--primary); margin-bottom: 5px; font-weight: 700;">
                    {{ App\Models\Stall::where('is_active', true)->count() }}
                </h3>
                <p style="color: var(--gray);">Food Categories</p>
            </div>

            <div style="text-align: center; padding: 30px; background: var(--light); border-radius: 12px;">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">💰</div>
                <h3 style="font-size: 2rem; color: var(--primary); margin-bottom: 5px; font-weight: 700;">
                    ₱{{ number_format(App\Models\Product::where('is_available', true)->avg('price'), 0) }}
                </h3>
                <p style="color: var(--gray);">Average Price</p>
            </div>
        </div>
    </div>
</section>
@endsection