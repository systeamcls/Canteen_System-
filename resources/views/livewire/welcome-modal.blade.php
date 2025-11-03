<div>
    @if ($showModal)
        <div class="modal-overlay active" id="welcomeModal">
            <div class="modal-container">
                <!-- Waving Hand Peeking from Top (only on options view) -->
                @if ($currentView === 'options')
                    <div class="wave-peek">👋</div>
                @endif

                <div class="modal-header">
                    <!-- Back button for login/register forms -->
                    @if ($currentView !== 'options')
                        <button class="back-btn-header"
                            wire:click="{{ $currentView === 'register-form' ? 'showEmployeeForm' : 'showOptions' }}">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>
                    @endif

                    <!-- Top Right: Close button -->
                    <button class="modal-close" wire:click="close">&times;</button>

                    <h2 class="modal-title">Welcome to Canteen Central</h2>

                    <!-- Only show subtitle on options view -->
                    @if ($currentView === 'options')
                        <p class="modal-subtitle">Choose how you'd like to continue</p>
                    @endif
                </div>

                <div class="modal-body">
                    @if ($currentView === 'options')
                        <!-- Login Options -->
                        <div id="loginOptions">
                            <!-- Guest Login -->
                            <button class="login-option" wire:click="loginAsGuest">
                                <div class="login-option-content">
                                    <div class="login-option-icon guest-icon">👤</div>
                                    <div class="login-option-text">
                                        <h3>Login as Guest</h3>
                                        <p>Browse and order without creating an account</p>
                                    </div>
                                    <div class="login-option-arrow">→</div>
                                </div>
                            </button>

                            <!-- Employee Login -->
                            <button class="login-option" wire:click="showEmployeeForm">
                                <div class="login-option-content">
                                    <div class="login-option-icon employee-icon">👨‍💼</div>
                                    <div class="login-option-text">
                                        <h3>Regular Customer Login</h3>
                                        <p>Enjoy a lot of perks and Discounts!</p>
                                    </div>
                                    <div class="login-option-arrow">→</div>
                                </div>
                            </button>
                        </div>
                    @endif

                    @if ($currentView === 'employee-form')
                        <!-- Employee Login Form with Floating Labels -->
                        <div class="employee-form active">
                            <div class="form-divider">
                                <span>Regular Customer Sign In</span>
                            </div>

                            <form wire:submit.prevent="loginAsEmployee">
                                <!-- Floating Label Email -->
                                <div class="floating-form-group">
                                    <input type="email" id="email" wire:model.defer="email"
                                        class="floating-input @error('email') error @enderror" required placeholder=" ">
                                    <label for="email" class="floating-label">Email Address</label>
                                </div>

                                <!-- Floating Label Password WITH EYE ICON -->
                                <div class="floating-form-group password-toggle-wrapper">
                                    <input type="password" id="password" wire:model.defer="password"
                                        class="floating-input @error('password') error @enderror" required
                                        placeholder=" ">
                                    <label for="password" class="floating-label">Password</label>

                                    <!-- Eye Toggle Button -->
                                    <button type="button" class="password-toggle-btn"
                                        onclick="togglePasswordVisibility('password', this)">
                                        <svg class="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg class="eye-closed" style="display: none;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>

                                @if ($loginError)
                                    <div class="error-message show">
                                        <strong>Login Failed:</strong> {{ $loginError }}
                                    </div>
                                @endif

                                <button type="submit" class="submit-btn" wire:loading.class="loading"
                                    wire:target="loginAsEmployee">
                                    <span wire:loading.remove wire:target="loginAsEmployee">Sign In</span>
                                    <span wire:loading wire:target="loginAsEmployee">Signing In...</span>
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <p class="text-sm text-gray-600">
                                    Don't have an account?
                                    <button type="button" wire:click="showRegisterForm"
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                        Register as Regular Customer
                                    </button>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($currentView === 'register-form')
                        <!-- Compact Registration Form -->
                        <div class="employee-form active">
                            <!-- Simple Welcome Header (no animation) -->
                            <div class="welcome-header-simple">
                                <h3 class="welcome-title">Create Your Account</h3>
                                <p class="welcome-subtitle">Join us to get started</p>
                            </div>

                            <!-- Progress Indicator -->
                            <div class="progress-indicator">
                                <div class="progress-step active">
                                    <div class="progress-dot">1</div>
                                    <span>Details</span>
                                </div>
                                <div class="progress-line"></div>
                                <div class="progress-step">
                                    <div class="progress-dot">2</div>
                                    <span>Verify</span>
                                </div>
                                <div class="progress-line"></div>
                                <div class="progress-step">
                                    <div class="progress-dot">3</div>
                                    <span>Done</span>
                                </div>
                            </div>

                            <form wire:submit.prevent="registerEmployee">
                                <!-- Full Name with Icon -->
                                <div class="floating-form-group input-with-icon">
                                    <span class="input-icon">👤</span>
                                    <input type="text" id="registerName" wire:model.defer="registerName"
                                        class="floating-input @error('registerName') error @enderror" required
                                        placeholder=" ">
                                    <label for="registerName" class="floating-label">Full Name</label>
                                </div>

                                <!-- Email with Icon -->
                                <div class="floating-form-group input-with-icon">
                                    <span class="input-icon">✉️</span>
                                    <input type="email" id="registerEmail" wire:model.defer="registerEmail"
                                        class="floating-input @error('registerEmail') error @enderror" required
                                        placeholder=" ">
                                    <label for="registerEmail" class="floating-label">Email Address</label>
                                </div>

                                <!-- Phone with Icon -->
                                <div class="floating-form-group input-with-icon">
                                    <span class="input-icon">📱</span>
                                    <input type="tel" id="registerPhone" wire:model.defer="registerPhone"
                                        class="floating-input @error('registerPhone') error @enderror"
                                        placeholder=" ">
                                    <label for="registerPhone" class="floating-label">Phone Number (Optional)</label>
                                </div>

                                <!-- Password with Icon & Toggle -->
                                <div class="floating-form-group password-toggle-wrapper input-with-icon">
                                    <span class="input-icon">🔑</span>
                                    <input type="password" id="registerPassword" wire:model.defer="registerPassword"
                                        class="floating-input @error('registerPassword') error @enderror" required
                                        placeholder=" " onfocus="showPasswordRequirements()"
                                        onblur="hidePasswordRequirements()"
                                        oninput="checkPasswordStrength(this.value)">
                                    <label for="registerPassword" class="floating-label">Password</label>

                                    <button type="button" class="password-toggle-btn"
                                        onclick="togglePasswordVisibility('registerPassword', this)">
                                        <svg class="eye-open" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg class="eye-closed" style="display: none;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Password Requirements -->
                                <div class="password-requirements" id="passwordRequirements">
                                    <span class="req-badge" id="req-length">8+ chars</span>
                                    <span class="req-badge" id="req-uppercase">A-Z</span>
                                    <span class="req-badge" id="req-lowercase">a-z</span>
                                    <span class="req-badge" id="req-number">0-9</span>
                                    <span class="req-badge" id="req-special">!@#$</span>
                                </div>

                                <!-- Confirm Password with Icon & Toggle -->
                                <div class="floating-form-group password-toggle-wrapper input-with-icon">
                                    <span class="input-icon">🔑</span>
                                    <input type="password" id="registerPasswordConfirmation"
                                        wire:model.defer="registerPasswordConfirmation"
                                        class="floating-input @error('registerPasswordConfirmation') error @enderror"
                                        required placeholder=" ">
                                    <label for="registerPasswordConfirmation" class="floating-label">Confirm
                                        Password</label>

                                    <button type="button" class="password-toggle-btn"
                                        onclick="togglePasswordVisibility('registerPasswordConfirmation', this)">
                                        <svg class="eye-open" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg class="eye-closed" style="display: none;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>

                                @if ($registerError)
                                    <div class="error-message show">
                                        <strong>⚠️ Registration Failed:</strong> {{ $registerError }}
                                    </div>
                                @endif

                                <!-- Submit Button -->
                                <button type="submit" class="submit-btn-modern" wire:loading.class="loading"
                                    wire:target="registerEmployee">
                                    <span class="btn-content" wire:loading.remove wire:target="registerEmployee">
                                        <span class="btn-icon"></span>
                                        <span>Create Account</span>
                                        <span class="btn-arrow">→</span>
                                    </span>
                                    <span wire:loading wire:target="registerEmployee">
                                        <span class="spinner"></span>
                                        Creating Account...
                                    </span>
                                </button>

                                <!-- Already have account link -->
                                <div class="already-have-account">
                                    Already have an account?
                                    <button type="button" wire:click="showEmployeeForm" class="link-btn">
                                        Sign in here
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Show/hide password requirements
    function showPasswordRequirements() {
        const requirements = document.getElementById('passwordRequirements');
        if (requirements) {
            requirements.classList.add('show');
        }
    }

    function hidePasswordRequirements() {
        // Don't hide immediately, give user time to see
        setTimeout(() => {
            const requirements = document.getElementById('passwordRequirements');
            if (requirements) {
                requirements.classList.remove('show');
            }
        }, 300);
    }

    // Password visibility toggle
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const eyeOpen = button.querySelector('.eye-open');
        const eyeClosed = button.querySelector('.eye-closed');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }

    // Password strength checker (compact badges)
    function checkPasswordStrength(password) {
        const lengthReq = document.getElementById('req-length');
        const uppercaseReq = document.getElementById('req-uppercase');
        const lowercaseReq = document.getElementById('req-lowercase');
        const numberReq = document.getElementById('req-number');
        const specialReq = document.getElementById('req-special');

        // Check length
        if (password.length >= 8) {
            lengthReq.classList.add('valid');
            lengthReq.classList.remove('invalid');
        } else if (password.length > 0) {
            lengthReq.classList.add('invalid');
            lengthReq.classList.remove('valid');
        } else {
            lengthReq.classList.remove('valid', 'invalid');
        }

        // Check uppercase
        if (/[A-Z]/.test(password)) {
            uppercaseReq.classList.add('valid');
            uppercaseReq.classList.remove('invalid');
        } else if (password.length > 0) {
            uppercaseReq.classList.add('invalid');
            uppercaseReq.classList.remove('valid');
        } else {
            uppercaseReq.classList.remove('valid', 'invalid');
        }

        // Check lowercase
        if (/[a-z]/.test(password)) {
            lowercaseReq.classList.add('valid');
            lowercaseReq.classList.remove('invalid');
        } else if (password.length > 0) {
            lowercaseReq.classList.add('invalid');
            lowercaseReq.classList.remove('valid');
        } else {
            lowercaseReq.classList.remove('valid', 'invalid');
        }

        // Check number
        if (/[0-9]/.test(password)) {
            numberReq.classList.add('valid');
            numberReq.classList.remove('invalid');
        } else if (password.length > 0) {
            numberReq.classList.add('invalid');
            numberReq.classList.remove('valid');
        } else {
            numberReq.classList.remove('valid', 'invalid');
        }

        // Check special character
        if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
            specialReq.classList.add('valid');
            specialReq.classList.remove('invalid');
        } else if (password.length > 0) {
            specialReq.classList.add('invalid');
            specialReq.classList.remove('valid');
        } else {
            specialReq.classList.remove('valid', 'invalid');
        }
    }

    // Existing functions
    function closeModal() {
        const modal = document.getElementById('welcomeModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    }

    function loginAsGuest() {
        window.location.href = '/menu';
    }

    function showEmployeeForm() {
        alert('Employee login form would open here. Please refresh and try the Livewire version.');
    }
</script>
