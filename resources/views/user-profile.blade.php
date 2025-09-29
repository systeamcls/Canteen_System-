<x-layouts.profile>

    <style>
        :root {
            --primary-orange: #FF6B35;
            --secondary-orange: #FF8A6B;
            --accent-orange: #E55B2B;
            --light-orange: #FFF4F1;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --text-light: #9CA3AF;
            --border: #E5E7EB;
            --background: #F9FAFB;
            --white: #FFFFFF;
            --success: #10B981;
            --error: #EF4444;
            --warning: #F59E0B;
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-lg: rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        /* Desktop Layout (Default) */
        .mobile-container { display: none; }
        .desktop-container { display: block; }

        /* Desktop Profile Header */
        .desktop-profile-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-orange) 100%);
            color: white;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.3);
        }

        .desktop-profile-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .desktop-profile-info {
            display: flex;
            align-items: center;
            gap: 2rem;
            position: relative;
            z-index: 1;
        }

        .desktop-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            border: 4px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .desktop-user-info h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .online-indicator {
            width: 16px;
            height: 16px;
            background: var(--success);
            border-radius: 50%;
            border: 3px solid white;
        }

        .desktop-user-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .desktop-contact-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 1rem;
            opacity: 0.9;
        }

        .desktop-contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .desktop-contact-item i { width: 20px; text-align: center; }

        /* Desktop Navigation */
        .desktop-nav-tabs {
            background: white;
            border-radius: 20px;
            padding: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px var(--shadow);
            display: flex;
            gap: 0.5rem;
        }

        .desktop-nav-tab {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .desktop-nav-tab.active {
            background: var(--primary-orange);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
        }

        .desktop-nav-tab .badge {
            background: var(--primary-orange);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }

        .desktop-nav-tab.active .badge {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Desktop Content */
        .desktop-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--shadow);
            overflow: hidden;
        }

        .desktop-tab-content {
            display: none;
            padding: 2rem;
        }

        .desktop-tab-content.active { display: block; }

        /* Orders Summary */
        .orders-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            text-align: center;
            padding: 2rem;
            border-radius: 16px;
            background: var(--background);
            border: 2px solid var(--border);
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            border-color: var(--primary-orange);
            transform: translateY(-4px);
        }

        .summary-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .summary-icon.orange { background: var(--light-orange); color: var(--primary-orange); }
        .summary-icon.blue { background: #EBF8FF; color: #3B82F6; }
        .summary-icon.green { background: #D1FAE5; color: var(--success); }

        .summary-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .summary-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Order History */
        .order-history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .section-subtitle {
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .view-all-btn {
            background: var(--light-orange);
            color: var(--primary-orange);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .view-all-btn:hover {
            background: var(--primary-orange);
            color: white;
        }

        /* Order Items */
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .order-item:hover {
            border-color: var(--primary-orange);
            background: var(--light-orange);
        }

        .order-info h4 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .order-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .order-meta i { width: 14px; }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending { background: #FEF3C7; color: #92400E; }
        .status-processing { background: #DBEAFE; color: #1E40AF; }
        .status-completed { background: #D1FAE5; color: #065F46; }
        .status-cancelled { background: #FEE2E2; color: #991B1B; }
        .status-delivered { background: var(--primary-orange); color: white; }
        .status-shipped { background: var(--background); color: var(--text-muted); border: 1px solid var(--border); }

        /* Recent Order Updates */
        .recent-updates-section {
            background: var(--background);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid var(--border);
        }

        .recent-updates-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .recent-updates-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--light-orange);
            color: var(--primary-orange);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .recent-update-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }

        .recent-update-item:last-child { border-bottom: none; }

        .recent-update-info h5 {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .recent-update-date {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .recent-update-amount {
            font-weight: 700;
            color: var(--text-dark);
            margin-left: 1rem;
        }

        /* Forms */
        .profile-card {
            background: var(--background);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .card-description {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .form-group { margin-bottom: 1.5rem; }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-orange);
        }

        .form-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        .form-grid-2 { grid-template-columns: 1fr 1fr; }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary { background: var(--primary-orange); color: white; }
        .btn-primary:hover { background: var(--accent-orange); }

        /* Mobile Layout */
        @media (max-width: 1024px) {
            .desktop-container { display: none; }
            .mobile-container { display: block; }

            .mobile-header {
                background: var(--white);
                padding: 1rem 1.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 2px 10px var(--shadow);
                position: sticky;
                top: 0;
                z-index: 100;
            }

            .back-btn {
                background: none;
                border: none;
                font-size: 1.25rem;
                color: var(--text-dark);
                cursor: pointer;
            }

            .header-title {
                font-size: 1.125rem;
                font-weight: 700;
                color: var(--text-dark);
            }

            .mobile-profile-card {
                background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
                padding: 2rem 1.5rem;
                color: white;
                position: relative;
                overflow: hidden;
            }

            .mobile-profile-card::before {
                content: '';
                position: absolute;
                top: -30px;
                right: -30px;
                width: 150px;
                height: 150px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
            }

            .mobile-profile-info {
                display: flex;
                align-items: center;
                gap: 1.5rem;
                position: relative;
                z-index: 1;
            }

            .mobile-avatar {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                font-weight: 700;
                color: white;
                border: 3px solid rgba(255, 255, 255, 0.3);
            }

            .mobile-user-details h1 {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 0.25rem;
            }

            .mobile-user-badge {
                background: rgba(255, 255, 255, 0.2);
                padding: 0.25rem 0.75rem;
                border-radius: 12px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                margin-bottom: 0.5rem;
                display: inline-block;
            }

            .mobile-contact-info { font-size: 0.875rem; opacity: 0.9; }

            .mobile-nav {
                background: white;
                padding: 1rem 1.5rem;
                box-shadow: 0 2px 10px var(--shadow);
            }

            .mobile-nav-tabs {
                display: flex;
                gap: 0.5rem;
                overflow-x: auto;
            }

            .mobile-nav-tab {
                padding: 0.75rem 1.5rem;
                border: none;
                background: var(--background);
                color: var(--text-muted);
                font-weight: 600;
                border-radius: 20px;
                cursor: pointer;
                white-space: nowrap;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .mobile-nav-tab.active {
                background: var(--primary-orange);
                color: white;
            }

            .mobile-content { padding: 1.5rem; }
            .mobile-tab-content { display: none; }
            .mobile-tab-content.active { display: block; }

            .mobile-card {
                background: white;
                border-radius: 16px;
                padding: 1.5rem;
                margin-bottom: 1rem;
                box-shadow: 0 4px 15px var(--shadow);
            }

            @media (max-width: 768px) {
                .form-grid-2 { grid-template-columns: 1fr; }
                .desktop-profile-info { flex-direction: column; text-align: center; gap: 1rem; }
                .orders-summary { grid-template-columns: 1fr; gap: 1rem; }
            }
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #D1FAE5;
            border: 1px solid #A7F3D0;
            color: #065F46;
        }

        .alert-error {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }

        /* Animations */
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .profile-picture-container:hover .upload-overlay {
    opacity: 1 !important;
}

.mobile-profile-picture-container:active .mobile-upload-overlay,
.mobile-profile-picture-container:hover .mobile-upload-overlay {
    opacity: 1 !important;
}

/* Loading spinner */
.upload-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 107, 53, 0.9);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    z-index: 10;
}

.upload-loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@media (max-width: 1024px) {
    .mobile-card .order-item:hover {
        background: var(--light-orange);
        transition: background 0.2s ease;
    }
    
    .mobile-card .order-item:active {
        transform: scale(0.98);
        transition: transform 0.1s ease;
    }
}   
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(!auth()->user()->hasVerifiedEmail())
<div class="alert alert-warning" style="margin-bottom: 2rem;">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Email Not Verified</strong> - Please verify your email to access checkout and place orders.
    <a href="{{ route('verification.notice') }}" class="btn btn-primary" style="margin-left: 1rem;">
        Verify Email
    </a>
</div>
@endif
            <!-- Desktop Layout -->
            <div class="desktop-container">
                <!-- Desktop Profile Header with Upload -->
<div class="desktop-profile-header fade-in">
    <!-- Close Button -->
    <button onclick="goToMenu()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: rgba(255, 255, 255, 0.2); border: none; color: white; padding: 0.75rem; border-radius: 50%; cursor: pointer; transition: all 0.2s ease; z-index: 2;">
        <i class="fas fa-times" style="font-size: 1.125rem;"></i>
    </button>
    
    <div class="desktop-profile-info">
        <!-- Avatar with Upload Functionality -->
        <div class="profile-picture-container" style="position: relative; cursor: pointer;" onclick="triggerFileUpload()">
            @if($user->profile_photo_path)
                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                     alt="Profile Picture" 
                     id="header-profile-avatar-img"
                     class="desktop-avatar"
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; background: rgba(236, 232, 232, 0.2); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: white; border: 4px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);">
            @else
                <div id="header-profile-avatar-initials" class="desktop-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            @endif
            
            <!-- Upload Overlay -->
            <div class="upload-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; color: white;">
                <i class="fas fa-camera" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                <span style="font-size: 0.875rem; font-weight: 600;">Change Photo</span>
            </div>
        </div>
        
        <!-- Hidden File Input -->
        <input type="file" id="profile-picture-input" accept="image/*" style="display: none;">
        
        <div class="desktop-user-info">
            <h1>
                {{ $user->name }}
                <div class="online-indicator"></div>
            </h1>
            <div class="desktop-user-badge">{{ ucfirst($user->type ?? 'Customer') }}</div>
            <div class="desktop-contact-info">
                <div class="desktop-contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $user->email }}</span>
                </div>
                @if($user->phone)
                <div class="desktop-contact-item">
                    <i class="fas fa-phone"></i>
                    <span>{{ $user->phone }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

                <!-- Desktop Navigation -->
                <div class="desktop-nav-tabs fade-in">
                    <button class="desktop-nav-tab active" onclick="showDesktopTab('profile')">
                        <i class="fas fa-user"></i>
                        Profile
                    </button>
                    <button class="desktop-nav-tab" onclick="showDesktopTab('orders')">
                        <i class="fas fa-receipt"></i>
                        Orders
                        <span class="badge">{{ $user->orders()->count() }}</span>
                    </button>
                    <button class="desktop-nav-tab" onclick="showDesktopTab('settings')">
                        <i class="fas fa-cog"></i>
                        Settings
                    </button>
                </div>

                <!-- Desktop Content -->
                <div class="desktop-content fade-in">
                    <!-- Profile Tab (Display Only) -->
                    <div class="desktop-tab-content active" id="desktop-profile-tab">
                        <!-- Enhanced Profile Information Display (Visual Only) -->
                        <div class="profile-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e5e7eb; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);">
                            <!-- Background Pattern -->
                            <div style="position: absolute; top: 0; right: 0; width: 150px; height: 150px; background: radial-gradient(circle, rgba(255, 107, 53, 0.05) 0%, transparent 70%); pointer-events: none;"></div>
                            
                            <div class="recent-updates-header" style="border-bottom: 1px solid #f3f4f6; padding-bottom: 1.5rem; margin-bottom: 2rem;">
                                <div class="recent-updates-icon" style="background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-orange) 100%); box-shadow: 0 8px 20px rgba(255, 107, 53, 0.25);">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h3 class="card-title" style="margin-bottom: 0.5rem; font-size: 1.5rem;">Profile Information</h3>
                                    <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">Your personal details and account information</p>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 2.5rem; margin-bottom: 2rem; position: relative; z-index: 1;">
                                <!-- Enhanced Avatar -->
                                <div style="position: relative;">
                                    <div class="desktop-avatar" style="width: 120px; height: 120px; font-size: 2.5rem; border-radius: 20px; background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%); box-shadow: 0 12px 25px rgba(255, 107, 53, 0.3); border: 4px solid white; position: relative; overflow: hidden;">
                                        <div style="position: absolute; inset: 0; background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%); animation: shimmer 3s infinite;"></div>
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    
                                    <!-- Online Status -->
                                    <div style="position: absolute; bottom: 8px; right: 8px; width: 24px; height: 24px; background: var(--success); border: 4px solid white; border-radius: 50%; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); animation: pulse 2s infinite;"></div>
                                    
                                    <!-- User Badge -->
                                    <div style="position: absolute; bottom: -12px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-orange) 100%); color: white; padding: 0.375rem 1rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4); border: 3px solid white;">
                                        {{ strtoupper($user->type ?? 'EMPLOYEE') }}
                                    </div>
                                </div>
                                
                                <!-- Enhanced Contact Info -->
                                <div style="flex: 1;">
                                    <!-- Name with gradient -->
                                    <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-dark); margin: 0 0 1.5rem 0; background: linear-gradient(135deg, var(--text-dark) 0%, var(--primary-orange) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $user->name }}</h2>
                                    
                                    <!-- Contact Items -->
                                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                                        <!-- Email -->
                                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 12px; border-left: 4px solid var(--primary-orange);">
                                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 0.25rem;">Email Address</div>
                                                <div style="color: var(--text-dark); font-weight: 600;">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Phone -->
                                        @if($user->phone)
                                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; border-left: 4px solid var(--success);">
                                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--success) 0%, #059669 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 0.25rem;">Phone Number</div>
                                                <div style="color: var(--text-dark); font-weight: 600;">{{ $user->phone }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-radius: 12px; border-left: 4px solid var(--warning);">
                                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                                                <i class="fas fa-phone-slash"></i>
                                            </div>
                                            <div>
                                                <div style="font-size: 0.75rem; color: #92400e; text-transform: uppercase; font-weight: 600; margin-bottom: 0.25rem;">Phone Number</div>
                                                <div style="color: #92400e; font-weight: 500;">Not provided</div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Order Updates Section -->
                        <div class="recent-updates-section">
                            <div class="recent-updates-header">
                                <div class="recent-updates-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3 class="card-title">Recent Order Updates</h3>
                            </div>

                            @forelse($user->orders()->latest()->take(3)->get() as $order)
                            <div class="recent-update-item">
                                <div class="recent-update-info">
                                    <h5>{{ $order->order_number }}</h5>
                                    <div class="recent-update-date">{{ $order->created_at->format('Y-m-d') }}</div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="order-status status-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</div>
                                    <div class="recent-update-amount">₱{{ number_format($order->orderGroup ? $order->orderGroup->amount_total / 100 : $order->orderItems->sum('line_total') / 100, 2) }}</div>
                                </div>
                            </div>
                            @empty
                            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                <i class="fas fa-receipt" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem;"></i>
                                <p>No recent order updates to display.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="desktop-tab-content" id="desktop-orders-tab">
                        <div class="orders-summary">
                            <div class="summary-card">
                                <div class="summary-icon orange">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="summary-number">{{ $user->orders()->count() }}</div>
                                <div class="summary-label">Total Orders</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon blue">
                                    <i class="fas fa-peso-sign"></i>
                                </div>
                                <div class="summary-number">₱{{ number_format($user->orderGroups()->sum('amount_total') / 100, 0) }}</div>
                                <div class="summary-label">Total Spent</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-icon green">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="summary-number">{{ $user->orders()->where('status', 'completed')->count() }}</div>
                                <div class="summary-label">Completed</div>
                            </div>
                        </div>

                        <div class="order-history-header">
                            <div>
                                <h2 class="section-title">Order History</h2>
                                <p class="section-subtitle">Track and manage your recent orders</p>
                            </div>
                            <a href="#" class="view-all-btn">View All</a>
                        </div>

                        @forelse($user->orders()->latest()->take(10)->get() as $order)
                        <div class="order-item" data-order-id="{{ $order->id }}" onclick="window.location.href='{{ route('order.detail', $order->id) }}'" style="cursor: pointer;">
                            <div class="order-info">
                                <h4>{{ $order->order_number }}</h4>
                                <div class="order-meta">
                                    <span><i class="fas fa-calendar"></i> {{ $order->created_at->format('M d, Y • g:i A') }}</span>
                                    <span><i class="fas fa-peso-sign"></i> ₱{{ number_format($order->orderGroup ? $order->orderGroup->amount_total / 100 : ($order->orderItems->sum('line_total') / 100), 2) }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="order-status status-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                        @empty
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fas fa-shopping-bag" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <h3>No Orders Yet</h3>
                            <p>Start exploring our menu and place your first order!</p>
                            <a href="{{ route('menu.index') }}" class="view-all-btn" style="margin-top: 1rem; display: inline-block;">Browse Menu</a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Settings Tab (Now Contains All Editing Forms) -->
                    <div class="desktop-tab-content" id="desktop-settings-tab">
                        <!-- Personal Information Editing -->
                        <div class="profile-card">
                            <h3 class="card-title">Personal Information</h3>
                            <p class="card-description">Update your personal details</p>

                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-error">
                                    <ul style="margin: 0; padding-left: 1rem;">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-grid form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">User Type</label>
                                        <input type="text" class="form-input" value="{{ ucfirst($user->type ?? 'Employee') }}" readonly>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Save Changes
                                </button>
                            </form>
                        </div>

                        <!-- Change Password -->
                        <div class="profile-card">
                            <h3 class="card-title">Change Password</h3>
                            <p class="card-description">Update your account password</p>

                            <form action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-input" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password" class="form-input" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-input" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-lock"></i>
                                    Update Password
                                </button>
                            </form>
                        </div>

                        <!-- Account Settings -->
                        <div class="profile-card">
                            <h3 class="card-title">Account Settings</h3>
                            <p class="card-description">Manage your account preferences</p>

                            <form action="{{ route('profile.settings') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label class="form-label">Preferred Notification Channel</label>
                                    <select name="preferred_notification_channel" class="form-input">
                                        <option value="email" {{ $user->preferred_notification_channel == 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="sms" {{ $user->preferred_notification_channel == 'sms' ? 'selected' : '' }}>SMS</option>
                                        <option value="both" {{ $user->preferred_notification_channel == 'both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-cog"></i>
                                    Save Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Layout -->
            <div class="mobile-container">
                <!-- Mobile Header -->
                <div class="mobile-header">
                    <button class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="header-title">Profile</div>
                    <div></div>
                </div>

                <!-- Mobile Profile Card with Upload -->
<div class="mobile-profile-card">
    <!-- Close Button for Mobile -->
    <button onclick="goToMenu()" style="position: absolute; top: 1rem; right: 1rem; background: rgba(255, 255, 255, 0.2); border: none; color: white; padding: 0.5rem; border-radius: 50%; cursor: pointer; transition: all 0.2s ease; z-index: 2;">
        <i class="fas fa-times" style="font-size: 1rem;"></i>
    </button>
    
    <div class="mobile-profile-info">
        <!-- Mobile Avatar with Upload -->
        <div class="mobile-profile-picture-container" style="position: relative; cursor: pointer;" onclick="triggerMobileFileUpload()">
            @if($user->profile_photo_path)
                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                     alt="Profile Picture" 
                     id="mobile-header-profile-avatar-img"
                     class="mobile-avatar"
                     style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: white; border: 3px solid rgba(255, 255, 255, 0.3);">
            @else
                <div id="mobile-header-profile-avatar-initials" class="mobile-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            @endif
            
            <!-- Mobile Upload Overlay -->
            <div class="mobile-upload-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; color: white;">
                <i class="fas fa-camera" style="font-size: 1.25rem; margin-bottom: 0.25rem;"></i>
                <span style="font-size: 0.75rem; font-weight: 600;">Change</span>
            </div>
        </div>
        
        <!-- Hidden File Input for Mobile -->
        <input type="file" id="mobile-profile-picture-input" accept="image/*" style="display: none;">
        
        <div class="mobile-user-details">
            <h1>{{ $user->name }}</h1>
            <div class="mobile-user-badge">{{ ucfirst($user->type ?? 'Customer') }}</div>
            <div class="mobile-contact-info">
                <div>{{ $user->email }}</div>
                @if($user->phone)
                <div>{{ $user->phone }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

                <!-- Mobile Navigation -->
                <div class="mobile-nav">
                    <div class="mobile-nav-tabs">
                        <button class="mobile-nav-tab active" onclick="showMobileTab('profile')">
                            <i class="fas fa-user"></i>
                            Profile
                        </button>
                        <button class="mobile-nav-tab" onclick="showMobileTab('orders')">
                            <i class="fas fa-receipt"></i>
                            Orders
                        </button>
                        <button class="mobile-nav-tab" onclick="showMobileTab('settings')">
                            <i class="fas fa-cog"></i>
                            Settings
                        </button>
                    </div>
                </div>

                <!-- Mobile Content -->
                <div class="mobile-content">
                    <!-- Mobile Profile Tab (Display Only) -->
                    <div class="mobile-tab-content active" id="mobile-profile-tab">
                        <!-- Mobile Profile Information Display -->
                        <div class="mobile-card">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <i class="fas fa-user" style="color: var(--primary-white);"></i>
                                <h3 class="card-title" style="margin: 0;">Profile Information</h3>
                            </div>

                            <div style="text-align: center; margin-bottom: 1.5rem;">
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--light-orange); color: var(--primary-orange); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 700; margin: 0 auto 0.75rem;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div style="background: var(--light-orange); color: var(--primary-orange); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; display: inline-block; text-transform: uppercase;">
                                    {{ $user->type ?? 'EMPLOYEE' }}
                                </div>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 8px; background: var(--background);">
                                    <i class="fas fa-envelope" style="color: var(--text-muted);"></i>
                                    <span style="font-weight: 500;">{{ $user->email }}</span>
                                </div>
                                @if($user->phone)
                                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 8px; background: var(--background);">
                                    <i class="fas fa-phone" style="color: var(--text-muted);"></i>
                                    <span style="font-weight: 500;">{{ $user->phone }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Mobile Recent Updates -->
                        <div class="mobile-card">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                                <i class="fas fa-clock" style="color: var(--primary-orange);"></i>
                                <h3 class="card-title" style="margin: 0;">Recent Order Updates</h3>
                            </div>
                            
                            @forelse($user->orders()->latest()->take(3)->get() as $order)
                            <div class="recent-update-item">
                                <div class="recent-update-info">
                                    <h5>{{ $order->order_number }}</h5>
                                    <div class="recent-update-date">{{ $order->created_at->format('Y-m-d') }}</div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-direction: column; text-align: right;">
                                    <div class="order-status status-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</div>
                                    <div class="recent-update-amount" style="font-size: 0.875rem;">₱{{ number_format($order->orderGroup ? $order->orderGroup->amount_total / 100 : $order->orderItems->sum('line_total') / 100, 2) }}</div>
                                </div>
                            </div>
                            @empty
                            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                <p>No recent order updates.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Mobile Orders Tab -->
                    <div class="mobile-tab-content" id="mobile-orders-tab">
                        <!-- Mobile Summary -->
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="mobile-card" style="text-align: center; padding: 1rem;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-orange); margin-bottom: 0.25rem;">{{ $user->orders()->count() }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Total Orders</div>
                            </div>
                            <div class="mobile-card" style="text-align: center; padding: 1rem;">
                                <div style="font-size: 1rem; font-weight: 700; color: var(--primary-orange); margin-bottom: 0.25rem;">₱{{ number_format($user->orderGroups()->sum('amount_total') / 100, 0) }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Total Spent</div>
                            </div>
                            <div class="mobile-card" style="text-align: center; padding: 1rem;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--success); margin-bottom: 0.25rem;">{{ $user->orders()->where('status', 'completed')->count() }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Completed</div>
                            </div>
                        </div>

                        <div class="mobile-card">
                            <h3 class="card-title">Order History</h3>
                            
                            @forelse($user->orders()->latest()->take(5)->get() as $order)
                            <div class="order-item" data-order-id="{{ $order->id }}" onclick="window.location.href='{{ route('order.detail', $order->id) }}'" style="padding: 1rem 0; border-bottom: 1px solid var(--border); margin-bottom: 0; cursor: pointer;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h5 style="font-weight: 600; margin-bottom: 0.25rem;">{{ $order->order_number }}</h5>
                                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $order->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 700; margin-bottom: 0.25rem;">₱{{ number_format($order->orderGroup ? $order->orderGroup->amount_total / 100 : ($order->orderItems->sum('line_total') / 100), 2) }}</div>
                                        <div class="order-status status-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                <p>No orders yet.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Mobile Settings Tab (Now Contains All Editing Forms) -->
                    <div class="mobile-tab-content" id="mobile-settings-tab">
                        <!-- Personal Information Editing -->
                        <div class="mobile-card">
                            <h3 class="card-title">Personal Information</h3>
                            <p class="card-description">Update your personal details</p>

                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-error">
                                    <ul style="margin: 0; padding-left: 1rem;">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">User Type</label>
                                        <input type="text" class="form-input" value="{{ ucfirst($user->type ?? 'Employee') }}" readonly>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-save"></i>
                                    Save Changes
                                </button>
                            </form>
                        </div>

                        <!-- Change Password -->
                        <div class="mobile-card">
                            <h3 class="card-title">Change Password</h3>
                            <p class="card-description">Update your account password</p>

                            <form action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password" class="form-input" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password" class="form-input" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-input" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-lock"></i>
                                    Update Password
                                </button>
                            </form>
                        </div>

                        <!-- Account Settings -->
                        <div class="mobile-card">
                            <h3 class="card-title">Account Settings</h3>
                            <p class="card-description">Manage your account preferences</p>
                            
                            <form action="{{ route('profile.settings') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label class="form-label">Preferred Notification Channel</label>
                                    <select name="preferred_notification_channel" class="form-input">
                                        <option value="email" {{ $user->preferred_notification_channel == 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="sms" {{ $user->preferred_notification_channel == 'sms' ? 'selected' : '' }}>SMS</option>
                                        <option value="both" {{ $user->preferred_notification_channel == 'both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-cog"></i>
                                    Save Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        // Desktop Tab Functions
        function showDesktopTab(tabName) {
            document.querySelectorAll('.desktop-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.desktop-nav-tab').forEach(button => {
                button.classList.remove('active');
            });
            document.getElementById('desktop-' + tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
        }

        // Mobile Tab Functions
        function showMobileTab(tabName) {
            document.querySelectorAll('.mobile-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.mobile-nav-tab').forEach(button => {
                button.classList.remove('active');
            });
            document.getElementById('mobile-' + tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
        }

        // Go to menu function
        function goToMenu() {
            window.location.href = '{{ route("menu.index") }}';
        }

        // Toast notification system
        function showToast(message) {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: var(--text-dark);
                color: white;
                padding: 12px 20px;
                border-radius: 25px;
                font-size: 14px;
                font-weight: 500;
                z-index: 10000;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            `;
            
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Real-time updates with Laravel Echo
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize animations
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transition = 'opacity 0.5s ease-in';
                }, index * 100);
            });

            // Set up real-time updates if Echo is available
            if (typeof Echo !== 'undefined') {
                @auth
                const userId = {{ auth()->id() }};
                
                Echo.private(`user.${userId}`)
                    .listen('OrderStatusUpdated', (e) => {
                        updateOrderStatus(e.order);
                        updateOrderStats(e.stats);
                        showToast(`Order ${e.order.order_number} status updated to: ${e.order.status}`);
                    });
                @endauth
            }

            // Form submission handlers
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                        
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 3000);
                    }
                });
            });
        });

        function updateOrderStatus(order) {
            // Update order items in both desktop and mobile views
            const orderElements = document.querySelectorAll(`[data-order-id="${order.id}"]`);
            orderElements.forEach(element => {
                const statusBadge = element.querySelector('.order-status');
                if (statusBadge) {
                    statusBadge.textContent = order.status.charAt(0).toUpperCase() + order.status.slice(1);
                    statusBadge.className = `order-status status-${order.status.toLowerCase()}`;
                }
                
                // Visual indicator for update
                element.style.background = 'var(--light-orange)';
                element.style.transition = 'background 0.3s ease';
                setTimeout(() => {
                    element.style.background = '';
                }, 2000);
            });
        }

        function updateOrderStats(stats) {
            if (!stats) return;
            
            // Update desktop summary cards
            const summaryNumbers = document.querySelectorAll('.desktop-container .summary-number');
            if (summaryNumbers.length >= 3) {
                summaryNumbers[0].textContent = stats.total_orders;
                summaryNumbers[1].textContent = `₱${stats.total_spent.toLocaleString()}`;
                summaryNumbers[2].textContent = stats.completed_orders;
            }
            
            // Update mobile summary cards
            const mobileSummaries = document.querySelectorAll('.mobile-container .mobile-card div[style*="font-weight: 700"]');
            mobileSummaries.forEach((element, index) => {
                switch(index) {
                    case 0:
                        element.textContent = stats.total_orders;
                        break;
                    case 1:
                        element.textContent = `₱${stats.total_spent.toLocaleString()}`;
                        break;
                    case 2:
                        element.textContent = stats.completed_orders;
                        break;
                }
            });
            
            // Update badges
            document.querySelectorAll('.badge').forEach(badge => {
                badge.textContent = stats.total_orders;
            });
        }

        // Back button handler
        document.querySelector('.back-btn')?.addEventListener('click', function() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/dashboard';
            }
        });

        // Interactive animations
        document.querySelectorAll('.order-item, .summary-card').forEach(element => {
            element.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

    function triggerFileUpload() {
    document.getElementById('profile-picture-input').click();
}

function triggerMobileFileUpload() {
    document.getElementById('mobile-profile-picture-input').click();
}

document.getElementById('profile-picture-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        uploadHeaderProfilePicture(file, 'desktop');
    }
});

document.getElementById('mobile-profile-picture-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        uploadHeaderProfilePicture(file, 'mobile');
    }
});

function uploadHeaderProfilePicture(file, device) {
    // Validate file
    if (file.size > 2 * 1024 * 1024) {
        showToast('File size must be less than 2MB', 'error');
        return;
    }

    if (!file.type.match(/^image\/(jpeg|jpg|png|gif|webp)$/)) {
        showToast('Please select a valid image file', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('profile_picture', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Show loading state
    const containerClass = device === 'mobile' ? '.mobile-profile-picture-container' : '.profile-picture-container';
    const container = document.querySelector(containerClass);
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'upload-loading';
    loadingDiv.innerHTML = '<i class="fas fa-spinner"></i><span style="margin-top: 0.5rem; font-size: 0.875rem;">Uploading...</span>';
    container.appendChild(loadingDiv);
    
    fetch('/profile/picture', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all avatars (header, profile section, navbar)
            updateAllAvatars(data.profile_picture_url);
            showToast('Profile picture updated successfully!', 'success');
        } else {
            throw new Error(data.message || 'Upload failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update profile picture. Please try again.', 'error');
    })
    .finally(() => {
        container.removeChild(loadingDiv);
    });
}

function updateAllAvatars(imageUrl) {
    // Update desktop header avatar
    updateHeaderAvatar(imageUrl, 'desktop');
    // Update mobile header avatar
    updateHeaderAvatar(imageUrl, 'mobile');
    // Update profile section avatars if they exist
    if (typeof updateProfileAvatar === 'function') {
        updateProfileAvatar(imageUrl);
    }
    // Update navbar avatar - you'll need to implement this
    updateNavbarAvatar(imageUrl);
}

function updateHeaderAvatar(imageUrl, device) {
    const prefix = device === 'mobile' ? 'mobile-header' : 'header';
    const containerClass = device === 'mobile' ? '.mobile-profile-picture-container' : '.profile-picture-container';
    const container = document.querySelector(containerClass);
    
    // Remove initials div if present
    const initialsDiv = document.getElementById(`${prefix}-profile-avatar-initials`);
    if (initialsDiv) {
        initialsDiv.remove();
    }
    
    // Create or update image
    let imgElement = document.getElementById(`${prefix}-profile-avatar-img`);
    if (imgElement) {
        imgElement.src = imageUrl;
    } else {
        imgElement = document.createElement('img');
        imgElement.id = `${prefix}-profile-avatar-img`;
        imgElement.src = imageUrl;
        imgElement.alt = 'Profile Picture';
        imgElement.className = device === 'mobile' ? 'mobile-avatar' : 'desktop-avatar';
        imgElement.style.cssText = device === 'mobile' 
            ? 'width: 80px; height: 80px; border-radius: 50%; object-fit: cover; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: white; border: 3px solid rgba(255, 255, 255, 0.3);'
            : 'width: 120px; height: 120px; border-radius: 50%; object-fit: cover; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: white; border: 4px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px);';
        
        // Insert before the upload overlay
        const uploadOverlay = container.querySelector(device === 'mobile' ? '.mobile-upload-overlay' : '.upload-overlay');
        container.insertBefore(imgElement, uploadOverlay);
    }
}

// Placeholder for navbar update - you'll implement this based on your navbar component
function updateNavbarAvatar(imageUrl) {
    // This will be implemented based on your navbar structure
    console.log('Update navbar avatar with:', imageUrl);
}


document.querySelectorAll('.mobile-container .order-item[data-order-id]').forEach(item => {
    item.addEventListener('touchstart', function() {
        this.style.background = 'var(--light-orange)';
    });
    
    item.addEventListener('touchend', function() {
        setTimeout(() => {
            this.style.background = '';
        }, 150);
    });
});
    </script>
</x-layouts.profile>