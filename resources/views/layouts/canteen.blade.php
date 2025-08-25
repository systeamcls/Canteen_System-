<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Flash message meta tag for JavaScript --}}
    @if(session('message'))
        <meta name="flash-message" content="{{ session('message') }}">
    @endif
    
    <title>@yield('title', 'LTO Canteen Central')</title>

    {{-- Livewire Styles --}}
    @livewireStyles
    
    <style>
    /* Your existing CSS styles remain the same */
    :root {
        --primary: #2E5BBA;
        --primary-light: #3b82f6;
        --primary-lighter: #93c5fd;
        --secondary: #f59e0b;
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

    /* Add this to your <style> section */
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

    /* @vite(['resources/css/app.css', 'resources/css/canteen2.css', 'resources/js/app.js']). */
    </style>

    {{-- Additional styles from individual pages --}}
    @stack('styles')
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
                    <a href="{{ route('home.index') }}" class="{{ request()->routeIs('home.*') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('menu.index') }}" class="{{ request()->routeIs('menu.*') ? 'active' : '' }}">Menu</a>
                    <a href="{{ route('stalls.index') }}" class="{{ request()->routeIs('stalls.*') ? 'active' : '' }}">Stalls</a>
                    
                    <!-- REPLACE THE OLD CART BUTTON WITH LIVEWIRE CART PANEL -->
                    @livewire('cart-panel')
                    
                    <!-- User Type Badge -->
                    <div style="background: var(--primary-lighter); color: var(--primary); padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                        @if(session('user_type') === 'guest')
                            👤 Guest
                        @elseif(session('user_type') === 'employee')
                            👨‍💼 Employee
                        @else
                            🔐 Login
                        @endif
                    </div>
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
                    <h3>LTO Canteen Central</h3>
                    <p style="color: #cbd5e1; margin-top: 10px;">Your one-stop food destination at LTO</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/menu">Menu</a></li>
                        <li><a href="/stalls">Stalls</a></li>
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
                &copy; 2023 LTO Canteen Central. All rights reserved.
            </div>
        </div>
    </footer>

    {{-- Livewire Scripts --}}
    @livewireScripts
    
    {{-- Additional scripts from individual pages --}}
    @stack('scripts')
</body>
</html>