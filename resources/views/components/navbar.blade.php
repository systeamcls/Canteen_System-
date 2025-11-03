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
        z-index: 999;
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
        max-height: calc(100vh - 64px);
        overflow-y: auto;
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

    .navbar-mobile-logout {
        color: #dc2626 !important;
        background: none;
        border: none;
        width: 100%;
        font-family: inherit;
    }

    .navbar-mobile-logout:hover {
        background: #fef2f2 !important;
    }

    /* Better Mobile Responsiveness */
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

        /* Hide user dropdown on mobile ONLY */
        .user-dropdown {
            display: none !important;
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
            font-size: 16px;
            /* Smaller on mobile */
        }

        .navbar-brand-tagline {
            display: none;
            /* Hide tagline */
        }

        /* Hide user name and type on mobile */
        .user-info {
            display: none !important;
        }

        /* Make dropdown trigger compact */
        .dropdown-trigger {
            padding: 6px;
            gap: 0;
            min-width: auto;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
        }

        .navbar-actions {
            gap: 6px;
        }

        .navbar-mobile-content {
            padding: 12px 16px 20px;
        }
    }


    /* User Dropdown Styles */
    .user-dropdown {
        position: relative;
        display: inline-block;
        margin-left: auto;
        z-index: 10000;
        /* Push to right side */
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
        top: calc(100% + 8px);
        /* Small gap below button */
        right: 0;
        /* Align to right edge of parent */
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        min-width: 220px;
        max-width: 250px;
        padding: 8px 0;
        display: none;
        z-index: 9999;
        /* Higher than navbar */
        border: 1px solid #e5e7eb;
    }

    .dropdown-menu.show {
        display: block;
        animation: dropdownFadeIn 0.2s ease;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: var(--text-dark);
        text-decoration: none;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: #f9fafb;
    }

    .dropdown-item form {
        margin: 0;
        width: 100%;
    }

    .dropdown-item button {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        background: none;
        border: none;
        padding: 0;
        color: inherit;
        font: inherit;
        cursor: pointer;
        text-align: left;
    }

    .logout-btn {
        color: var(--text-dark);
    }

    .logout-btn:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .dropdown-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
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


    < !-- ALL WELCOME MODAL CSS --><style>

    /* Floating Label Styles */
    .floating-form-group {
        position: relative;
        margin-bottom: 24px;
    }

    .floating-input {
        width: 100%;
        padding: 20px 16px 8px 16px;
        border: 2px solid #fed7aa;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        outline: none;
    }

    .floating-input:focus {
        border-color: #FF6B47;
        box-shadow: 0 0 0 3px rgba(255, 107, 71, 0.1);
    }

    .floating-input.error {
        border-color: #ef4444;
    }

    .floating-label {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1rem;
        font-weight: 500;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        padding: 0 4px;
    }

    /* Float the label when input is focused or has value */
    .floating-input:focus+.floating-label,
    .floating-input:not(:placeholder-shown)+.floating-label {
        top: -10px;
        transform: translateY(0);
        left: 12px;
        font-size: 0.75rem;
        color: #FF6B47;
        font-weight: 600;
    }

    /* Error state */
    .floating-input.error:focus+.floating-label,
    .floating-input.error:not(:placeholder-shown)+.floating-label {
        color: #ef4444;
    }

    /* Password Toggle Eye Icon */
    .password-toggle-wrapper {
        position: relative;
    }

    .password-toggle-btn {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #9ca3af;
        transition: color 0.2s ease;
        z-index: 5;
    }

    .password-toggle-btn:hover {
        color: #FF6B47;
    }

    .password-toggle-btn svg {
        width: 20px;
        height: 20px;
    }

    /* Adjust floating input for password toggle button */
    .password-toggle-wrapper .floating-input {
        padding-right: 45px;
    }

    /* Compact Password Requirements (Pills/Badges) */
    .password-requirements {
        margin-top: 8px;
        margin-bottom: 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .password-requirements.show {
        opacity: 1;
        max-height: 200px;
    }

    .password-requirements .req-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        font-size: 0.7rem;
        border-radius: 12px;
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .password-requirements .req-badge.valid {
        background: #d1fae5;
        color: #065f46;
        border-color: #10b981;
    }

    .password-requirements .req-badge.invalid {
        background: #fee2e2;
        color: #991b1b;
        border-color: #ef4444;
    }

    .password-requirements .req-badge::before {
        content: '○';
        font-size: 0.6rem;
    }

    .password-requirements .req-badge.valid::before {
        content: '✓';
        font-weight: bold;
    }

    .password-requirements .req-badge.invalid::before {
        content: '✗';
    }

    /* Back Button in Header - Top Left */
    .back-btn-header {
        position: absolute;
        top: 16px;
        left: 16px;
        background: none;
        border: none;
        color: #FF6B47;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
        z-index: 10;
    }

    .back-btn-header:hover {
        background: #fff5f2;
        color: #ea580c;
    }

    .back-btn-header svg {
        transition: transform 0.2s ease;
    }

    .back-btn-header:hover svg {
        transform: translateX(-2px);
    }

    /* Adjust modal header for back button */
    .modal-header {
        position: relative;
        padding: 60px 60px 0 60px;
        text-align: center;
    }

    /* Make close button align with back button */
    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: none;
        border: none;
        font-size: 28px;
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
        color: #FF6B47;
    }

    /* Tailwind utility classes */
    .text-center {
        text-align: center;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-gray-600 {
        color: #4b5563;
    }

    .text-blue-600 {
        color: #2563eb;
    }

    .hover\:text-blue-800:hover {
        color: #1e40af;
    }

    .font-medium {
        font-weight: 500;
    }

    /* ========== COMPACT REGISTRATION FORM ========== */

    /* Simple Welcome Header (no animation, no emoji) */
    .welcome-header-simple {
        text-align: center;
        margin-bottom: 20px;
    }

    .welcome-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .welcome-subtitle {
        font-size: 0.85rem;
        color: #6b7280;
    }

    /* Progress Indicator */
    .progress-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        padding: 12px 0;
    }

    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .progress-dot {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .progress-step.active .progress-dot {
        background: linear-gradient(135deg, #FF6B47, #ea580c);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 107, 71, 0.4);
    }

    .progress-step span {
        font-size: 0.7rem;
        color: #9ca3af;
        font-weight: 600;
    }

    .progress-step.active span {
        color: #FF6B47;
    }

    .progress-line {
        width: 40px;
        height: 2px;
        background: #e5e7eb;
        margin: 0 8px;
        margin-bottom: 20px;
    }

    /* Icons Inside Inputs */
    .input-with-icon {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.2rem;
        z-index: 1;
        pointer-events: none;
    }

    .input-with-icon .floating-input {
        padding-left: 48px;
    }

    .input-with-icon .floating-label {
        left: 48px;
    }

    .input-with-icon .floating-input:focus+.floating-label,
    .input-with-icon .floating-input:not(:placeholder-shown)+.floating-label {
        left: 12px;
    }

    /* Password toggle adjustment for icon inputs */
    .input-with-icon.password-toggle-wrapper .floating-input {
        padding-left: 48px;
        padding-right: 45px;
    }

    /* Compact form groups - less margin */
    .floating-form-group {
        margin-bottom: 18px;
    }

    /* Modern Submit Button */
    .submit-btn-modern {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #FF6B47 0%, #ea580c 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(255, 107, 71, 0.3);
        margin-top: 8px;
    }

    .submit-btn-modern:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(255, 107, 71, 0.4);
    }

    .submit-btn-modern:active {
        transform: translateY(0);
    }

    .submit-btn-modern .btn-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .submit-btn-modern .btn-icon {
        font-size: 1.3rem;
    }

    .submit-btn-modern .btn-arrow {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .submit-btn-modern:hover .btn-arrow {
        transform: translateX(4px);
    }

    /* Loading Spinner */
    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Already Have Account Link */
    .already-have-account {
        text-align: center;
        margin-top: 16px;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .link-btn {
        background: none;
        border: none;
        color: #2563eb;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
        transition: color 0.2s ease;
        padding: 0;
    }

    .link-btn:hover {
        color: #1e40af;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .progress-dot {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .progress-step span {
            font-size: 0.65rem;
        }

        .progress-line {
            width: 30px;
        }
    }

    /* Waving Hand Peeking from Top of Modal - Centered & Higher */
    .wave-peek {
        position: absolute;
        top: -40px;
        /* Raised higher (was -30px) */
        left: 50%;
        /* Center horizontally */
        transform: translateX(-50%);
        /* Perfect centering */
        font-size: 3.5rem;
        animation: wave-peek 1.8s ease-in-out infinite;
        z-index: 100;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
    }

    @keyframes wave-peek {

        0%,
        100% {
            transform: translateX(-50%) rotate(0deg) translateY(0px);
        }

        10%,
        30% {
            transform: translateX(-50%) rotate(14deg) translateY(-3px);
        }

        20%,
        40% {
            transform: translateX(-50%) rotate(-8deg) translateY(0px);
        }

        50% {
            transform: translateX(-50%) rotate(14deg) translateY(-3px);
        }

        60% {
            transform: translateX(-50%) rotate(-4deg) translateY(0px);
        }

        70% {
            transform: translateX(-50%) rotate(10deg) translateY(-2px);
        }

        80% {
            transform: translateX(-50%) rotate(0deg) translateY(0px);
        }
    }

    /* Make sure modal-container has overflow visible for the hand */
    .modal-container {
        position: relative;
        overflow: visible;
        /* Allow hand to peek out */
    }

    /* Adjust modal header spacing for wave */
    .modal-header {
        position: relative;
        padding: 60px 60px 0 60px;
        text-align: center;
    }

    .modal-title {
        margin-top: 0;
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6" />
                </svg>
                Home
            </a>
            <a href="{{ route('menu.index') }}" class="navbar-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Menu
            </a>
            <a href="{{ route('stalls.index') }}"
                class="navbar-link {{ request()->routeIs('stalls.*') ? 'active' : '' }}">
                <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
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
                    @if (session('user_type') === 'guest')
                        <div class="user-avatar">👤</div>
                    @elseif(session('user_type') === 'employee' && Auth::check())
                        @if (Auth::user()->profile_photo_path)
                            <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="Profile Picture"
                                id="navbar-user-avatar" class="user-avatar">
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
                            @if (session('user_type') === 'guest')
                                Guest User
                            @elseif(session('user_type') === 'employee')
                                {{ Auth::user()->name ?? 'User' }}
                            @else
                                Guest User
                            @endif
                        </span>
                        <span class="user-type">
                            @if (session('user_type') === 'guest')
                                Guest
                            @elseif(session('user_type') === 'employee')
                                Employee
                            @else
                                Guest
                            @endif
                        </span>
                    </div>
                    <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </div>

                <div class="dropdown-menu" id="userDropdownMenu">
                    @if (session('user_type') === 'employee' && Auth::check())
                        <!-- Employee options -->
                        <a href="{{ route('user.profile.show') }}" class="dropdown-item">
                            <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('employee.logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout-btn">
                                <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    @else
                        <!-- Guest options -->
                        <button class="dropdown-item" onclick="openWelcomeModal()">
                            <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Log In
                        </button>
                    @endif
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button class="navbar-mobile-toggle" onclick="toggleNavbarMobileMenu()">
                <svg class="navbar-hamburger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="navbar-mobile-menu" id="navbarMobileNav">
        <div class="navbar-mobile-content">
            <nav class="navbar-mobile-nav">
                <a href="{{ route('home.index') }}"
                    class="navbar-mobile-link {{ request()->routeIs('home.*') ? 'active' : '' }}">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6" />
                    </svg>
                    Home
                </a>
                <a href="{{ route('menu.index') }}"
                    class="navbar-mobile-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Menu
                </a>
                <a href="{{ route('stalls.index') }}"
                    class="navbar-mobile-link {{ request()->routeIs('stalls.*') ? 'active' : '' }}">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Stalls
                </a>
            </nav>

            <!-- Profile link added to mobile menu -->
            @if (session('user_type') === 'employee' && Auth::check())
                <a href="{{ route('user.profile.show') }}"
                    class="navbar-mobile-link {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z" />
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('employee.logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="navbar-mobile-link navbar-mobile-logout"
                        onclick="return confirm('Are you sure you want to sign out?')">
                        <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            @else
                <a href="javascript:void(0)" onclick="openWelcomeModal()" class="navbar-mobile-link">
                    <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z" />
                    </svg>
                    Login
                </a>
            @endif
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

    // Mobile functions
    function toggleNavbarMobileMenu() {
        const mobileMenu = document.getElementById('navbarMobileNav');
        mobileMenu.classList.toggle('show');
    }

    function handleMobileUserClick() {
        console.log('Mobile user click - user type:', '{{ session('user_type') }}');
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
