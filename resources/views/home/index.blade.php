@extends('layouts.canteen')

@section('title', 'Home - LTO Canteen Central')

@section('content')
    <!-- ============ MARKER A - START (Line ~5) ============ -->
    <style>
        :root {
            --amber-50: #fffbeb;
            --amber-100: #fef3c7;
            --amber-500: #f59e0b;
            --amber-600: #d97706;
            --red-50: #fef2f2;
            --red-500: #ef4444;
            --red-600: #dc2626;
            --orange-500: #f97316;
            --blue-500: #3b82f6;
            --green-500: #10b981;
            --pink-500: #ec4899;
            --purple-500: #8b5cf6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(to bottom, var(--amber-50), var(--white));
            color: var(--gray-900);
            line-height: 1.6;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        @media (min-width: 640px) {
            .container {
                padding: 0 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .container {
                padding: 0 2rem;
            }
        }

        /* Header */
        .header {
            background: var(--white);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid var(--gray-100);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(to right, var(--red-500), var(--amber-500));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.125rem;
        }

        .logo-text h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .logo-text p {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .nav-items {
            display: none;
            align-items: center;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .nav-items {
                display: flex;
            }
        }

        .nav-btn {
            padding: 0.5rem 1rem;
            border: none;
            background: transparent;
            color: var(--gray-900);
            cursor: pointer;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
            text-decoration: none;
        }

        .nav-btn:hover {
            background: var(--gray-50);
        }

        .guest-btn {
            border: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* ========================================
                                       IMPROVED HERO SECTION - Replace in your index.blade.php
                                       ======================================== */

        /* Hero Section with Curved Wave Shape */
        .hero-section {
            position: relative;
            background: linear-gradient(135deg, var(--red-500) 0%, var(--amber-500) 50%, var(--orange-500) 100%);
            color: white;
            padding: 5rem 0 10rem;
            text-align: center;
            overflow: hidden;
            /* ✅ Curved wave bottom - removes rectangular look */
            clip-path: polygon(0 0,
                    100% 0,
                    100% 85%,
                    75% 90%,
                    50% 95%,
                    25% 90%,
                    0 85%);
        }

        /* ✅ Animated floating circles for visual interest */
        .hero-section::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            right: -150px;
            animation: float 8s ease-in-out infinite;
            z-index: 0;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -100px;
            left: -100px;
            animation: float 6s ease-in-out infinite reverse;
            z-index: 0;
        }

        /* ✅ Floating animation */
        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-30px, -30px) scale(1.1);
            }
        }

        /* ✅ Desktop responsive curve */
        @media (min-width: 768px) {
            .hero-section {
                padding: 6rem 0 12rem;
                clip-path: polygon(0 0, 100% 0, 100% 85%,
                        87.5% 88%, 75% 90%, 62.5% 92%,
                        50% 95%, 37.5% 92%, 25% 90%,
                        12.5% 88%, 0 85%);

            }
        }

        /* ✅ Mobile responsive - simpler curve */
        @media (max-width: 480px) {
            .hero-section {
                padding: 4rem 0 7rem;
                clip-path: polygon(0 0, 100% 0, 100% 85%, 75% 90%, 50% 95%, 25% 90%, 0 85%);

            }
        }

        /* Hero Content - ensure it's above decorative elements */
        .hero-content {
            position: relative;
            z-index: 1;
        }

        /* Optional: Add more decorative elements */
        .hero-section .hero-decoration {
            position: absolute;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
            border-radius: 50%;
            filter: blur(40px);
        }

        .hero-section .hero-decoration:nth-child(1) {
            top: 10%;
            left: 10%;
            animation: float 7s ease-in-out infinite;
        }

        .hero-section .hero-decoration:nth-child(2) {
            top: 60%;
            right: 15%;
            animation: float 9s ease-in-out infinite reverse;
        }


        .hero-content {
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 3.75rem);
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        @media (min-width: 768px) {
            .hero-title {
                font-size: clamp(3.75rem, 8vw, 6rem);
            }
        }

        .hero-title .highlight {
            display: block;
            color: #fde68a;
        }

        .hero-description {
            font-size: clamp(1.25rem, 2vw, 1.5rem);
            color: #fde68a;
            margin-bottom: 2rem;
            max-width: 32rem;
            margin-left: auto;
            margin-right: auto;
        }

        @media (min-width: 768px) {
            .hero-description {
                font-size: clamp(1.5rem, 3vw, 2rem);
            }
        }

        .hero-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }

        @media (min-width: 640px) {
            .hero-buttons {
                flex-direction: row;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-lg {
            padding: 1rem 2rem;
        }

        .btn-primary {
            background: white;
            color: var(--red-600);
        }

        .btn-primary:hover {
            background: var(--amber-50);
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 1px solid white;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .delivery-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #fde68a;
            font-size: 0.875rem;
        }

        /* Categories Section */
        .categories-section {
            margin-top: -60px;
            /* Pull it up */
            position: relative;
            z-index: 2;
            padding: 2rem 0;
            background: white;
        }

        @media (min-width: 768px) {
            .categories-section {
                padding: 3rem 0;
            }
        }

        .section-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: clamp(1.5rem, 3vw, 1.875rem);
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        @media (min-width: 768px) {
            .section-title {
                font-size: clamp(1.875rem, 4vw, 2.25rem);
            }
        }

        .section-subtitle {
            color: var(--gray-600);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .categories-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            border-radius: 1rem;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: var(--gray-900);
            background: transparent;
            /* Changed from var(--white) */
        }

        .category-item:hover {
            transform: scale(1.05);
            /* Removed box-shadow */
        }

        .category-item:hover .category-icon {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            /* Shadow on icon only */
        }


        .category-icon {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
            color: white;
            font-size: 2rem;
            transition: transform 0.2s;
        }

        .category-item:hover .category-icon {
            transform: scale(1.1);
        }

        .category-name {
            font-size: 0.875rem;
            font-weight: 500;
            text-align: center;
        }


        /* ========================================
                                       POPULAR ITEMS GRID - THIS WAS MISSING!
                                       ======================================== */

        .popular-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .popular-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .popular-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1280px) {
            .popular-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* ========================================
                                       IMPROVED PRODUCT CARD STYLES - HOMEPAGE
                                       ======================================== */

        .product-card-improved {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card-improved:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(234, 88, 12, 0.15);
            border-color: var(--amber-500);
        }

        /* Image Container */
        .product-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        }

        @media (min-width: 768px) {
            .product-image-container {
                height: 220px;
            }
        }

        .image-link {
            display: block;
            width: 100%;
            height: 100%;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-card-improved:hover .product-image {
            transform: scale(1.08);
        }

        /* Product Badges */
        .product-badges {
            position: absolute;
            top: 12px;
            left: 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 2;
        }

        .product-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            white-space: nowrap;
        }

        .badge-bestseller {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .badge-popular {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .badge-new {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .badge-featured {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        /* Quick View Overlay */
        .quick-view-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .product-card-improved:hover .quick-view-overlay {
            opacity: 1;
        }

        .quick-view-button {
            background: white;
            color: var(--gray-900);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .quick-view-button:hover {
            background: var(--amber-500);
            color: white;
            transform: scale(1.05);
        }

        /* Product Details */
        .product-details {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .stall-link {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--gray-500);
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }

        .stall-link:hover {
            color: var(--amber-600);
        }

        .stall-icon {
            font-size: 1rem;
        }

        .stall-name-text {
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .product-title a {
            color: var(--gray-900);
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.2s;
        }

        .product-title a:hover {
            color: var(--red-600);
        }

        @media (min-width: 768px) {
            .product-title {
                font-size: 1.2rem;
            }
        }

        .product-desc {
            color: var(--gray-600);
            font-size: 0.85rem;
            line-height: 1.5;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Product Meta */
        .product-meta {
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid var(--gray-100);
        }

        .price-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--red-600) 0%, var(--amber-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sales-count {
            font-size: 0.75rem;
            color: var(--gray-500);
            background: var(--gray-50);
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 500;
        }

        /* Add to Cart Wrapper */
        .add-to-cart-wrapper {
            margin-top: 8px;
        }

        /* Override Livewire button styles if needed */
        .add-to-cart-wrapper button {
            width: 100%;
            font-size: 0.95rem;
            font-weight: 600;
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .product-card-improved {
                border-radius: 12px;
            }

            .product-image-container {
                height: 180px;
            }

            .product-details {
                padding: 16px;
                gap: 8px;
            }

            .product-title {
                font-size: 1rem;
            }

            .product-price {
                font-size: 1.3rem;
            }

            .product-badge {
                font-size: 0.7rem;
                padding: 4px 10px;
            }

            .quick-view-button {
                padding: 8px 16px;
                font-size: 0.85rem;
            }
        }

        /* Loading State */
        .add-to-cart-wrapper .loading {
            opacity: 0.7;
            cursor: wait;
        }

        /* Featurede STalls Section */
        .stalls-section {
            padding: 4rem 0;
            background: white;
        }

        .stalls-grid {
            display: grid;
            gap: 2rem;
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
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border: none;
        }

        .stall-card:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            transform: translateY(-8px);
        }

        .stall-image {
            position: relative;
            width: 100%;
            height: 12rem;
        }

        .stall-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .stall-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: white;
        }

        .badge-new {
            background: var(--green-500);
        }

        .badge-24h {
            background: var(--blue-500);
        }

        .stall-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
        }

        .status-open {
            background: var(--green-500);
        }

        .stall-content {
            padding: 1.5rem;
        }

        .stall-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .stall-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .stall-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stall-description {
            color: var(--gray-600);
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .stall-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .featured-items {
            margin-bottom: 1rem;
        }

        .featured-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .featured-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .featured-tag {
            background: var(--gray-100);
            color: var(--gray-700);
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }

        .stall-btn {
            width: 100%;
            background: linear-gradient(to right, var(--red-500), var(--amber-500));
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .stall-btn:hover {
            background: linear-gradient(to right, var(--red-600), var(--amber-600));
        }

        /* How I Wors Section */
        .how-it-works-section {
            padding: 4rem 0;
            background: linear-gradient(to right, var(--amber-50), var(--red-50));
        }

        .steps-grid {
            display: grid;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .steps-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .step-item {
            text-align: center;
        }

        .step-icon {
            width: 5rem;
            height: 5rem;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .step-item:hover .step-icon {
            transform: scale(1.1);
        }

        .step-number {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            background: linear-gradient(to right, var(--red-500), var(--amber-500));
            color: white;
            border-radius: 50%;
            font-size: 0.875rem;
            font-weight: 700;
            line-height: 2rem;
            margin-bottom: 0.5rem;
        }

        .step-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .step-description {
            color: var(--gray-600);
            line-height: 1.5;
        }

        /* CTA Section */
        .cta-section {
            padding: 4rem 0;
            background: linear-gradient(to right, var(--red-600), var(--amber-600));
            color: white;
            text-align: center;
        }

        .cta-content {
            max-width: 64rem;
            margin: 0 auto;
        }

        .cta-title {
            font-size: clamp(1.875rem, 4vw, 2.25rem);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        @media (min-width: 768px) {
            .cta-title {
                font-size: clamp(2.25rem, 5vw, 3rem);
            }
        }

        .cta-description {
            font-size: 1.25rem;
            color: #fde68a;
            margin-bottom: 2rem;
        }

        .cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: center;
            align-items: center;
        }

        @media (min-width: 640px) {
            .cta-buttons {
                flex-direction: row;
            }
        }



        /* Responsive adjustments */
        @media (max-width: 480px) {
            .container {
                padding: 0 0.75rem;
            }

            .hero-section {
                padding: 3rem 0 4rem;
            }

            .categories-section,
            .popular-section,
            .stalls-section,
            .how-it-works-section {
                padding: 2rem 0;
            }

            .popular-grid {
                gap: 1rem;
            }

            .item-image {
                height: 7rem;
            }

            .item-content {
                padding: 0.75rem;
            }

            .stall-content {
                padding: 1rem;
            }
        }
    </style>

    <div class="min-h-screen">


        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-badge">
                        🎉 Good Evening! Special dinner deals available
                    </div>

                    <h1 class="hero-title">
                        Craving Something
                        <span class="highlight">Delicious?</span>
                    </h1>

                    <p class="hero-description">
                        @if (session('user_type') === 'guest')
                            Browse our amazing food selection from multiple stalls and place your order!
                        @else
                            Welcome back! Enjoy exclusive employee discounts and benefits.
                        @endif
                    </p>

                    <div class="hero-buttons">
                        <a href="{{ route('menu.index') }}" class="btn btn-lg btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 2l1.68 12.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L20 2H3z" />
                                <path d="m7 13l3 3 7-7" />
                            </svg>
                            Browse Menu
                        </a>
                        <a href="{{ route('stalls.index') }}" class="btn btn-lg btn-outline">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            View Stalls
                        </a>
                    </div>

                    <div class="delivery-info">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span>Delivering to: LTO Main Building</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Categories Section (Around line 365-463 based on your CSS) --}}
        <section class="categories-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">What are you craving for today?</h2>
                    <p class="section-subtitle">Choose from our diverse selection of food categories</p>
                </div>

                <div class="categories-grid">
                    {{-- All Items (Always First) --}}
                    <a href="{{ route('menu.index') }}" class="category-item">
                        <div class="category-icon" style="background: linear-gradient(135deg, #ef4444, #f59e0b);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <span class="category-name">All Items</span>
                    </a>

                    {{-- 🔥 Dynamic Categories from Database --}}
                    @foreach ($categories as $category)
                        <a href="{{ route('menu.index', ['category_id' => $category->id]) }}" class="category-item">
                            <div class="category-icon"
                                style="background: linear-gradient(135deg, #{{ sprintf('%06X', mt_rand(0, 0xee4444)) }}, #f59e0b);">
                                @if ($category->icon)
                                    {{-- If category has custom icon --}}
                                    <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}"
                                        style="width: 50%; height: 50%; object-fit: contain;">
                                @else
                                    {{-- Default Font Awesome icons based on category name --}}
                                    <i
                                        class="fas fa-{{ strtolower(str_replace(' ', '-', $category->name)) === 'fresh-meals'
                                            ? 'utensils'
                                            : (strtolower(str_replace(' ', '-', $category->name)) === 'sandwiches'
                                                ? 'bread-slice'
                                                : (strtolower(str_replace(' ', '-', $category->name)) === 'beverages'
                                                    ? 'mug-hot'
                                                    : (strtolower(str_replace(' ', '-', $category->name)) === 'snacks'
                                                        ? 'cookie'
                                                        : (strtolower(str_replace(' ', '-', $category->name)) === 'boxed-meals'
                                                            ? 'box'
                                                            : 'utensils')))) }}"></i>
                                @endif
                            </div>
                            <span class="category-name">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ============ MARKER B - POPULAR SECTION START ============ -->
        <!-- Popular Items -->
        <section class="popular-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Today's Favorites</h2>
                    <p class="section-subtitle">Most loved dishes by our community</p>
                </div>

                <div class="popular-grid">
                    @php
                        $popularItems =
                            $topFoods ??
                            App\Models\Product::where('is_available', true)
                                ->where('is_published', true)
                                ->with('stall')
                                ->withCount('orderItems')
                                ->orderBy('order_items_count', 'desc')
                                ->take(4)
                                ->get();
                    @endphp

                    @foreach ($popularItems as $item)
                        <!-- Use the SAME structure as your menu page - just the Livewire component -->
                        @livewire(
                            'add-to-cart-button',
                            [
                                'product' => $item,
                                'showPrice' => true,
                                'showQuantitySelector' => false,
                                'buttonText' => 'Add',
                                'buttonSize' => 'medium',
                            ],
                            key('home-product-' . $item->id)
                        )
                    @endforeach
                </div>
            </div>
        </section>



        <!-- Featured Stalls -->
        <section class="stalls-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Featured Food Stalls</h2>
                    <p class="section-subtitle">Discover unique flavors from our carefully selected food vendors</p>
                </div>

                <div class="stalls-grid">
                    @php
                        $featuredStalls = App\Models\Stall::where('is_active', true)
                            ->with([
                                'products' => function ($query) {
                                    $query
                                        ->where('is_available', true)
                                        ->where('is_published', true)
                                        ->orderBy('created_at', 'desc')
                                        ->take(3);
                                },
                            ])
                            ->take(3)
                            ->get();
                    @endphp

                    @forelse ($featuredStalls as $stall)
                        @php
                            // Determine if stall is open 24 hours
                            $is24Hours = $stall->opening_time === '00:00:00' && $stall->closing_time === '23:59:59';

                            // Determine if stall is new (created within last 30 days)
                            $isNew = $stall->created_at && $stall->created_at->diffInDays(now()) <= 30;

                            // Determine if stall is open - assume all active stalls are open
                            $isOpen = true;

                            // Get stall image using the image_url accessor
                            $stallImage =
                                $stall->image_url ??
                                'https://ui-avatars.com/api/?name=' .
                                    urlencode($stall->name) .
                                    '&size=800&background=FF6B35&color=fff&font-size=0.33&bold=true';
                        @endphp

                        <div class="stall-card">
                            <div class="stall-image">
                                <img src="{{ $stallImage }}" alt="{{ $stall->name }}"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($stall->name) }}&size=800&background=FF6B35&color=fff&font-size=0.33&bold=true'">

                                @if ($is24Hours)
                                    <div class="stall-badge badge-24h">24H</div>
                                @elseif ($isNew)
                                    <div class="stall-badge badge-new">NEW</div>
                                @endif

                                <div class="stall-status">
                                    <div class="status-dot status-open"></div>
                                    <span style="font-size: 0.75rem; font-weight: 500;">Open</span>
                                </div>
                            </div>

                            <div class="stall-content">
                                <div class="stall-header">
                                    <h3 class="stall-name">{{ $stall->name }}</h3>
                                </div>

                                <p class="stall-description">
                                    {{ $stall->description ?? 'Delicious food awaits you at this amazing stall.' }}</p>

                                <div class="stall-location">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <span
                                        style="font-size: 0.875rem; color: var(--gray-500);">{{ $stall->location }}</span>
                                </div>

                                @if ($stall->opening_time && $stall->closing_time)
                                    <div class="stall-location" style="margin-bottom: 1rem;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        <span style="font-size: 0.875rem; color: var(--gray-500);">
                                            {{ \Carbon\Carbon::parse($stall->opening_time)->format('g:i A') }} -
                                            {{ \Carbon\Carbon::parse($stall->closing_time)->format('g:i A') }}
                                        </span>
                                    </div>
                                @endif

                                @if ($stall->products->count() > 0)
                                    <div class="featured-items">
                                        <p class="featured-label">Popular Items:</p>
                                        <div class="featured-tags">
                                            @foreach ($stall->products as $product)
                                                <span class="featured-tag">{{ $product->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <a href="{{ route('stalls.show', $stall->id) }}" class="stall-btn">View Menu</a>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--gray-500);">
                            <p>No featured stalls available at the moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="how-it-works-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">How It Works</h2>
                    <p class="section-subtitle">Simple steps to get your favorite food</p>
                </div>

                <div class="steps-grid">
                    <div class="step-item">
                        <div class="step-icon" style="background: #fff9e6; color: #f59e0b;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                        </div>
                        <div class="step-number">1</div>
                        <h3 class="step-title">Browse</h3>
                        <p class="step-description">Explore our diverse menu from multiple food stalls</p>
                    </div>

                    <div class="step-item">
                        <div class="step-icon" style="background: #ffe6e6; color: #ef4444;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="11" width="18" height="10" rx="2" ry="2" />
                                <rect x="3" y="3" width="18" height="6" rx="2" ry="2" />
                            </svg>
                        </div>
                        <div class="step-number">2</div>
                        <h3 class="step-title">Order</h3>
                        <p class="step-description">Add your favorite items to cart and checkout</p>
                    </div>

                    <div class="step-item">
                        <div class="step-icon" style="background: #e6f2ff; color: #3b82f6;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12,6 12,12 16,14" />
                            </svg>
                        </div>
                        <div class="step-number">3</div>
                        <h3 class="step-title">Pay</h3>
                        <p class="step-description">Secure payment options available</p>
                    </div>

                    <div class="step-item">
                        <div class="step-icon" style="background: #e6fff2; color: #10b981;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                        </div>
                        <div class="step-number">4</div>
                        <h3 class="step-title">Enjoy</h3>
                        <p class="step-description">Pick up your fresh, hot meal and enjoy!</p>
                    </div>
                </div>
            </div>
        </section>

        <script>
            // para sa smooth scrolling and interactive behaviors
            document.addEventListener('DOMContentLoaded', function() {
                // Smooth scroll for anchor links
                const links = document.querySelectorAll('a[href^="#"]');
                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    });
                });


                const buttons = document.querySelectorAll('.btn');
                buttons.forEach(button => {
                    button.addEventListener('click', function() {
                        this.style.opacity = '0.8';
                        setTimeout(() => {
                            this.style.opacity = '1';
                        }, 200);
                    });
                });

                // Category selection
                const categoryItems = document.querySelectorAll('.category-item');
                categoryItems.forEach(item => {
                    item.addEventListener('click', function() {
                        categoryItems.forEach(cat => cat.classList.remove('active'));
                        this.classList.add('active');
                    });
                });

                // Intersection Observer for animations
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, observerOptions);


                const cards = document.querySelectorAll('.popular-item, .stall-card, .step-item');
                cards.forEach(card => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    observer.observe(card);
                });
            });
        </script>

    @endsection
