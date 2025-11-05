@extends('layouts.canteen')

@section('title', 'Food Stalls - LTO Canteen Central')

@section('content')
    <style>
        /* Custom CSS Variables matching your React design */
        :root {
            --background: oklch(1 0 0);
            --foreground: oklch(0.298 0 0);
            --card: oklch(0.99 0.01 85.87);
            --card-foreground: oklch(0.298 0 0);
            --primary: oklch(0.646 0.222 41.116);
            --primary-foreground: oklch(1 0 0);
            --secondary: oklch(0.696 0.17 162.48);
            --secondary-foreground: oklch(1 0 0);
            --muted: oklch(0.98 0 0);
            --muted-foreground: oklch(0.556 0 0);
            --accent: oklch(0.696 0.17 162.48);
            --accent-foreground: oklch(1 0 0);
            --border: oklch(0.922 0 0);
            --input: oklch(0.98 0 0);
            --ring: oklch(0.646 0.222 41.116 / 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background);
            color: var(--foreground);
            line-height: 1.6;
        }

        .container {
            max-width: 1152px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            background: linear-gradient(135deg,
                    color-mix(in srgb, var(--primary) 10%, transparent),
                    color-mix(in srgb, var(--secondary) 5%, transparent),
                    color-mix(in srgb, var(--accent) 10%, transparent));
            padding: 3rem 0 5rem;
        }

        .hero-content {
            text-align: center;
            max-width: 1152px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 3.75rem);
            font-weight: 700;
            color: var(--foreground);
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        .hero-title .text-primary {
            color: var(--primary);
            display: block;
        }

        .hero-description {
            font-size: clamp(1.125rem, 2vw, 1.25rem);
            color: var(--muted-foreground);
            max-width: 32rem;
            margin: 0 auto 1.5rem;
            line-height: 1.5;
        }

        /* Search Section */
        .search-container {
            max-width: 32rem;
            margin: 0 auto 1.5rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1.125rem;
            border: 2px solid transparent;
            border-radius: 0.75rem;
            background: color-mix(in srgb, var(--card) 80%, transparent);
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: color-mix(in srgb, var(--primary) 50%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 10%, transparent);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted-foreground);
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Category Filters */
        .category-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }

        .category-btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .category-btn:hover {
            transform: scale(1.05);
        }

        .category-btn.active {
            background-color: var(--foreground);
            color: var(--background);
        }

        .category-btn:not(.active) {
            background-color: transparent;
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .category-btn:not(.active):hover {
            background-color: var(--foreground);
            color: var(--background);
        }

        /* Statistics Section */
        .stats-section {
            padding: 3rem 0;
            background: color-mix(in srgb, var(--card) 30%, transparent);
        }

        .stats-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .stats-title {
            font-size: clamp(1.5rem, 3vw, 1.875rem);
            font-weight: 700;
            color: var(--foreground);
            margin-bottom: 0.5rem;
        }

        .stats-description {
            color: var(--muted-foreground);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
            }
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: color-mix(in srgb, var(--card) 80%, transparent);
            backdrop-filter: blur(4px);
            border-radius: 0.5rem;
            border: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-0.25rem);
        }

        .stat-icon {
            display: inline-flex;
            padding: 0.75rem;
            border-radius: 50%;
            background: color-mix(in srgb, var(--primary) 10%, transparent);
            color: var(--primary);
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: clamp(1.5rem, 3vw, 1.875rem);
            font-weight: 700;
            color: var(--foreground);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--muted-foreground);
            font-weight: 500;
        }

        /* Stalls Section */
        .stalls-section {
            padding: 3rem 0;
        }

        .stalls-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 640px) {
            .stalls-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .stalls-title {
            font-size: clamp(1.5rem, 3vw, 1.875rem);
            font-weight: 700;
            color: var(--foreground);
        }

        .stalls-count {
            color: var(--muted-foreground);
            margin-top: 0.25rem;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            border: 1px solid var(--border);
            background: transparent;
        }

        /* Stalls Grid */
        .stalls-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .stalls-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .stalls-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .stall-card {
            background: var(--card);
            border: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
            group: true;
        }

        .stall-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-0.5rem);
        }

        .stall-image {
            position: relative;
            width: 100%;
            height: 12rem;
            overflow: hidden;
        }

        .stall-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .stall-card:hover .stall-image img {
            transform: scale(1.05);
        }

        .time-badge {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: #10b981;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: var(--primary);
            color: var(--primary-foreground);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .stall-content {
            padding: 1.5rem;
        }

        .stall-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--foreground);
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        .stall-card:hover .stall-name {
            color: var(--primary);
        }

        .stall-description {
            font-size: 0.875rem;
            color: var(--muted-foreground);
            margin-bottom: 1rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .stall-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            margin-bottom: 1rem;
        }

        .stall-tag {
            font-size: 0.75rem;
            padding: 0.125rem 0.5rem;
            border: 1px solid var(--border);
            border-radius: 0.25rem;
            background: transparent;
            color: var(--foreground);
        }

        .stall-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.875rem;
            color: var(--muted-foreground);
            margin-bottom: 1rem;
        }

        .meta-location {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .meta-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .star-icon {
            color: #fbbf24;
            fill: #fbbf24;
        }

        .stall-info {
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .stall-info .text-muted-foreground {
            color: var(--muted-foreground);
        }

        .stall-info .text-primary {
            color: var(--primary);
            font-weight: 600;
        }

        .stall-actions {
            display: flex;
            gap: 0.5rem;
            padding-top: 0.5rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 0.875rem;
            display: block;
            /* Make full width work */
            width: 100%;
            /* Ensure full width */
        }

        .btn-primary {
            background: var(--primary);
            color: var(--primary-foreground);
        }

        .btn-primary:hover {
            background: color-mix(in srgb, var(--primary) 90%, black);
        }

        .btn-outline {
            border: 1px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--primary-foreground);
        }

        /* No results */
        .no-results {
            text-align: center;
            padding: 3rem 1rem;
        }

        .no-results-text {
            color: var(--muted-foreground);
            font-size: 1.125rem;
            margin-bottom: 1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .hero-section {
                padding: 2rem 0 3rem;
            }

            .stats-section,
            .stalls-section {
                padding: 2rem 0;
            }

            .category-filters {
                gap: 0.375rem;
            }

            .category-btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
            }
        }
    </style>

    <div class="min-h-screen bg-background">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <div class="space-y-6">
                    <div class="space-y-4">
                        <h1 class="hero-title">
                            Discover Amazing
                            <span class="text-primary">Food Stalls</span>
                        </h1>
                        <p class="hero-description">
                            Explore all the incredible food vendors at LTO Canteen Central. Each stall offers unique
                            specialties and authentic flavors.
                        </p>
                    </div>

                    <!-- Search Bar -->
                    <div class="search-container">
                        <div style="position: relative;">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z" />
                            </svg>
                            <form action="{{ route('stalls.index') }}" method="GET" id="search-form">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search stalls, dishes, or specialties..." class="search-input"
                                    id="search-input">
                            </form>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="category-filters">
                        <a href="{{ route('stalls.index') }}"
                            class="category-btn {{ !request('search') && !request('category') ? 'active' : '' }}">All</a>
                        <a href="{{ route('stalls.index', ['category' => 'Filipino']) }}"
                            class="category-btn {{ request('category') === 'Filipino' ? 'active' : '' }}">Filipino</a>
                        <a href="{{ route('stalls.index', ['category' => 'Chinese-Filipino']) }}"
                            class="category-btn {{ request('category') === 'Chinese-Filipino' ? 'active' : '' }}">Chinese-Filipino</a>
                        <a href="{{ route('stalls.index', ['category' => 'Fresh Market']) }}"
                            class="category-btn {{ request('category') === 'Fresh Market' ? 'active' : '' }}">Fresh
                            Market</a>
                        <a href="{{ route('stalls.index', ['category' => 'Services']) }}"
                            class="category-btn {{ request('category') === 'Services' ? 'active' : '' }}">Services</a>
                        <a href="{{ route('stalls.index', ['category' => 'Snacks & Beverages']) }}"
                            class="category-btn {{ request('category') === 'Snacks & Beverages' ? 'active' : '' }}">Snacks
                            & Beverages</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stalls Grid -->
        <section class="stalls-section">
            <div class="container">
                <div class="stalls-header">
                    <div>
                        <h2 class="stalls-title">
                            @if (request('search'))
                                Search Results for "{{ request('search') }}"
                            @else
                                All Active Stalls
                            @endif
                        </h2>
                        <p class="stalls-count">
                            {{ $stalls->count() }} stall{{ $stalls->count() !== 1 ? 's' : '' }} available
                        </p>
                    </div>
                    <div class="user-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        @if (session('user_type') === 'guest')
                            Browsing as Guest
                        @else
                            Employee Access
                        @endif
                    </div>
                </div>

                @if ($stalls->count() > 0)
                    <div class="stalls-grid">
                        @foreach ($stalls as $stall)
                            <div class="stall-card">
                                <div class="stall-image">
                                    <img src="{{ $stall->image_url }}" alt="{{ $stall->name }}"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($stall->name) }}&size=800&background=FF6B35&color=fff&font-size=0.33&bold=true'">
                                    @if ($stall->opening_time && $stall->closing_time)
                                        <div class="time-badge">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12,6 12,12 16,14" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($stall->opening_time)->format('g:iA') }} -
                                            {{ \Carbon\Carbon::parse($stall->closing_time)->format('g:iA') }}
                                        </div>
                                    @endif
                                    <div class="status-badge">{{ $stall->is_active ? 'Active' : 'Closed' }}</div>
                                </div>

                                <div class="stall-content">
                                    <div style="margin-bottom: 0.5rem;">
                                        <h3 class="stall-name">{{ $stall->name }}</h3>
                                        <p class="stall-description">{{ $stall->description }}</p>
                                    </div>

                                    <div class="stall-tags">
                                        @if ($stall->name === "Tita's Kitchen")
                                            <span class="stall-tag">Adobo</span>
                                            <span class="stall-tag">Sinigang</span>
                                            <span class="stall-tag">Lechon Kawali</span>
                                        @elseif($stall->name === 'Chowpan sa Binondo')
                                            <span class="stall-tag">Chowpan</span>
                                            <span class="stall-tag">Dumplings</span>
                                            <span class="stall-tag">Noodles</span>
                                        @else
                                            <span class="stall-tag">Specialty</span>
                                            <span class="stall-tag">Local</span>
                                        @endif
                                    </div>

                                    <div class="stall-meta">
                                        <div class="meta-location">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            <span>{{ $stall->location }}</span>
                                        </div>
                                        <div class="meta-rating">
                                            <svg class="star-icon" width="16" height="16" viewBox="0 0 24 24"
                                                fill="currentColor" stroke="currentColor" stroke-width="2">
                                                <polygon
                                                    points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                                            </svg>
                                            <span style="font-weight: 500;">4.8</span>
                                            <span>(124)</span>
                                        </div>
                                    </div>

                                    <div class="stall-info">
                                        <span
                                            class="text-muted-foreground">{{ $stall->products->where('is_available', true)->count() }}
                                            items • </span>
                                        <span class="text-primary">{{ $stall->getPriceRange() }}</span>
                                    </div>

                                    <div class="stall-actions">
                                        <a href="{{ route('stalls.show', $stall) }}" class="btn btn-primary"
                                            style="flex: 1; width: 100%;">
                                            View Full Menu
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-results">
                        <div class="no-results-text">No stalls found matching your search criteria.</div>
                        @if (request('search') || request('category'))
                            <a href="{{ route('stalls.index') }}" class="btn btn-outline"
                                style="display: inline-block; margin-top: 1rem;">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </section>


        <!-- Statistics Section -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-header">
                    <h2 class="stats-title">Stall Statistics</h2>
                    <p class="stats-description">Overview of our vibrant food stall ecosystem</p>
                </div>

                <div class="stats-grid">
                    <!-- Active Stalls -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                                <path d="M3 6h18" />
                                <path d="M16 10a4 4 0 0 1-8 0" />
                            </svg>
                        </div>
                        <div class="stat-value">{{ App\Models\Stall::where('is_active', true)->count() }}</div>
                        <div class="stat-label">Active Stalls</div>
                    </div>

                    <!-- Total Menu Items -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="m7.5 4.27 9 5.15" />
                                <path
                                    d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                <path d="m3.3 7 8.7 5 8.7-5" />
                                <path d="M12 22V12" />
                            </svg>
                        </div>
                        <div class="stat-value">{{ App\Models\Product::where('is_available', true)->count() }}</div>
                        <div class="stat-label">Total Menu Items</div>
                    </div>

                    <!-- Food Categories -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="m22 21-3-3m1-4a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                            </svg>
                        </div>
                        <div class="stat-value">{{ App\Models\Category::where('is_active', true)->count() ?: 'N/A' }}
                        </div>
                        <div class="stat-label">Food Categories</div>
                    </div>

                    <!-- Average Price (FIXED - Convert centavos to pesos) -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="m3 3 3 9-3 9 19-9Z" />
                                <path d="M6 12h16" />
                            </svg>
                        </div>
                        <div class="stat-value">
                            @php
                                $avgPriceInCentavos = App\Models\Product::where('is_available', true)->avg('price');
                                $avgPriceInPesos = $avgPriceInCentavos ? $avgPriceInCentavos / 100 : 0;
                            @endphp
                            ₱{{ number_format($avgPriceInPesos, 0) }}
                        </div>
                        <div class="stat-label">Average Price</div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script>
        // Auto-submit search form
        document.getElementById('search-input').addEventListener('input', function() {
            const form = document.getElementById('search-form');
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                form.submit();
            }, 500);
        });
    </script>
@endsection
