<x-filament-panels::page>
    <style>
        /* Hide page header and padding */
        .fi-simple-page,
        .fi-section-content-ctn {
            padding: 0 !important;
        }

        /* Fullscreen overlay */
        .two-factor-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* Auth card */
        .auth-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 28rem;
            width: 100%;
            padding: 2.5rem;
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Lock icon */
        .lock-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            }

            50% {
                box-shadow: 0 10px 35px rgba(102, 126, 234, 0.6);
            }
        }

        /* Code input styling */
        .code-input input {
            text-align: center;
            font-size: 1.75rem;
            letter-spacing: 1rem;
            font-weight: 600;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            padding: 1rem;
        }

        .code-input input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Button styling */
        .fi-btn {
            border-radius: 0.75rem !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .fi-btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
        }

        .fi-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3) !important;
        }

        /* Help section */
        .help-section {
            background: #f9fafb;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1.5rem;
        }
    </style>

    <!-- Fullscreen Blurred Overlay -->
    <div class="two-factor-overlay">

        <!-- Authentication Card -->
        <div class="auth-card">

            <!-- Lock Icon -->
            <div class="lock-icon">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
            </div>

            <!-- Title -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    Two-Factor Authentication
                </h1>
                <p class="text-gray-600 text-sm">
                    @if ($recovery)
                        Enter your recovery code to continue
                    @else
                        Enter the 6-digit code from your authenticator app
                    @endif
                </p>
            </div>

            <!-- Form -->
            <form wire:submit="verify" class="space-y-4">

                <div class="code-input">
                    {{ $this->form }}
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2 pt-2">
                    @foreach ($this->getFormActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>

            </form>

            <!-- Help Section -->
            <div class="help-section">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="text-sm">
                        <p class="font-medium text-gray-700 mb-1">Need help?</p>
                        <p class="text-gray-600">
                            @if ($recovery)
                                Use one of the recovery codes you saved when enabling 2FA.
                            @else
                                Open your authenticator app (Google Authenticator, Authy, etc.) and enter the code
                                shown.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Toggle Recovery -->
            <div class="text-center mt-4 pt-4 border-t border-gray-200">
                <button wire:click="toggleRecovery" type="button"
                    class="text-sm text-gray-600 hover:text-gray-900 font-medium transition">
                    @if ($recovery)
                        ← Back to authenticator code
                    @else
                        Lost your device? Use a recovery code →
                    @endif
                </button>
            </div>

        </div>

    </div>

    @push('scripts')
        <script>
            // Auto-focus on input
            document.addEventListener('DOMContentLoaded', () => {
                const input = document.querySelector('input[wire\\:model="code"]');
                if (input) {
                    input.focus();

                    // Auto-select text if any
                    input.addEventListener('focus', () => input.select());
                }
            });

            // Prevent closing with ESC (optional)
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    e.preventDefault();
                }
            });
        </script>
    @endpush

</x-filament-panels::page>
