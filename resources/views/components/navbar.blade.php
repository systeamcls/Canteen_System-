{{-- navbar.blade.php - With Profile Picture Support --}}
<style>
:root {
    --primary-orange: #FF6B47;
    --text-dark: #333333;
    --text-muted: #6B7280;
    --border: #E5E7EB;
    --white: #FFFFFF;
}

.navbar-component {
    background: var(--white);
    border-bottom: 1px solid var(--border);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    height: 64px;
}

.navbar-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 64px;
    position: relative;
}

/* Logo Section */
.navbar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.navbar-logo-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-orange);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 16px;
    letter-spacing: 0.5px;
}

.navbar-brand-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.navbar-brand-name {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1;
    margin: 0;
}

.navbar-brand-tagline {
    font-size: 13px;
    color: var(--text-muted);
    font-weight: 400;
    line-height: 1;
    margin: 0;
}

/* Desktop Navigation */
.navbar-nav {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: 48px;
}

.navbar-link {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: var(--text-muted);
    font-weight: 500;
    font-size: 15px;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.navbar-link:hover {
    color: var(--text-dark);
    background: #F9FAFB;
}

.navbar-link.active {
    color: var(--text-dark);
    background: #F3F4F6;
}

.navbar-icon {
    width: 18px;
    height: 18px;
    color: currentColor;
}

/* Right Section */
.navbar-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 24px;
}

.navbar-search {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    font-weight: 500;
    font-size: 15px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.navbar-search:hover {
    color: var(--text-dark);
    background: #F9FAFB;
}

.navbar-cart {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.navbar-cart:hover {
    background: #F9FAFB;
}

.navbar-cart-icon {
    width: 20px;
    height: 20px;
    color: var(--text-muted);
}

.navbar-cart-badge {
    position: absolute;
    top: 2px;
    right: 6px;
    background: var(--primary-orange);
    color: white;
    font-size: 12px;
    font-weight: 600;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.navbar-user {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    font-weight: 500;
    font-size: 15px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.navbar-user:hover {
    color: var(--text-dark);
    background: #F9FAFB;
}

.navbar-user-icon {
    width: 18px;
    height: 18px;
    color: currentColor;
}

/* Mobile Menu Toggle */
.navbar-mobile-toggle {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: background 0.2s ease;
}

.navbar-mobile-toggle:hover {
    background: #F9FAFB;
}

.navbar-hamburger {
    width: 20px;
    height: 20px;
    color: var(--text-dark);
}

/* Mobile Navigation */
.navbar-mobile-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border-bottom: 1px solid var(--border);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.navbar-mobile-menu.show {
    display: block;
}

.navbar-mobile-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 16px 24px 24px;
}

.navbar-mobile-nav {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 20px;
}

.navbar-mobile-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: var(--text-muted);
    font-weight: 500;
    font-size: 15px;
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.navbar-mobile-link:hover,
.navbar-mobile-link.active {
    color: var(--text-dark);
    background: #F3F4F6;
}

.navbar-mobile-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 16px;
    border-top: 1px solid var(--border);
}

.navbar-mobile-user {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    font-weight: 500;
    font-size: 15px;
}

.navbar-mobile-cart {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.navbar-mobile-cart:hover {
    background: #F9FAFB;
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar-container {
        display: flex;
        justify-content: space-between;
        padding: 0 16px;
    }
    
    .navbar-nav {
        display: none;
    }
    
    .navbar-search {
        display: none;
    }
    
    .navbar-mobile-toggle {
        display: block;
    }
    
    .navbar-actions {
        justify-content: flex-end;
        gap: 12px;
    }
}

@media (max-width: 480px) {
    .navbar-container {
        padding: 0 12px;
    }
    
    .navbar-logo-icon {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
    
    .navbar-brand-name {
        font-size: 18px;
    }
    
    .navbar-brand-tagline {
        font-size: 12px;
    }
    
    .navbar-mobile-content {
        padding: 12px 16px 20px;
    }
}

@media (max-width: 360px) {
    .navbar-brand-tagline {
        display: none;
    }
}

/* User Dropdown Styles */
.user-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--white);
    border: 2px solid var(--border);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.dropdown-trigger:hover {
    border-color: var(--primary-orange);
    background: #fff5f2;
}

.user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--primary-orange);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
    object-fit: cover;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 500;
    color: var(--text-dark);
    font-size: 14px;
    line-height: 1;
}

.user-type {
    font-size: 12px;
    color: var(--text-muted);
    line-height: 1;
}

.dropdown-arrow {
    transition: transform 0.2s ease;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    min-width: 180px;
    z-index: 9999;
    display: none;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: var(--text-dark);
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    font-size: 14px;
    cursor: pointer;
}

.dropdown-item:hover {
    background: #f9fafb;
}

.logout-btn:hover {
    background: #fef2f2;
    color: #dc2626;
}

.dropdown-icon {
    width: 16px;
    height: 16px;
}
</style>

<nav class="navbar-component">
    <div class="navbar-container">
        <!-- Logo -->
        <a href="{{ route('home.index') }}" class="navbar-logo">
            <div class="navbar-logo-icon">CC</div>
            <div class="navbar-brand-text">
                <div class="navbar-brand-name">Canteen Central</div>
                <div class="navbar-brand-tagline">Fresh • Fast • Delicious</div>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="navbar-nav">
            <a href="{{ route('home.index') }}" class="navbar-link {{ request()->routeIs('home.*') ? 'active' : '' }}">
                <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>
                </svg>
                Home
            </a>
            <a href="{{ route('menu.index') }}" class="navbar-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Menu
            </a>
            <a href="{{ route('stalls.index') }}" class="navbar-link {{ request()->routeIs('stalls.*') ? 'active' : '' }}">
                <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                Stalls
            </a>
        </nav>

        <!-- Right Actions -->
        <div class="navbar-actions">
            <!-- Cart Component -->
            @livewire('cart-panel')

            <!-- User Dropdown with Profile Picture Support -->
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-trigger" onclick="toggleUserDropdown()">
                    <!-- Updated Avatar with Profile Picture Support -->
                    @if(session('user_type') === 'guest')
                        <div class="user-avatar">👤</div>
                    @elseif(session('user_type') === 'employee' && Auth::check())
                        @if(Auth::user()->profile_photo_path)
                            <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" 
                                 alt="Profile Picture" 
                                 id="navbar-user-avatar"
                                 class="user-avatar">
                        @else
                            <div class="user-avatar" id="navbar-user-avatar-initials">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                            </div>
                        @endif
                    @else
                        <div class="user-avatar">👤</div>
                    @endif
                    
                    <div class="user-info">
                        <span class="user-name">
                            @if(session('user_type') === 'guest')
                                Guest User
                            @elseif(session('user_type') === 'employee')
                                {{ Auth::user()->name ?? 'User' }}
                            @else
                                Guest User
                            @endif
                        </span>
                        <span class="user-type">
                            @if(session('user_type') === 'guest')
                                Guest
                            @elseif(session('user_type') === 'employee')
                                Employee
                            @else
                                Guest
                            @endif
                        </span>
                    </div>
                    <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </div>
                
                <div class="dropdown-menu" id="userDropdownMenu">
                    @if(session('user_type') === 'employee' && Auth::check())
                        <!-- Employee options -->
                        <a href="{{ route('user.profile.show') }}" class="dropdown-item">
                            <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout-btn" onclick="return confirm('Are you sure you want to sign out?')">
                                <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    @else
                        <!-- Guest options -->
                        <button class="dropdown-item" onclick="openLoginModal()">
                            <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Log In
                        </button>
                    @endif
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button class="navbar-mobile-toggle" onclick="toggleNavbarMobileMenu()">
                <svg class="navbar-hamburger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="navbar-mobile-menu" id="navbarMobileNav">
        <div class="navbar-mobile-content">
            <nav class="navbar-mobile-nav">
                <a href="{{ route('home.index') }}" class="navbar-mobile-link {{ request()->routeIs('home.*') ? 'active' : '' }}">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>
                    </svg>
                    Home
                </a>
                <a href="{{ route('menu.index') }}" class="navbar-mobile-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Menu
                </a>
                <a href="{{ route('stalls.index') }}" class="navbar-mobile-link {{ request()->routeIs('stalls.*') ? 'active' : '' }}">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    Stalls
                </a>
            </nav>

            <div class="navbar-mobile-actions">
                <div class="navbar-mobile-user" onclick="handleMobileUserClick()">
                    <svg class="navbar-user-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/>
                    </svg>
                    @if(session('user_type') === 'guest')
                        Guest
                    @elseif(session('user_type') === 'employee')
                        Employee
                    @else
                        Login
                    @endif
                </div>
                
                @livewire('cart-panel')
            </div>
        </div>
    </div>
</nav>

<script>
function toggleUserDropdown() {
    console.log('toggleUserDropdown called');
    const menu = document.getElementById('userDropdownMenu');
    const arrow = document.querySelector('.dropdown-arrow');
    
    console.log('Menu element found:', !!menu);
    console.log('Arrow element found:', !!arrow);
    
    if (menu && arrow) {
        menu.classList.toggle('show');
        const isShowing = menu.classList.contains('show');
        console.log('Menu is now showing:', isShowing);
        
        if (isShowing) {
            arrow.style.transform = 'rotate(180deg)';
        } else {
            arrow.style.transform = 'rotate(0deg)';
        }
    } else {
        console.error('Missing elements - Menu:', !!menu, 'Arrow:', !!arrow);
    }
}

function openLoginModal() {
    console.log('Direct modal control...');
    
    // Find the modal element
    const modal = document.querySelector('.modal-overlay');
    
    if (modal) {
        // Force the modal to show
        modal.style.display = 'flex';
        modal.style.opacity = '1'; 
        modal.style.visibility = 'visible';
        modal.style.zIndex = '10000';
        modal.classList.add('active');
        
        console.log('Modal opened via direct DOM manipulation');
        
        // Also trigger the Livewire state change
        setTimeout(() => {
            if (window.Livewire) {
                window.Livewire.dispatch('openWelcomeModal');
            }
        }, 50);
        
    } else {
        console.error('Modal element not found in DOM');
    }
}

// Mobile functions
function toggleNavbarMobileMenu() {
    const mobileMenu = document.getElementById('navbarMobileNav');
    mobileMenu.classList.toggle('show');
}

function handleMobileUserClick() {
    console.log('Mobile user click - user type:', '{{ session("user_type") }}');
    openLoginModal();
}

// Event listeners
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const menu = document.getElementById('userDropdownMenu');
    const arrow = document.querySelector('.dropdown-arrow');
    
    if (dropdown && menu && arrow && !dropdown.contains(event.target)) {
        menu.classList.remove('show');
        arrow.style.transform = 'rotate(0deg)';
    }
});

// Function to update navbar avatar - called from profile page
function updateNavbarAvatar(imageUrl) {
    const avatarElement = document.getElementById('navbar-user-avatar');
    const initialsElement = document.getElementById('navbar-user-avatar-initials');
    
    if (initialsElement) {
        // Replace initials with image
        const imgElement = document.createElement('img');
        imgElement.src = imageUrl;
        imgElement.alt = 'Profile Picture';
        imgElement.id = 'navbar-user-avatar';
        imgElement.className = 'user-avatar';
        
        initialsElement.parentNode.replaceChild(imgElement, initialsElement);
    } else if (avatarElement) {
        // Update existing image
        avatarElement.src = imageUrl;
    }
}

// Test function to check if elements exist
function debugElements() {
    console.log('=== Debug Elements ===');
    console.log('userDropdown:', !!document.getElementById('userDropdown'));
    console.log('userDropdownMenu:', !!document.getElementById('userDropdownMenu'));
    console.log('dropdown-arrow:', !!document.querySelector('.dropdown-arrow'));
    console.log('dropdown-trigger:', !!document.querySelector('.dropdown-trigger'));
}

// Run debug after page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, running debug...');
    debugElements();
});
</script>