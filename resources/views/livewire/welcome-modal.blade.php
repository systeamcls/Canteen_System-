<div>
    @if($showModal)
        <div class="modal-overlay active">
            <div class="modal-container">
                <div class="modal-header">
                    <button class="modal-close" wire:click="close">&times;</button>
                    <h2 class="modal-title">Welcome to Canteen Central</h2>
                    <p class="modal-subtitle">Choose how you'd like to continue</p>
                </div>
                
                <div class="modal-body">
                    @if($currentView === 'options')
                        <!-- Login Options -->
                        <div id="loginOptions">
                            <!-- Guest Login -->
                            <button class="login-option" wire:click="loginAsGuest">
                                <div class="login-option-content">
                                    <div class="login-option-icon guest-icon">
                                        👤
                                    </div>
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
                                    <div class="login-option-icon employee-icon">
                                        👨‍💼
                                    </div>
                                    <div class="login-option-text">
                                        <h3>Employee Login</h3>
                                        <p>Enjoy a lot of perks and Discounts!</p>
                                    </div>
                                    <div class="login-option-arrow">→</div>
                                </div>
                            </button>
                        </div>
                    @endif

                    @if($currentView === 'employee-form')
                        <!-- Employee Login Form -->
                        <div class="employee-form active">
                            <button class="back-btn" wire:click="showOptions">
                                ← Back to login options
                            </button>
                            
                            <div class="form-divider">
                                <span>Employee Sign In</span>
                            </div>

                            <form wire:submit.prevent="loginAsEmployee">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" 
                                           id="email" 
                                           wire:model.defer="email" 
                                           class="form-input @error('email') error @enderror" 
                                           placeholder="Enter your email" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" 
                                           id="password" 
                                           wire:model.defer="password" 
                                           class="form-input @error('password') error @enderror" 
                                           placeholder="Enter your password" 
                                           required>
                                </div>

                                @if($loginError)
                                    <div class="error-message show">
                                        <strong>Login Failed:</strong> {{ $loginError }}
                                    </div>
                                @endif

                                <button type="submit" class="submit-btn" wire:loading.class="loading" wire:target="loginAsEmployee">
                                    <span wire:loading.remove wire:target="loginAsEmployee">Sign In</span>
                                    <span wire:loading wire:target="loginAsEmployee">Signing In...</span>
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <p class="text-sm text-gray-600">
                                    Don't have an account? 
                                    <button type="button" 
                                            wire:click="showRegisterForm" 
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                        Register as Employee
                                    </button>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($currentView === 'register-form')
                        <!-- Employee Registration Form -->
                        <div class="employee-form active">
                            <button class="back-btn" wire:click="showEmployeeForm">
                                ← Back to sign in
                            </button>
                            
                            <div class="form-divider">
                                <span>Employee Registration</span>
                            </div>

                            <form wire:submit.prevent="registerEmployee">
                                <div class="form-group">
                                    <label for="registerName" class="form-label">Full Name</label>
                                    <input type="text" 
                                           id="registerName" 
                                           wire:model.defer="registerName" 
                                           class="form-input @error('registerName') error @enderror" 
                                           placeholder="Enter your full name" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="registerEmail" class="form-label">Email Address</label>
                                    <input type="email" 
                                           id="registerEmail" 
                                           wire:model.defer="registerEmail" 
                                           class="form-input @error('registerEmail') error @enderror" 
                                           placeholder="Enter your email" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="registerPhone" class="form-label">Phone Number (Optional)</label>
                                    <input type="tel" 
                                           id="registerPhone" 
                                           wire:model.defer="registerPhone" 
                                           class="form-input @error('registerPhone') error @enderror" 
                                           placeholder="Enter your phone number">
                                </div>

                                <div class="form-group">
                                    <label for="registerPassword" class="form-label">Password</label>
                                    <input type="password" 
                                           id="registerPassword" 
                                           wire:model.defer="registerPassword" 
                                           class="form-input @error('registerPassword') error @enderror" 
                                           placeholder="Enter your password" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="registerPasswordConfirmation" class="form-label">Confirm Password</label>
                                    <input type="password" 
                                           id="registerPasswordConfirmation" 
                                           wire:model.defer="registerPasswordConfirmation" 
                                           class="form-input @error('registerPasswordConfirmation') error @enderror" 
                                           placeholder="Confirm your password" 
                                           required>
                                </div>

                                @if($registerError)
                                    <div class="error-message show">
                                        <strong>Registration Failed:</strong> {{ $registerError }}
                                    </div>
                                @endif

                                <button type="submit" class="submit-btn" wire:loading.class="loading" wire:target="registerEmployee">
                                    <span wire:loading.remove wire:target="registerEmployee">Register</span>
                                    <span wire:loading wire:target="registerEmployee">Creating Account...</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .modal-overlay.active .modal-container {
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>