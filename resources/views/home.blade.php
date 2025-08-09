<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home - LTO Canteen Central</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/canteen.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    
    <!-- Page-specific styles -->
    <style>
        /* Hero Section */
        .hero {
            padding: 60px 0;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            text-align: center;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .hero p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 30px;
            color: var(--gray);
        }
        
        .welcome-message {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.1);
            display: inline-block;
            margin-bottom: 30px;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }
        
        /* Featured Stalls */
        .featured-stalls {
            padding: 60px 0;
            background-color: white;
        }
        
        .stalls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .stall-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(30, 58, 138, 0.1);
            transition: all 0.3s;
            border: 1px solid var(--primary-lighter);
        }
        
        .stall-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.15);
        }
        
        .stall-image {
            height: 180px;
            background: linear-gradient(135deg, var(--primary-lighter), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }
        
        .stall-content {
            padding: 20px;
        }
        
        .stall-content h3 {
            font-size: 1.3rem;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .stall-content p {
            color: var(--gray);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .view-menu-btn {
            width: 100%;
            padding: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .view-menu-btn:hover {
            background: var(--primary-light);
        }
        
        /* Trending Items */
        .trending-items {
            padding: 60px 0;
            background: #eff6ff;
        }
        
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .item-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.1);
            transition: transform 0.3s;
        }
        
        .item-card:hover {
            transform: translateY(-3px);
        }
        
        .item-image {
            height: 150px;
            background: linear-gradient(45deg, var(--secondary), #fbbf24);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        
        .item-content {
            padding: 15px;
        }
        
        .item-content h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .item-stall {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .add-to-cart-btn {
            width: 100%;
            padding: 8px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .add-to-cart-btn:hover {
            background: #059669;
        }
        
        /* How It Works */
        .how-it-works {
            padding: 60px 0;
            background: white;
        }
        
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .step-card {
            text-align: center;
            padding: 30px 20px;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 15px;
        }
        
        .step-card h3 {
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .step-card p {
            color: var(--gray);
            font-size: 0.9rem;
        }
    </style>
    
    @livewireStyles
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <div class="logo-img">LTO</div>
                    <h1>LTO Canteen Central</h1>
                </div>
                <div class="nav-links">
                    <a href="{{ route('home') }}" class="active">Home</a>
                    <a href="{{ route('menu.index') }}">Menu</a>
                    <a href="{{ route('stalls.index') }}">Stalls</a>
                </div>
                <div class="user-menu">
                    @livewire('cart-icon')
                    @auth
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @else
                        <div class="user-avatar">
                            👤
                        </div>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="welcome-message">
                    @if(session('user_type') === 'guest')
                        <h2>Welcome, Guest! 👋</h2>
                        <p>Browse our delicious menu and order your favorites</p>
                    @elseif(auth()->check())
                        <h2>Welcome back, {{ auth()->user()->name }}! 👋</h2>
                        <p>Ready for another delicious meal?</p>
                    @else
                        <h2>Welcome to LTO Canteen Central! 🍽️</h2>
                        <p>Your food journey starts here</p>
                    @endif
                </div>
                
                <h1>Delicious Food, Just a Click Away</h1>
                <p>Discover amazing meals from multiple vendors in one convenient location. Fresh, fast, and made with love.</p>
                
                <div class="cta-buttons">
                    <a href="{{ route('menu.index') }}" class="btn btn-primary">🛒 Order Now</a>
                    <a href="{{ route('stalls.index') }}" class="btn btn-secondary">🏪 Browse Stalls</a>
                </div>
            </div>
        </section>

        <!-- Featured Stalls -->
        <section class="featured-stalls">
            <div class="container">
                <div class="section-title">
                    <h2>Featured Stalls</h2>
                    <p>Explore our top-rated food vendors and their specialties</p>
                </div>
                
                <div class="stalls-grid">
                    <div class="stall-card">
                        <div class="stall-image">🍛</div>
                        <div class="stall-content">
                            <h3>Filipino Classics</h3>
                            <p>Authentic Filipino dishes made fresh daily. Adobo, sisig, and more!</p>
                            <button class="view-menu-btn" onclick="location.href='{{ route('menu.index', ['stall' => 'filipino-classics']) }}'">View Menu</button>
                        </div>
                    </div>
                    
                    <div class="stall-card">
                        <div class="stall-image">🥪</div>
                        <div class="stall-content">
                            <h3>Sandwich Corner</h3>
                            <p>Fresh sandwiches with premium ingredients. Perfect for quick meals.</p>
                            <button class="view-menu-btn" onclick="location.href='{{ route('menu.index', ['stall' => 'sandwich-corner']) }}'">View Menu</button>
                        </div>
                    </div>
                    
                    <div class="stall-card">
                        <div class="stall-image">🥤</div>
                        <div class="stall-content">
                            <h3>Beverage Station</h3>
                            <p>Refreshing drinks, coffee, and specialty beverages to quench your thirst.</p>
                            <button class="view-menu-btn" onclick="location.href='{{ route('menu.index', ['stall' => 'beverage-station']) }}'">View Menu</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trending Items -->
        <section class="trending-items">
            <div class="container">
                <div class="section-title">
                    <h2>Trending This Week</h2>
                    <p>Popular items that everyone's talking about</p>
                </div>
                
                @livewire('trending-items')
            </div>
        </section>

        <!-- How It Works -->
        <section class="how-it-works">
            <div class="container">
                <div class="section-title">
                    <h2>How It Works</h2>
                    <p>Ordering your favorite food is simple and fast</p>
                </div>
                
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3>Browse</h3>
                        <p>Explore menus from multiple food stalls</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3>Order</h3>
                        <p>Add items to cart and place your order</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3>Pay</h3>
                        <p>Secure online payment or pay on pickup</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3>Enjoy</h3>
                        <p>Pick up your fresh, hot meal and enjoy!</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>LTO Canteen Central</h3>
                    <p>Your one-stop food destination at LTO</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <p><a href="{{ route('home') }}">Home</a></p>
                    <p><a href="{{ route('menu.index') }}">Menu</a></p>
                    <p><a href="{{ route('stalls.index') }}">Stalls</a></p>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <p>LTO Office Building</p>
                    <p>Ground Floor Canteen</p>
                    <p>Phone: (02) 123-4567</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 LTO Canteen Central. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    @livewire('cart-sidebar')

    @livewireScripts
</body>
</html>