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
            /* 🎨 NEW COLOR SCHEME: Orange, White, Red */
            --primary: #ea580c;        /* Orange 600 */
            --primary-light: #fb923c;  /* Orange 400 */
            --primary-dark: #c2410c;   /* Orange 700 */
            --secondary: #dc2626;      /* Red 600 */
            --secondary-light: #ef4444; /* Red 500 */
            --accent: #fbbf24;         /* Amber 400 */
            --light: #fff7ed;          /* Orange 50 */
            --dark: #1e293b;
            --gray: #64748b;
            --success: #10b981;
            --error: #dc2626;
            --warning: #f59e0b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: white;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* ========== IMPROVED HEADER WITH MOBILE MENU ========== */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(234, 88, 12, 0.1);
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
            z-index: 101;
        }
        
        .logo-img {
            height: 45px;
            width: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(234, 88, 12, 0.3);
        }
        
        .logo h1 {
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        /* ✅ RESPONSIVE NAV LINKS */
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
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            bottom: -5px;
            left: 0;
            transition: width 0.3s;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .login-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white !important;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
            border: none;
            cursor: pointer;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4);
        }
        
        /* ✅ MOBILE HAMBURGER MENU */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            z-index: 101;
        }
        
        .hamburger {
            width: 28px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .hamburger::before,
        .hamburger::after {
            content: '';
            position: absolute;
            width: 28px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .hamburger::before {
            top: -8px;
        }
        
        .hamburger::after {
            bottom: -8px;
        }
        
        /* Hamburger animation when open */
        .mobile-menu-btn.active .hamburger {
            background: transparent;
        }
        
        .mobile-menu-btn.active .hamburger::before {
            top: 0;
            transform: rotate(45deg);
        }
        
        .mobile-menu-btn.active .hamburger::after {
            bottom: 0;
            transform: rotate(-45deg);
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
            box-shadow: 0 20px 25px -5px rgba(234, 88, 12, 0.2), 0 10px 10px -5px rgba(234, 88, 12, 0.1);
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
            background-color: #fff7ed;
            color: var(--primary);
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            border: 2px solid #fed7aa;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .login-option:hover {
            border-color: var(--primary);
            background-color: var(--light);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(234, 88, 12, 0.15);
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
            background: linear-gradient(135deg, var(--accent), var(--primary-light));
            color: white;
        }
        
        .employee-icon {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
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
            background: #fed7aa;
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
            border: 2px solid #fed7aa;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
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
            background: linear-gradient(135deg, var(--primary), var(--secondary));
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
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(234, 88, 12, 0.3);
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
            color: var(--secondary);
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
        
        /* ========== IMPROVED HERO SECTION WITH IMAGE BACKGROUND ========== */
        .hero {
            padding: 150px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
            /* 🎨 ADD YOUR BACKGROUND IMAGE HERE */
            background-image: url('{{ asset("images/canteen.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Parallax effect */
        }
        
        /* ✅ ORANGE COLOR OVERLAY */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* Orange gradient overlay - adjust opacity as needed */
            background: linear-gradient(
                135deg, 
                rgba(234, 88, 12, 0.85) 0%,    /* Orange with 85% opacity */
                rgba(251, 146, 60, 0.75) 50%,   /* Lighter orange with 75% opacity */
                rgba(220, 38, 38, 0.8) 100%     /* Red with 80% opacity */
            );
            z-index: 1;
        }
        
        /* Decorative pattern overlay (optional) */
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            z-index: 2;
        }
        
        .hero-content {
            position: relative;
            z-index: 3; /* ✅ Above the overlays */
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            /* ✅ White text for better contrast on photo */
            color: white;
            font-weight: 800;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 40px;
            /* ✅ White text for better readability */
            color: rgba(255, 255, 255, 0.95);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(234, 88, 12, 0.4);
        }
        
        .btn-secondary {
            background-color: white;
            color: var(--primary);
            border: 2px solid var(--primary);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(234, 88, 12, 0.3);
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
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .section-title p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Desktop: 4 columns */
            gap: 30px;
            margin-top: 50px;
        }
        
        .feature-card {
            background: linear-gradient(135deg, #fff7ed 0%, white 100%);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid #fed7aa;
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
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(234, 88, 12, 0.2);
            border-color: var(--primary);
        }
        
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
            display: block;
            filter: drop-shadow(0 4px 10px rgba(234, 88, 12, 0.3));
        }
        
        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: var(--dark);
            font-weight: 700;
        }
        
        .feature-card p {
            color: var(--gray);
            line-height: 1.6;
            font-size: 1rem;
        }
        
        /* Food Categories */
        .categories {
            padding: 80px 0;
            background: linear-gradient(135deg, #fff7ed 0%, white 100%);
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Desktop: 4 columns */
            gap: 30px;
        }
        
        .category-card {
            background-color: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(234, 88, 12, 0.1);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 2px solid #fed7aa;
        }
        
        .category-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(234, 88, 12, 0.2);
            border-color: var(--primary);
        }
        
        .category-img {
            height: 220px;
            width: 100%;
            object-fit: cover;
        }
        
        .category-content {
            padding: 25px;
            text-align: center;
        }
        
        .category-content h3 {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: var(--dark);
            font-weight: 700;
        }
        
        .category-content p {
            color: var(--gray);
        }
        
        /* Testimonials */
        .testimonials {
            padding: 80px 0;
            background-color: white;
        }
        
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Desktop: 3 columns */
            gap: 30px;
        }
        
        .testimonial-card {
            background: linear-gradient(135deg, #fff7ed 0%, white 100%);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(234, 88, 12, 0.1);
            border: 2px solid #fed7aa;
            transition: all 0.3s;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(234, 88, 12, 0.15);
            border-color: var(--primary);
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            color: var(--dark);
            font-size: 1.05rem;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--primary);
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary) 100%);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: white;
            font-weight: 700;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 12px;
        }
        
        .footer-column ul li a {
            color: #fed7aa;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .footer-column ul li a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        /* ✅ NEW: Payment Methods Section */
        .payment-methods {
            margin-top: 25px;
        }
        
        .payment-methods h4 {
            font-size: 1rem;
            margin-bottom: 15px;
            color: white;
            font-weight: 600;
        }
        
        .payment-icons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .payment-badge {
            background: white;
            padding: 8px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--primary);
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .payment-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .payment-badge img {
            height: 20px;
            width: auto;
        }
        
        /* Payment icon emojis as fallback */
        .payment-icon {
            font-size: 1.3rem;
        }
        
        /* ✅ Footer bottom section */
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 30px;
            margin-top: 40px;
        }
        
        .footer-bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .copyright {
            color: #fed7aa;
            font-size: 0.9rem;
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1.2rem;
        }
        
        .social-link:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }
        
        /* Food Highlights */
        .food-highlights {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* ✅ Always 3 columns */
            gap: 30px;
            max-width: 1000px;
            margin: 50px auto;
        }
        
        .highlight-card {
            background-color: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(234, 88, 12, 0.15);
            border: 2px solid #fed7aa;
            transition: all 0.3s;
        }
        
        .highlight-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(234, 88, 12, 0.25);
            border-color: var(--primary);
        }
        
        .highlight-card .emoji {
            font-size: 4.5rem;
            margin-bottom: 20px;
            display: block;
            filter: drop-shadow(0 4px 10px rgba(234, 88, 12, 0.2));
        }
        
        .highlight-card h3 {
            font-size: 1.6rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        .highlight-card p {
            color: var(--gray);
            font-size: 1.05rem;
        }
        
        /* ========== RESPONSIVE DESIGN ========== */
        @media (max-width: 768px) {
            /* Mobile navigation */
            .mobile-menu-btn {
                display: block;
            }
            
            .nav-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 280px;
                height: 100vh;
                background: white;
                flex-direction: column;
                padding: 100px 30px 30px;
                box-shadow: -5px 0 20px rgba(0,0,0,0.1);
                transition: right 0.3s ease;
                gap: 20px;
            }
            
            .nav-links.active {
                right: 0;
            }
            
            .nav-links a {
                font-size: 1.1rem;
                padding: 10px 0;
                width: 100%;
            }
            
            .login-btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            
            /* ✅ Compact hero on mobile */
            .hero {
                padding: 120px 0 60px; /* Less padding */
            }
            
            .hero h1 {
                font-size: 2rem; /* Smaller title */
            }
            
            .hero p {
                font-size: 0.95rem; /* Smaller text */
                margin-bottom: 30px;
            }
            
            .cta-buttons {
                margin-bottom: 40px; /* Less margin */
            }
            
            /* ✅ Keep hero cards in 3 columns on mobile */
            .food-highlights {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px; /* Smaller gap */
                margin: 30px auto;
            }
            
            .highlight-card {
                padding: 20px 15px; /* Smaller padding */
            }
            
            .highlight-card .emoji {
                font-size: 2.5rem; /* Smaller emoji */
                margin-bottom: 10px;
            }
            
            .highlight-card h3 {
                font-size: 0.9rem; /* Smaller heading */
                margin-bottom: 5px;
            }
            
            .highlight-card p {
                font-size: 0.75rem; /* Smaller text */
                line-height: 1.3;
            }
            
            /* ✅ Features: 2 columns on mobile */
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .feature-card {
                padding: 25px 15px; /* Compact padding */
            }
            
            .feature-icon {
                font-size: 2.5rem;
                margin-bottom: 12px;
            }
            
            .feature-card h3 {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }
            
            .feature-card p {
                font-size: 0.85rem;
            }
            
            /* ✅ Categories: 2 columns on mobile */
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .category-img {
                height: 150px; /* Smaller images */
            }
            
            .category-content {
                padding: 15px;
            }
            
            .category-content h3 {
                font-size: 1.1rem;
            }
            
            .category-content p {
                font-size: 0.85rem;
            }
            
            /* ✅ Testimonials: 2 columns on mobile */
            .testimonial-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .testimonial-card {
                padding: 20px;
            }
            
            .testimonial-text {
                font-size: 0.85rem;
                margin-bottom: 15px;
            }
            
            .testimonial-author {
                font-size: 0.8rem;
            }
            
            /* Compact sections */
            .features, .categories, .testimonials {
                padding: 50px 0; /* Less padding */
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            .section-title p {
                font-size: 0.95rem;
            }
            
            .btn {
                padding: 12px 28px;
                font-size: 1rem;
            }
            
            .modal-container {
                margin: 20px;
                max-width: calc(100% - 40px);
            }
            
            /* ✅ Footer mobile styles */
            footer {
                padding: 40px 0 20px;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .footer-column {
                text-align: left;
            }
            
            .payment-icons {
                justify-content: flex-start;
            }
            
            .payment-badge {
                font-size: 0.8rem;
                padding: 6px 12px;
            }
            
            .footer-bottom-content {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-social {
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.6rem;
            }
            
            .section-title h2 {
                font-size: 1.5rem;
            }
            
            /* ✅ Still 3 columns for hero */
            .food-highlights {
                gap: 10px;
            }
            
            .highlight-card {
                padding: 15px 10px;
            }
            
            .highlight-card .emoji {
                font-size: 2rem;
            }
            
            .highlight-card h3 {
                font-size: 0.8rem;
            }
            
            .highlight-card p {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <!-- ========== IMPROVED HEADER WITH MOBILE MENU ========== -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <div class="logo-img">CC</div>
                    <h1>Canteen Central</h1>
                </div>
                
                <!-- ✅ MOBILE HAMBURGER BUTTON -->
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Toggle menu">
                    <div class="hamburger"></div>
                </button>
                
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

    <!-- ========== IMPROVED HERO SECTION ========== -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>🍽️ Canteen Central</h1>
                <p>Discover amazing food from multiple vendors in one place. Fresh meals, quick service, and delicious options for everyone - visitors and LTO employees alike.</p>
                <div class="cta-buttons">
                    <button onclick="openWelcomeModal()" class="btn btn-primary" style="border: none; cursor: pointer;">🛒 Browse Menu</button>
                    <a href="{{ route('menu.index') }}" class="btn btn-secondary">📲 Quick Order</a>
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

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>🍽️ LTO Canteen Central</h3>
                    <p style="color: #fed7aa; margin-top: 10px; line-height: 1.6;">Your one-stop food destination at LTO. Fresh meals, quick service, and delicious options for everyone.</p>
                    
                    <!-- ✅ Payment Methods Section -->
                    <div class="payment-methods">
                        <h4>We Accept:</h4>
                        <div class="payment-icons">
                            <div class="payment-badge">
                                <span class="payment-icon">💳</span>
                                <span>GCash</span>
                            </div>
                            <div class="payment-badge">
                                <span class="payment-icon">💰</span>
                                <span>PayMaya</span>
                            </div>
                            <div class="payment-badge">
                                <span class="payment-icon">💳</span>
                                <span>Cards</span>
                            </div>
                            <div class="payment-badge">
                                <span class="payment-icon">💵</span>
                                <span>Cash</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">🏠 Home</a></li>
                        <li><a href="#features">⭐ Features</a></li>
                        <li><a href="#categories">🍽️ Menu</a></li>
                        <li><a href="#testimonials">💬 Testimonials</a></li>
                        <li><a href="{{ route('menu.index') }}">📲 Order Now</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul>
                        <li><a href="kajacms@gmail.com">📧 Email Us</a></li>
                        <li><a href="#">📞 Customer Support</a></li>
                        <li><a href="#">💬 Feedback</a></li>
                        <li><a href="#">❓ FAQs</a></li>
                        <li><a href="#">📍 Visit Us</a></li>
                    </ul>
                </div>
                
                
            </div>
            
            <!-- ✅ Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        &copy; 2025 Canteen Central. All rights reserved.
                    </div>
                    
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script>
// ✅ MOBILE MENU TOGGLE
function toggleMobileMenu() {
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    mobileBtn.classList.toggle('active');
    navLinks.classList.toggle('active');
}

// Close mobile menu when clicking a link
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        const mobileBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        mobileBtn.classList.remove('active');
        navLinks.classList.remove('active');
    });
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    const nav = document.querySelector('nav');
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (!nav.contains(e.target) && navLinks.classList.contains('active')) {
        mobileBtn.classList.remove('active');
        navLinks.classList.remove('active');
    }
});

function openWelcomeModal() {
    // Wait for Livewire to be ready
    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('openWelcomeModal');
    } else {
        // Fallback: directly manipulate DOM
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            modal.classList.add('active');
        }
    }
}
</script>

</body>
</html>