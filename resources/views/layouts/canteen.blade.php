<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Flash message meta tag for JavaScript --}}
    @if (session('message'))
        <meta name="flash-message" content="{{ session('message') }}">
    @endif

    <title>@yield('title', 'LTO Canteen Central')</title>

    <!-- ✅ ADD: reCAPTCHA v3 Script -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

    <!-- ADD THIS: Welcome Modal CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome-modal.css') }}">
    {{-- Livewire Styles --}}
    @livewireStyles

    <style>
        /* Your existing CSS variables with updated colors to match the design */
        :root {
            --primary: #ef4444;
            --primary-light: #f87171;
            --primary-lighter: #fecaca;
            --secondary: #ea580c;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --orange-50: #fff7ed;
            --orange-100: #ffedd5;
            --red-500: #ef4444;
            --red-600: #dc2626;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* REMOVED ALL NAVBAR-RELATED CSS TO AVOID CONFLICTS */
        /* Your separate navbar component will handle all navbar styling */

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
            background: linear-gradient(135deg, #ef4444 0%, #ea580c 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-secondary {
            background-color: white;
            color: #ef4444;
            border: 2px solid #ef4444;
        }

        .btn-secondary:hover {
            background-color: #ef4444;
            color: white;
        }

        /* Main content spacing - adjusted for your navbar component */
        main {
            margin-top: 64px;
            padding-top: 0;
            min-height: calc(100vh - 64px);
        }


        @media (max-width: 640px) {
            main {
                margin-top: 64px;
                padding-top: 0;
                /* ← Remove the padding */
            }
        }

        /* Modern Footer Styles */
        footer {
            background: linear-gradient(135deg, #ef4444 0%, #ea580c 100%);
            color: white;
            margin-top: 60px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (min-width: 640px) {
            .footer-container {
                padding: 48px 24px;
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
                gap: 32px;
                margin-bottom: 32px;
            }
        }

        @media (min-width: 1024px) {
            .footer-content {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Brand Section */
        .footer-brand {
            grid-column: span 1;
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            .footer-brand {
                grid-column: span 2;
            }
        }

        @media (min-width: 1024px) {
            .footer-brand {
                grid-column: span 1;
            }
        }

        .footer-brand h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        @media (min-width: 640px) {
            .footer-brand h3 {
                font-size: 1.5rem;
                margin-bottom: 16px;
            }
        }

        .footer-brand p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 16px;
            line-height: 1.6;
            font-size: 0.875rem;
        }

        @media (min-width: 640px) {
            .footer-brand p {
                margin-bottom: 24px;
                font-size: 1rem;
            }
        }

        .footer-cta-btn {
            background: white;
            color: #ef4444;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-block;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }

        @media (min-width: 640px) {
            .footer-cta-btn {
                padding: 8px 24px;
                width: auto;
                font-size: 1rem;
            }
        }

        .footer-cta-btn:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: scale(1.05);
            color: #ef4444;
        }

        /* Footer Columns */
        .footer-column h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        @media (min-width: 640px) {
            .footer-column h4 {
                font-size: 1.125rem;
                margin-bottom: 16px;
            }
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-column ul li {
            margin-bottom: 8px;
        }

        @media (min-width: 640px) {
            .footer-column ul li {
                margin-bottom: 12px;
            }
        }

        .footer-column ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.2s;
            font-size: 0.875rem;
        }

        @media (min-width: 640px) {
            .footer-column ul li a {
                font-size: 1rem;
            }
        }

        .footer-column ul li a:hover {
            color: white;
        }

        /* Contact Info Styles */
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 0.875rem;
        }

        @media (min-width: 640px) {
            .contact-item {
                gap: 12px;
                margin-bottom: 12px;
                font-size: 1rem;
            }
        }

        .contact-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .contact-text {
            color: rgba(255, 255, 255, 0.8);
            word-break: break-all;
        }

        /* Payment Methods */
        .payment-section {
            grid-column: span 1;
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            .payment-section {
                grid-column: span 2;
            }
        }

        @media (min-width: 1024px) {
            .payment-section {
                grid-column: span 1;
            }
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
            justify-content: flex-start;
        }

        @media (min-width: 640px) {
            .payment-methods {
                gap: 12px;
                margin-bottom: 16px;
            }
        }

        .payment-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        @media (min-width: 640px) {
            .payment-card {
                padding: 12px;
            }
        }

        .payment-card:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .payment-logo {
            width: 56px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
        }

        @media (min-width: 640px) {
            .payment-logo {
                width: 64px;
                height: 40px;
                font-size: 0.75rem;
            }
        }

        .gcash {
            background: #0066cc;
        }

        .maya {
            background: #00a650;
        }

        .credit-card {
            background: #374151;
            font-size: 16px;
        }

        @media (min-width: 640px) {
            .credit-card {
                font-size: 20px;
            }
        }

        .payment-note {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (min-width: 640px) {
            .payment-note {
                font-size: 0.875rem;
            }
        }

        /* Footer Bottom */
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 16px;
        }

        @media (min-width: 640px) {
            .footer-bottom {
                padding-top: 24px;
            }
        }

        .footer-bottom-content {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }

        @media (min-width: 768px) {
            .footer-bottom-content {
                flex-direction: row;
                justify-content: space-between;
                gap: 16px;
            }
        }

        .copyright {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            text-align: center;
        }

        @media (min-width: 640px) {
            .copyright {
                font-size: 0.875rem;
            }
        }

        @media (min-width: 768px) {
            .copyright {
                text-align: left;
            }
        }

        .footer-links {
            display: flex;
            gap: 16px;
            font-size: 0.75rem;
        }

        @media (min-width: 640px) {
            .footer-links {
                gap: 24px;
                font-size: 0.875rem;
            }
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: white;
        }
    </style>

    {{-- Additional styles from individual pages --}}
    @stack('styles')
</head>

<body>
    <!-- Include the separate navbar component -->
    @include('components.navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer>
        <div class="footer-container">
            <!-- Main Footer Content -->
            <div class="footer-content">
                <!-- Brand Section -->
                <div class="footer-brand">
                    <h3>LTO Canteen Central</h3>
                    <p>Your one-stop food destination at LTO. Fresh meals, quick service, and unbeatable taste.</p>
                    <a href="{{ route('menu.index') }}" class="footer-cta-btn">Order Now</a>
                </div>

                <!-- Quick Links -->
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="{{ route('home.index') }}">Home</a></li>
                        <li><a href="{{ route('menu.index') }}">Menu</a></li>
                        <li><a href="{{ route('stalls.index') }}">Stalls</a></li>
                        <li><a href="#about">About Us</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-column">
                    <h4>Contact Us</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li class="contact-item">
                            <svg class="contact-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            <span class="contact-text">+63 123 456 7890</span>
                        </li>
                        <li class="contact-item">
                            <svg class="contact-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            <span class="contact-text">info@ltocanteen.com</span>
                        </li>
                        <li class="contact-item">
                            <svg class="contact-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="contact-text">LTO Main Office</span>
                        </li>
                        <li class="contact-item">
                            <svg class="contact-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="contact-text">Mon-Fri: 7AM-6PM</span>
                        </li>
                    </ul>
                </div>

                <!-- Payment Methods -->
                <div class="payment-section">
                    <h4>We Accept</h4>
                    <div class="payment-methods">
                        <!-- GCash -->
                        <div class="payment-card">
                            <div class="payment-logo gcash">GCash</div>
                        </div>

                        <!-- PayMaya/Maya -->
                        <div class="payment-card">
                            <div class="payment-logo maya">Maya</div>
                        </div>

                        <!-- Credit Cards -->
                        <div class="payment-card">
                            <div class="payment-logo credit-card">💳</div>
                        </div>
                    </div>
                    <div class="payment-note">Cash payments also accepted</div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        © 2024 LTO Canteen Central. All rights reserved.
                    </div>
                    <div class="footer-links">
                        <a href="#privacy">Privacy Policy</a>
                        <a href="#terms">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Additional scripts from individual pages --}}
    @stack('scripts')

    @livewire('welcome-modal')
</body>

</html>
