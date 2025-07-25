<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CuisiCourt')</title>
    
    <style>
        :root {
            --primary: #FF6B35;
            --primary-light: #FF8866;
            --primary-lighter: #FFB399;
            --secondary: #FF6B35;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8fafc;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            z-index: 100;
            top: 0;
        }
        
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-img {
            height: 40px;
            width: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .logo h1 {
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: 700;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
        }
        
        .nav-links a.active::after {
            width: 100%;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary);
            bottom: -5px;
            left: 0;
            transition: width 0.3s;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .nav-search {
            display: flex;
            align-items: center;
            background: #f3f4f6;
            border-radius: 8px;
            padding: 8px 12px;
            margin-left: 20px;
            margin-right: 20px;
            min-width: 250px;
        }
        
        .nav-search input {
            border: none;
            background: none;
            outline: none;
            color: var(--dark);
            flex: 1;
            padding: 4px 8px;
        }
        
        .nav-search input::placeholder {
            color: var(--gray);
        }
        
        .cart-icon {
            position: relative;
            background: var(--primary);
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-left: 10px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(46, 91, 186, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 91, 186, 0.3);
        }
        
        .btn-secondary {
            background-color: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-secondary:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-cart {
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Main Content */
        main {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .card-content {
            padding: 20px;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .card-text {
            color: var(--gray);
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        /* Grid */
        .grid {
            display: grid;
            gap: 20px;
        }
        
        .grid-2 { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
        .grid-3 { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }
        .grid-4 { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        
        /* Search */
        .search-bar {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 91, 186, 0.1);
        }
        
        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
        }
        
        /* Filters */
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .filter-select {
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            color: var(--dark);
            min-width: 150px;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        /* Status badges */
        .status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-open {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-closed {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Rating */
        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 5px 0;
        }
        
        .stars {
            color: #fbbf24;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary);
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-column h3 {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 8px;
        }
        
        .footer-column ul li a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: white;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 15px;
            }
            
            .nav-links a {
                font-size: 0.9rem;
            }
            
            .nav-search {
                min-width: 200px;
                margin: 10px 0;
            }
            
            .btn {
                padding: 10px 18px;
                font-size: 0.9rem;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .filter-select {
                width: 100%;
            }
            
            .grid-3 { 
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            }
            
            /* Hero section mobile adjustments */
            .container > div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
                gap: 20px !important;
            }
            
            /* Statistics mobile layout */
            div[style*="display: flex; gap: 40px"] {
                gap: 20px !important;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            /* Category filter mobile adjustments */
            section[style*="padding: 30px 0"] div[style*="display: flex; justify-content: center"] {
                justify-content: flex-start !important;
                overflow-x: auto;
                padding-bottom: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }
            
            .nav-search {
                order: -1;
                width: 100%;
                margin: 0 0 15px 0;
            }
            
            .grid-3 { 
                grid-template-columns: 1fr; 
            }
            
            h1[style*="font-size: 3.5rem"] {
                font-size: 2.5rem !important;
            }
            
            h1[style*="font-size: 2.8rem"] {
                font-size: 2rem !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <div class="logo-img">K</div>
                    <h1>Kajacms</h1>
                </div>
                <div class="nav-links">
                    <a href="/menu" class="{{ request()->is('menu*') ? 'active' : '' }}">Our Menu</a>
                    <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
                    <a href="/stalls" class="{{ request()->is('stalls*') ? 'active' : '' }}">Stalls</a>
                    
                    <div class="nav-search">
                        <span>🔍</span>
                        <input type="text" placeholder="Search products">
                    </div>
                    
                    <a href="/cart" class="cart-icon">
                        🛒
                        <span class="cart-count" id="cartCount">3</span>
                    </a>
                    
                    <a href="/profile" style="color: var(--dark); margin-left: 10px;">👤</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Kajacms</h3>
                    <p style="color: #cbd5e1; margin-top: 10px;">Discover amazing food from multiple vendors in one place</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/menu">Menu</a></li>
                        <li><a href="/stalls">Stalls</a></li>
                        <li><a href="/cart">Cart</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul>
                        <li><a href="#">Email Us</a></li>
                        <li><a href="#">Customer Support</a></li>
                        <li><a href="#">Feedback</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; 2023 Kajacms. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Basic cart functionality
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        function updateCartCount() {
            document.getElementById('cartCount').textContent = cart.length;
        }
        
        function addToCart(productId, name, price, image) {
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    name: name,
                    price: price,
                    image: image,
                    quantity: 1
                });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            
            // Show success message
            alert(`${name} added to cart!`);
        }
        
        // Initialize cart count on page load
        updateCartCount();
    </script>
    
    @stack('scripts')
</body>
</html>