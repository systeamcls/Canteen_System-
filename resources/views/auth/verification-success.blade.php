<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verified Successfully</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-orange: #FF6B35;
            --secondary-orange: #FF8A6B;
            --accent-orange: #E55B2B;
            --light-orange: #FFF4F1;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --background: #F9FAFB;
            --white: #FFFFFF;
            --success: #10B981;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--background) 0%, #E5E7EB 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .success-container {
            background: white;
            border-radius: 24px;
            padding: 4rem 3rem;
            box-shadow: 0 20px 40px var(--shadow);
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        .success-container::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2.5rem;
            color: white;
            font-size: 3rem;
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.3);
            position: relative;
            z-index: 1;
            animation: bounceIn 0.8s ease-out 0.3s both;
        }

        .success-title {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--text-dark) 0%, var(--success) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .success-message {
            color: var(--text-muted);
            font-size: 1.125rem;
            line-height: 1.6;
            margin-bottom: 3rem;
        }

        .success-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-orange) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(255, 107, 53, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-muted);
            border: 2px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--background);
            border-color: var(--primary-orange);
            color: var(--primary-orange);
        }

        .features-preview {
            background: var(--light-orange);
            border-radius: 16px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }

        .features-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-orange);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .features-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            list-style: none;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-dark);
            font-size: 0.875rem;
        }

        .feature-item i {
            color: var(--success);
            font-size: 0.75rem;
        }

        /* Mobile responsive */
        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .success-container {
                padding: 3rem 2rem;
            }

            .success-title {
                font-size: 1.75rem;
            }

            .success-message {
                font-size: 1rem;
            }

            .features-list {
                grid-template-columns: 1fr;
            }

            .btn {
                padding: 0.875rem 1.5rem;
                font-size: 0.875rem;
            }
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.1);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .checkmark-animation {
            animation: checkmark 0.6s ease-in-out 0.9s both;
        }

        @keyframes checkmark {
            0% {
                opacity: 0;
                transform: scale(0);
            }
            50% {
                opacity: 1;
                transform: scale(1.3);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check checkmark-animation"></i>
        </div>

        <h1 class="success-title">Email Verified Successfully!</h1>
        
        <p class="success-message">
            Your account is now fully activated and ready to use. You can now place orders, access your profile, and enjoy all the features of our canteen system.
        </p>

        <div class="features-preview">
            <div class="features-title">
                <i class="fas fa-sparkles"></i>
                What you can do now:
            </div>
            <ul class="features-list">
                <li class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    Place food orders
                </li>
                <li class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    Track order status
                </li>
                <li class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    Access full profile
                </li>
                <li class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    View order history
                </li>
            </ul>
        </div>

        <div class="success-actions">
            <a href="{{ route('menu.index') }}" class="btn btn-primary">
                <i class="fas fa-utensils"></i>
                Start Ordering
            </a>
            <a href="{{ route('user.profile.show') }}" class="btn btn-secondary">
                <i class="fas fa-user"></i>
                View Profile
            </a>
        </div>
    </div>

    <script>
        // Add some confetti effect or celebration
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Add a subtle celebration sound or animation
            console.log('🎉 Email verification successful!');
        });
    </script>
</body>
</html>