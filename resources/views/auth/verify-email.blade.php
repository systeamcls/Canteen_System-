<x-layouts.profile>
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
            --error: #EF4444;
            --warning: #F59E0B;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, var(--background) 0%, #E5E7EB 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .verification-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 40px var(--shadow);
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .verification-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 107, 53, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .verification-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2.5rem;
            box-shadow: 0 12px 25px rgba(255, 107, 53, 0.3);
            position: relative;
            z-index: 1;
        }

        .verification-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .verification-message {
            color: var(--text-muted);
            font-size: 1.125rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .email-display {
            background: var(--light-orange);
            color: var(--primary-orange);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            margin-bottom: 2rem;
            word-break: break-all;
        }

        .verification-actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-orange) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 107, 53, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
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

        .last-sent {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 1rem;
            padding: 0.75rem;
            background: var(--background);
            border-radius: 8px;
        }

        .countdown {
            font-weight: 600;
            color: var(--primary-orange);
        }

        /* Mobile responsive */
        @media (max-width: 640px) {
            .verification-container {
                padding: 1rem;
            }

            .verification-card {
                padding: 2rem;
            }

            .verification-title {
                font-size: 1.5rem;
            }

            .verification-message {
                font-size: 1rem;
            }

            .verification-actions {
                gap: 0.75rem;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                font-size: 0.875rem;
            }
        }

        /* Animation */
        .verification-card {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
    </style>

    <div class="verification-container">
        <div class="verification-card">
            <div class="verification-icon pulse">
                <i class="fas fa-envelope"></i>
            </div>

            <h1 class="verification-title">Verify Your Email</h1>
            
            <p class="verification-message">
                We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.
            </p>

            <div class="email-display">
                {{ auth()->user()->email }}
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="verification-actions">
                <!-- Resend Button -->
                <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                    @csrf
                    <button type="submit" 
                            class="btn btn-primary" 
                            id="resendBtn"
                            @if(!auth()->user()->canResendVerification()) disabled @endif>
                        <i class="fas fa-paper-plane"></i>
                        <span id="resendText">
                            @if(auth()->user()->canResendVerification())
                                Resend Verification Email
                            @else
                                Resend Available In <span class="countdown" id="countdown">60</span>s
                            @endif
                        </span>
                    </button>
                </form>

                <!-- Back to Menu -->
                <a href="{{ route('menu.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Menu
                </a>
            </div>

            <!-- Last Sent Info -->
            @if(auth()->user()->verification_sent_at)
                <div class="last-sent">
                    <i class="fas fa-clock"></i>
                    Last verification email sent: {{ auth()->user()->verification_sent_at->diffForHumans() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resendBtn = document.getElementById('resendBtn');
            const resendText = document.getElementById('resendText');
            const countdownEl = document.getElementById('countdown');
            
            @if(!auth()->user()->canResendVerification())
                // Calculate remaining seconds
                const verificationSentAt = new Date('{{ auth()->user()->verification_sent_at->toISOString() }}');
                const now = new Date();
                const elapsedSeconds = Math.floor((now - verificationSentAt) / 1000);
                let remainingSeconds = Math.max(0, 60 - elapsedSeconds);
                
                if (remainingSeconds > 0) {
                    const countdown = setInterval(() => {
                        remainingSeconds--;
                        
                        if (countdownEl) {
                            countdownEl.textContent = remainingSeconds;
                        }
                        
                        if (remainingSeconds <= 0) {
                            clearInterval(countdown);
                            resendBtn.disabled = false;
                            resendText.innerHTML = '<i class="fas fa-paper-plane"></i> Resend Verification Email';
                        }
                    }, 1000);
                }
            @endif

            // Form submission handling
            document.getElementById('resendForm').addEventListener('submit', function(e) {
                if (!resendBtn.disabled) {
                    resendBtn.disabled = true;
                    resendText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                }
            });
        });

        // Auto-refresh every 30 seconds to check for verification
        setInterval(() => {
            fetch('/email/verify-status', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.verified) {
                    window.location.href = '{{ route("user.profile.show") }}';
                }
            })
            .catch(error => {
                console.log('Verification check failed:', error);
            });
        }, 30000);
    </script>
</x-layouts.profile>