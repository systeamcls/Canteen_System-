<div>
    <!-- Modal Overlay -->
    <div x-data="{ open: @entangle('isOpen').live }" @open-delivery-modal.window="open = true">

        <div class="delivery-modal-overlay" :class="{ 'active': open }" @click="open = false; $wire.close()">
            <div class="delivery-modal-container" wire:click.stop>

                <!-- Close Button -->
                <button class="delivery-modal-close" wire:click="close">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Header -->
                <div class="delivery-modal-header">
                    <h2 class="delivery-modal-title">Choose Delivery Option</h2>
                    <p class="delivery-modal-subtitle">How would you like to receive your order?</p>
                </div>

                <!-- Delivery Type Tabs -->
                <div class="delivery-tabs">
                    <button class="delivery-tab {{ $deliveryType === 'delivery' ? 'active' : '' }}"
                        wire:click="setDeliveryType('delivery')">
                        <span class="delivery-tab-icon">🚚</span>
                        <span>Delivery</span>
                    </button>
                    <button class="delivery-tab {{ $deliveryType === 'pickup' ? 'active' : '' }}"
                        wire:click="setDeliveryType('pickup')">
                        <span class="delivery-tab-icon">🏪</span>
                        <span>Pick-up</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="delivery-modal-body">

                    <!-- Delivery Address (only show for delivery) -->
                    @if ($deliveryType === 'delivery')
                        <div class="delivery-field">
                            <label class="delivery-label">
                                <span class="label-icon">📍</span>
                                Delivery Address
                                <span class="label-note">(LTO vicinity only)</span>
                            </label>
                            <input type="text" wire:model.defer="deliveryAddress"
                                class="delivery-input @error('deliveryAddress') error @enderror"
                                placeholder="e.g., Building A, 2nd Floor, Licensing Division">
                            @error('deliveryAddress')
                                <span class="delivery-error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Pickup Info (only show for pickup) -->
                    @if ($deliveryType === 'pickup')
                        <div class="delivery-info-box">
                            <span class="info-icon">ℹ️</span>
                            <div>
                                <strong>Pickup Instructions:</strong>
                                <p>Please pick up your order at the respective stall counters.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Time Selection -->
                    <div class="delivery-field">
                        <label class="delivery-label">
                            <span class="label-icon">🕐</span>
                            Choose {{ $deliveryType === 'delivery' ? 'Delivery' : 'Pickup' }} Time
                        </label>

                        <!-- Now Option -->
                        <div class="delivery-radio-option">
                            <label class="radio-label">
                                <input type="radio" wire:model.live="deliveryTime" value="now" class="radio-input">
                                <span class="radio-custom"></span>
                                <div class="radio-content">
                                    <span class="radio-title">Now (Today)</span>
                                    <span class="radio-subtitle">Ready in ~30 minutes</span>
                                </div>
                            </label>
                        </div>

                        <!-- Schedule for Later Option -->
                        <div class="delivery-radio-option">
                            <label class="radio-label">
                                <input type="radio" wire:model.live="deliveryTime" value="scheduled"
                                    class="radio-input">
                                <span class="radio-custom"></span>
                                <div class="radio-content">
                                    <span class="radio-title">Schedule for Later (Pre-order)</span>
                                    <span class="radio-subtitle">Choose date and time</span>
                                </div>
                            </label>
                        </div>

                        <!-- Scheduled Date & Time (show only if scheduled is selected) -->
                        @if ($deliveryTime === 'scheduled')
                            <div class="scheduled-fields">
                                <div class="scheduled-field">
                                    <label class="scheduled-label">📅 Select Date</label>
                                    <input type="date" wire:model.defer="scheduledDate"
                                        class="scheduled-input @error('scheduledDate') error @enderror"
                                        min="{{ date('Y-m-d') }}">
                                    @error('scheduledDate')
                                        <span class="delivery-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="scheduled-field">
                                    <label class="scheduled-label">🕐 Select Time</label>
                                    <select wire:model.defer="scheduledTime"
                                        class="scheduled-input @error('scheduledTime') error @enderror">
                                        <option value="">Choose time...</option>
                                        @foreach ($timeSlots as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('scheduledTime')
                                        <span class="delivery-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Canteen Hours Info -->
                    <div class="delivery-info-box hours-info">
                        <span class="info-icon">🕐</span>
                        <div>
                            <strong>Canteen Operating Hours:</strong>
                            <p>Monday - Friday: 8:00 AM - 5:00 PM</p>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="delivery-modal-footer">
                    <button type="button" class="delivery-btn-secondary" wire:click="close">
                        Cancel
                    </button>
                    <button type="button" class="delivery-btn-primary" wire:click="confirm"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirm">
                            Confirm & Continue
                        </span>
                        <span wire:loading wire:target="confirm">
                            Processing...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
