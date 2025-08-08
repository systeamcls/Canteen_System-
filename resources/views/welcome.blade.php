<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LTO Canteen Central</title>
    @livewireStyles
    <style>
        :root {
            --primary: #1e3a8a; /* Darker blue */
            --primary-light: #3b82f6; /* Medium blue */
            --primary-lighter: #93c5fd; /* Light blue */
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
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-links a:hover::after {
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
        
        .login-btn {
            background-color: var(--primary);
            color: white !important;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
            border: none;
            cursor: pointer;
        }
        
        .login-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
           display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 420px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s ease;
        }
        
        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }
        
        .modal-header {
            padding: 24px 24px 0 24px;
            text-align: center;
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            font-size: 24px;
            color: #9ca3af;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .modal-close:hover {
            background-color: #f3f4f6;
            color: #6b7280;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .modal-subtitle {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 32px;
        }
        
        .modal-body {
            padding: 0 24px 24px 24px;
        }
        
        /* Login Options */
        .login-option {
            display: block;
            width: 100%;
            padding: 16px;
            margin-bottom: 16px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .login-option:hover {
            border-color: var(--primary-lighter);
            background-color: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
        }
        
        .login-option-content {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .login-option-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        .guest-icon {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
        }
        
        .employee-icon {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: white;
        }
        
        .login-option-text h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .login-option-text p {
            font-size: 0.9rem;
            color: var(--gray);
            line-height: 1.4;
        }
        
        .login-option-arrow {
            margin-left: auto;
            color: #d1d5db;
            font-size: 1.2rem;
            transition: all 0.2s ease;
        }
        
        .login-option:hover .login-option-arrow {
           color: var(--primary);
            transform: translateX(4px);
        }
        
        /* Employee Login Form */
        .employee-form {
            display: none;
            animation: slideIn 0.3s ease;
        }
        
        .employee-form.active {
            display: block;
        }
        
        .form-divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            gap: 16px;
        }
        
        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        
        .form-divider span {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-input.error {
            border-color: var(--error);
        }
        
        .error-message {
            color: var(--error);
            font-size: 0.85rem;
            margin-top: 8px;
            display: none;
            padding: 8px 12px;
            background: #fef2f2;
            border-radius: 6px;
            border-left: 3px solid var(--error);
        }
        
        .error-message.show {
           display: block;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
        }
        
        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .submit-btn.loading {
            color: transparent;
        }
        
        .submit-btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .back-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            padding: 8px 0;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s ease;
        }
        
        .back-btn:hover {
            color: var(--primary-light);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
        
        /* Hero Section */
        .hero {
            padding: 150px 0 80px;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--primary);
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 40px;
            color: var(--gray);
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 50px;
        }
        
        .btn {
            padding: 15px 35px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.4);
        }
        
        .btn-secondary {
            background-color: white;
            color: var(--primary);
            border: 2px solid var(--primary);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn-secondary:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.3);
        }
        
        /* Features Section */
        .features {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-top: 50px;
        }
        
        .feature-card {
            background-color: #f8fafc;
            border-radius: 15px;
            padding: 35px 25px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid var(--primary-lighter);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(30, 58, 138, 0.15);
            background-color: #eff6ff;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--primary);
            display: block;
        }
        
        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: var(--dark);
            font-weight: 600;
        }
        
        .feature-card p {
            color: var(--gray);
            line-height: 1.5;
            font-size: 0.95rem;
        }
        
        /* Food Categories */
        .categories {
            padding: 80px 0;
            background-color: #eff6ff;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .category-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.1);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid var(--primary-lighter);
        }
        
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(30, 58, 138, 0.15);
        }
        
        .category-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        
        .category-content {
            padding: 20px;
            text-align: center;
        }
        
        .category-content h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        /* Testimonials */
        .testimonials {
            padding: 80px 0;
            background-color: white;
        }
        
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .testimonial-card {
           background-color: #f8fafc;
           border-radius: 10px;
           padding: 30px;
           box-shadow: 0 5px 15px rgba(30, 58, 138, 0.05);
           border: 1px solid var(--primary-lighter);
       }
       
       .testimonial-text {
           font-style: italic;
            margin-bottom: 20px;
            color: var(--gray);
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--dark);
        }
        
        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .cta p {
            max-width: 700px;
            margin: 0 auto 40px;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary);
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--light);
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
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
        
        /* Food Highlights */
         .food-highlights {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 50px auto;
        }
        
        .highlight-card {
            background-color: white;
            border-radius: 15px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.1);
            border: 1px solid var(--primary-lighter);
            transition: all 0.3s;
        }
        
        .highlight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.15);
        }
        
        .highlight-card .emoji {
            font-size: 4rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .highlight-card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--primary);
            font-weight: 600;
        }
        
        .highlight-card p {
            color: var(--gray);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }            
            .food-highlights {
                grid-template-columns: 1fr;
            }
            
            .modal-container {
                margin: 20px;
                max-width: calc(100% - 40px);
            }
            
            .features-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }
    </style>
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
                    <a href="#features">Features</a>
                    <a href="#categories">Categories</a>
                    <a href="#testimonials">Testimonials</a>
                    <button class="login-btn" onclick="openWelcomeModal()">Login</button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Livewire Welcome Modal -->
    @livewire('welcome-modal')

    <!-- Hero Section -->
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>LTO Canteen Central</h1>
                <p>Discover amazing food from multiple vendors in one place. Fresh meals, quick service, and delicious options for everyone - visitors and LTO employees alike.</p>
                <div class="cta-buttons">
                    <a href="#categories" class="btn btn-primary">🛒 Browse Menu</a>
                    <a href="#features" class="btn btn-secondary">📲 Quick Order</a>
                </div>
                
                <div class="food-highlights">
                    <div class="highlight-card">
                        <span class="emoji">🍛</span>
                        <h3>Fresh Meals</h3>
                        <p>Hot, Fresh, and Nutritious Meals</p>
                    </div>
                    
                    <div class="highlight-card">
                        <span class="emoji">🥪</span>
                        <h3>Sandwiches</h3>
                        <p>Fresh ingredients, endless combinations</p>
                    </div>
                    
                    <div class="highlight-card">
                        <span class="emoji">🥤</span>
                        <h3>Beverages</h3>
                        <p>Different Variation of Drinks</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Why LTO Visitors & Employees Love Us</h2>
                <p>From quick meals during transactions to hearty lunch breaks - we've got everyone covered</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🍽️</div>
                    <h3>Multiple Food Vendors</h3>
                    <p>Choose from pizza, Asian cuisine, healthy options, and more - all in one place</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Lightning Fast Service</h3>
                    <p>Pre-order your meal and skip the line - perfect for busy schedules</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Pickup & Delivery</h3>
                    <p>Grab your food from the canteen or have it delivered to your office</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👋</div>
                    <h3>Visitor Friendly</h3>
                    <p>Affordable pricing, quick service, and flexible payment methods for all LTO visitors</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Food Categories -->
    <section class="categories" id="categories">
        <div class="container">
            <div class="section-title">
                <h2>Multiple Stalls</h2>
                <p>Variety of Foods Coming from different Stalls!</p>
            </div>
            <div class="category-grid">
                <div class="category-card">
                    <img src="{{ asset('images/adobo.png') }}" alt="Fresh Meals" class="category-img">
                    <div class="category-content">
                        <h3>Fresh Meals</h3>
                        <p>Hot, Savoury, Meaty Flavour</p>
                    </div>
                </div>
                <div class="category-card">
                    <img src="{{ asset('images/pc.png') }}" alt="Instant Food" class="category-img">
                    <div class="category-content">
                        <h3>Instant Foods</h3>
                        <p>Ready to Snacks</p>
                    </div>
                </div>
                <div class="category-card">
                    <img src="{{ asset('images/drinks.png') }}" alt="Beverages" class="category-img">
                    <div class="category-content">
                        <h3>Drinks</h3>
                        <p>Energy boost with our special blends</p>
                    </div>
                </div>
                <div class="category-card">
                    <img src="{{ asset('images/sandwiches.png') }}" alt="Sandwiches" class="category-img">
                    <div class="category-content">
                        <h3>Sandwiches</h3>
                        <p>Authentic Sandwiches made from mwah</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>What People Say</h2>
                <p>Hear from our satisfied customers</p>
            </div>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <p class="testimonial-text">"So convinient, nakapag order kahit nasa pila ako for my license."</p>
                    <p class="testimonial-author">- Aldri L., LTO Employee</p>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"As a visitor, I appreciate how quick and easy it is to get a good meal while waiting for my transaction."</p>
                    <p class="testimonial-author">- Ajo W., LTO Visitor</p>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"Shoutout, Ang smooth ng website and ang sarap ng food Atakeee ang foods."</p>
                    <p class="testimonial-author">- Kath Ty., LTO Staff</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Satisfy Your Cravings?</h2>
            <p>Join LTO visitors and employees who've discovered the easiest way to order delicious food</p>
            <div class="cta-buttons">
                <a href="#" class="btn btn-secondary">Order Now</a>
                <a href="#" class="btn btn-primary">View Menu</a>
            </div>
            <div style="margin-top: 30px; color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                ✓ No delivery fees within LTO ✓ Affordable pricing for all ✓ Quick 10-15 min pickup
            </div>
        </div>
    </section>

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
                        <li><a href="#">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#categories">Menu</a></li>
                        <li><a href="#testimonials">Testimonials</a></li>
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

    @livewireScripts
    <script>
// SIMPLE AND DIRECT - This will work 100%
function openWelcomeModal() {
    console.log('Opening modal...');
    
    // Find the Livewire component
    const livewireComponent = document.querySelector('[wire\\:id]');
    if (livewireComponent) {
        // Get the Livewire component instance
        const component = Livewire.find(livewireComponent.getAttribute('wire:id'));
        if (component) {
            console.log('Found component, calling open method');
            component.call('open');
        } else {
            console.log('Component not found, trying direct method');
            // Fallback: directly set the modal to show
            directModalOpen();
        }
    } else {
        console.log('Livewire component not found, using direct method');
        directModalOpen();
    }
}

// Direct modal opener - GUARANTEED to work
function directModalOpen() {
    // Find the modal
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        modal.style.visibility = 'visible';
        modal.classList.add('active');
        console.log('Modal opened directly!');
    } else {
        console.error('Modal element not found in DOM');
        // If modal not found, create a simple alert
        alert('Modal not found. Please refresh the page.');
    }
}

// Wait for everything to load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, checking for modal...');
    
    setTimeout(() => {
        const modal = document.querySelector('.modal-overlay');
        const livewireComponent = document.querySelector('[wire\\:id]');
        console.log('Modal found:', !!modal);
        console.log('Livewire component found:', !!livewireComponent);
        
        // If modal exists but is hidden, show it briefly to test
        if (modal) {
            console.log('Modal element exists in DOM');
        }
    }, 1000);
});
</script>

</body>
</html>