@extends('layouts.canteen')

@section('title', 'Home - LTO Canteen Central')

@section('content')
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

/* Hero Section */
.hero-section {
  position: relative;
  background: linear-gradient(to right, var(--red-500), var(--amber-500), var(--orange-500));
  color: white;
  padding: 4rem 0 6rem;
  text-align: center;
}

.hero-section::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.1);
}

@media (min-width: 768px) {
  .hero-section {
    padding: 6rem 0 8rem;
  }
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
  background: var(--white);
}

.category-item:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.category-item.active {
  box-shadow: 0 0 0 2px var(--amber-500);
}

.category-icon {
  width: 4rem;
  height: 4rem;
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

/* Popular Items Section */
.popular-section {
  padding: 3rem 0;
  background: var(--amber-50);
}

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

.popular-item {
  background: white;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
  border: none;
}

.popular-item:hover {
  box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
  transform: translateY(-4px);
}

.item-image {
  position: relative;
  width: 100%;
  height: 8rem;
}

@media (min-width: 768px) {
  .item-image {
    height: 10rem;
  }
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 0.75rem 0.75rem 0 0;
}

.item-badge {
  position: absolute;
  top: 0.5rem;
  left: 0.5rem;
  background: var(--red-500);
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.item-rating {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 9999px;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.star {
  width: 0.75rem;
  height: 0.75rem;
  color: var(--amber-500);
  fill: var(--amber-500);
}

.item-content {
  padding: 1rem;
}

.item-title {
  font-weight: 600;
  color: var(--gray-900);
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
}

@media (min-width: 768px) {
  .item-title {
    font-size: 1rem;
  }
}

.item-stall {
  font-size: 0.75rem;
  color: var(--gray-500);
  margin-bottom: 0.5rem;
}

.item-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.item-price {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--red-600);
}

.add-btn {
  background: var(--amber-500);
  color: white;
  padding: 0.5rem;
  border-radius: 0.25rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.add-btn:hover {
  background: var(--amber-600);
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

/* Footer */
.footer {
  background: var(--gray-900);
  color: white;
  padding: 2rem 0;
  text-align: center;
}

.footer-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.footer-logo-icon {
  width: 2rem;
  height: 2rem;
  background: linear-gradient(to right, var(--red-500), var(--amber-500));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 0.875rem;
}

.footer-text {
  color: var(--gray-400);
  font-size: 0.875rem;
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
          @if(session('user_type') === 'guest')
            Browse our amazing food selection from multiple stalls and place your order!
          @else
            Welcome back! Enjoy exclusive employee discounts and benefits.
          @endif
        </p>
        
        <div class="hero-buttons">
          <a href="{{ route('menu.index') }}" class="btn btn-lg btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 2l1.68 12.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L20 2H3z"/>
              <path d="m7 13l3 3 7-7"/>
            </svg>
            Browse Menu
          </a>
          <a href="{{ route('stalls.index') }}" class="btn btn-lg btn-outline">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            View Stalls
          </a>
        </div>
        
        <div class="delivery-info">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          <span>Delivering to: LTO Main Building</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Categories -->
  <section class="categories-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">What are you craving for today?</h2>
        <p class="section-subtitle">Choose from our diverse selection of food categories</p>
      </div>

      <div class="categories-grid">
        <a href="{{ route('menu.index') }}" class="category-item active">
          <div class="category-icon" style="background: var(--amber-500);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 7h14l-1 7H6l-1-7Z"/>
              <path d="M5 7l-1-3H2"/>
              <path d="m5 17 1 3h8l1-3"/>
              <circle cx="9" cy="21" r="1"/>
              <circle cx="16" cy="21" r="1"/>
            </svg>
          </div>
          <p class="category-name">All Items</p>
        </a>
        
        <a href="{{ route('menu.index', ['category' => 'fresh-meats']) }}" class="category-item">
          <div class="category-icon" style="background: var(--red-500);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
            </svg>
          </div>
          <p class="category-name">Fresh Meals</p>
        </a>
        
        <a href="{{ route('menu.index', ['category' => 'sandwiches']) }}" class="category-item">
          <div class="category-icon" style="background: var(--orange-500);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="10" rx="2" ry="2"/>
              <rect x="3" y="3" width="18" height="6" rx="2" ry="2"/>
            </svg>
          </div>
          <p class="category-name">Sandwiches</p>
        </a>
        
        <a href="{{ route('menu.index', ['category' => 'beverages']) }}" class="category-item">
          <div class="category-icon" style="background: var(--blue-500);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12,6 12,12 16,14"/>
            </svg>
          </div>
          <p class="category-name">Beverages</p>
        </a>
        
        <a href="{{ route('menu.index', ['category' => 'snacks']) }}" class="category-item">
          <div class="category-icon" style="background: var(--pink-500);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
          </div>
          <p class="category-name">Snacks</p>
        </a>
        
        <a href="{{ route('menu.index', ['category' => 'desserts']) }}" class="category-item">
          <div class="category-icon" style="background: var(--purple-500);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <p class="category-name">Desserts</p>
        </a>
      </div>
    </div>
  </section>

  <!-- Popular Items -->
  <section class="popular-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Today's Favorites</h2>
        <p class="section-subtitle">Most loved dishes by our community</p>
      </div>

      <div class="popular-grid">
        @php
          $popularItems = App\Models\Product::where('is_available', true)
              ->where('is_published', true)
              ->with('stall')
              ->take(4)
              ->get();
          
          if($popularItems->isEmpty()) {
              $popularItems = collect([
                  (object)['name' => 'Chicken Adobo Bowl', 'price' => 8500, 'stall' => (object)['name' => "Tita's Kitchen"], 'badge' => 'Bestseller'],
                  (object)['name' => 'Beef Chowpan', 'price' => 12000, 'stall' => (object)['name' => 'Chowpan sa Binondo'], 'badge' => 'Spicy'],
                  (object)['name' => 'Grilled Liempo Set', 'price' => 9500, 'stall' => (object)['name' => 'Grill Master'], 'badge' => 'New'],
                  (object)['name' => 'Fresh Lumpia', 'price' => 4500, 'stall' => (object)['name' => "Tita's Kitchen"], 'badge' => 'Healthy']
              ]);
          }
        @endphp
        
        @foreach($popularItems as $item)
        <div class="popular-item">
          <div class="item-image">
            @if(isset($item->image) && $item->image)
              <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}">
            @else
              <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop" alt="{{ $item->name }}">
            @endif
            
            <div class="item-badge">{{ $item->badge ?? 'Popular' }}</div>
            <div class="item-rating">
              <svg class="star" viewBox="0 0 24 24" fill="currentColor">
                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
              </svg>
              <span style="font-size: 0.75rem; font-weight: 500;">4.8</span>
            </div>
          </div>
          
          <div class="item-content">
            <h3 class="item-title">{{ $item->name }}</h3>
            <p class="item-stall">{{ $item->stall->name }}</p>
            <div class="item-footer">
              <span class="item-price">₱{{ number_format($item->price / 100, 2) }}</span>
              <button class="add-btn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
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
          $featuredStalls = App\Models\Stall::where('is_active', true)->take(3)->get();
          
          if($featuredStalls->isEmpty()) {
              $featuredStalls = collect([
                  (object)['name' => "Tita's Kitchen", 'description' => 'Home-cooked Filipino meals with love. Specializing in traditional dishes that remind you of home.', 'location' => 'Main Canteen', 'id' => 1],
                  (object)['name' => 'Chowpan sa Binondo', 'description' => 'Authentic Chinese-Filipino fusion cuisine. Fresh ingredients, bold flavors, unbeatable prices.', 'location' => 'Food Court Level 2', 'id' => 2],
                  (object)['name' => 'Grill Master', 'description' => 'Sizzling grilled specialties and BBQ favorites. Perfect for meat lovers and group meals.', 'location' => 'Outdoor Terrace', 'id' => 3]
              ]);
          }
        @endphp
        
        @foreach($featuredStalls as $index => $stall)
        <div class="stall-card">
          <div class="stall-image">
            @if($index === 0)
              <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=500&h=300&fit=crop" alt="{{ $stall->name }}">
              <div class="stall-badge badge-24h">24H</div>
            @elseif($index === 1)
              <img src="https://images.unsplash.com/photo-1552611052-33e04de081de?w=500&h=300&fit=crop" alt="{{ $stall->name }}">
              <div class="stall-badge badge-24h">24H</div>
            @else
              <img src="https://images.unsplash.com/photo-1544025162-d76694265947?w=500&h=300&fit=crop" alt="{{ $stall->name }}">
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
              <div class="stall-rating">
                <svg class="star" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                </svg>
                <span style="font-size: 0.875rem; font-weight: 500;">4.8</span>
                <span style="font-size: 0.75rem; color: var(--gray-500);">(124)</span>
              </div>
            </div>

            <p class="stall-description">{{ $stall->description }}</p>

            <div class="stall-location">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              <span style="font-size: 0.875rem; color: var(--gray-500);">{{ $stall->location }}</span>
            </div>

            <div class="featured-items">
              <p class="featured-label">Popular Items:</p>
              <div class="featured-tags">
                @if($index === 0)
                  <span class="featured-tag">Adobo</span>
                  <span class="featured-tag">Sinigang</span>
                  <span class="featured-tag">Lechon Kawali</span>
                @elseif($index === 1)
                  <span class="featured-tag">Chowpan</span>
                  <span class="featured-tag">Siomai</span>
                  <span class="featured-tag">Beef Noodles</span>
                @else
                  <span class="featured-tag">Pork BBQ</span>
                  <span class="featured-tag">Grilled Chicken</span>
                  <span class="featured-tag">Liempo</span>
                @endif
              </div>
            </div>

            @if(method_exists($stall, 'id') && isset($stall->id))
              <a href="{{ route('stalls.show', $stall->id) }}" class="stall-btn">View Menu</a>
            @else
              <a href="{{ route('stalls.index') }}" class="stall-btn">View Menu</a>
            @endif
          </div>
        </div>
        @endforeach
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
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="M21 21l-4.35-4.35"/>
            </svg>
          </div>
          <div class="step-number">1</div>
          <h3 class="step-title">Browse</h3>
          <p class="step-description">Explore our diverse menu from multiple food stalls</p>
        </div>

        <div class="step-item">
          <div class="step-icon" style="background: #ffe6e6; color: #ef4444;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="10" rx="2" ry="2"/>
              <rect x="3" y="3" width="18" height="6" rx="2" ry="2"/>
            </svg>
          </div>
          <div class="step-number">2</div>
          <h3 class="step-title">Order</h3>
          <p class="step-description">Add your favorite items to cart and checkout</p>
        </div>

        <div class="step-item">
          <div class="step-icon" style="background: #e6f2ff; color: #3b82f6;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12,6 12,12 16,14"/>
            </svg>
          </div>
          <div class="step-number">3</div>
          <h3 class="step-title">Pay</h3>
          <p class="step-description">Secure payment options available</p>
        </div>

        <div class="step-item">
          <div class="step-icon" style="background: #e6fff2; color: #10b981;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
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
        target.scrollIntoView({ behavior: 'smooth' });
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