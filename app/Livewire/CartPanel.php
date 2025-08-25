<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPanel extends Component
{
    public array $cartTotals = [];
    public bool $isOpen = false;
    public bool $isLoading = false;
    public string $message = '';
    public string $messageType = 'success';

    protected CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        $this->loadCartData();
    }

    /**
     * Load cart data and totals
     */
    public function loadCartData(): void
    {
        try {
            $this->cartTotals = $this->cartService->getCartTotals();
            $this->dispatch('cart-updated', $this->cartTotals);
        } catch (\Exception $e) {
            $this->showMessage('Error loading cart data', 'error');
        }
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $this->isLoading = true;

        try {
            $result = $this->cartService->updateQuantity($cartItemId, $quantity);

            if ($result['success']) {
                $this->cartTotals = $result['cart_totals'];
                $this->showMessage($result['message'], 'success');
                $this->broadcastCartUpdate();
                $this->dispatch('cart-updated', $this->cartTotals);
            } else {
                $this->showMessage($result['message'], 'error');
            }
        } catch (\Exception $e) {
            $this->showMessage('Error updating quantity', 'error');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartItemId): void
    {
        $this->isLoading = true;

        try {
            $result = $this->cartService->removeFromCart($cartItemId);

            if ($result['success']) {
                $this->cartTotals = $result['cart_totals'];
                $this->showMessage($result['message'], 'success');
                $this->broadcastCartUpdate();
                $this->dispatch('cart-updated', $this->cartTotals);
            } else {
                $this->showMessage($result['message'], 'error');
            }
        } catch (\Exception $e) {
            $this->showMessage('Error removing item', 'error');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): void
    {
        $this->isLoading = true;

        try {
            $result = $this->cartService->clearCart();

            if ($result['success']) {
                $this->loadCartData();
                $this->showMessage($result['message'], 'success');
                $this->broadcastCartUpdate();
            } else {
                $this->showMessage($result['message'], 'error');
            }
        } catch (\Exception $e) {
            $this->showMessage('Error clearing cart', 'error');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Toggle cart panel visibility
     */
    public function toggleCart(): void
    {
        $this->isOpen = !$this->isOpen;
        
        if ($this->isOpen) {
            $this->loadCartData();
        }
    }

    /**
     * Listen for cart updates from other components
     */
    #[On('cart-item-added')]
    public function handleCartItemAdded(array $data = []): void
    {
        $this->loadCartData();
        $this->showMessage('Item added to cart', 'success');
    }

    /**
     * Listen for external cart updates
     */
    #[On('refresh-cart')]
    public function handleRefreshCart(): void
    {
        $this->loadCartData();
    }

    /**
     * Validate cart items (check availability and prices)
     */
    public function validateCart(): void
    {
        try {
            $validation = $this->cartService->validateCart();

            if (!$validation['valid']) {
                $this->cartTotals = $validation['cart_totals'];
                $messages = collect($validation['issues'])->pluck('message')->join(', ');
                $this->showMessage("Cart updated: {$messages}", 'warning');
                $this->dispatch('cart-updated', $this->cartTotals);
            }
        } catch (\Exception $e) {
            $this->showMessage('Error validating cart', 'error');
        }
    }

    /**
     * Get cart items grouped by vendor
     */
    public function getCartItemsByVendorProperty(): array
    {
        try {
            return $this->cartService->getCartItemsByVendor()->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Show message to user
     */
    private function showMessage(string $message, string $type = 'success'): void
    {
        $this->message = $message;
        $this->messageType = $type;
        
        // Clear message after 3 seconds
        $this->dispatch('show-cart-message', [
            'message' => $message,
            'type' => $type
        ]);
    }

    /**
     * Broadcast cart update to other users/sessions
     */
    private function broadcastCartUpdate(): void
    {
        try {
            $channelName = Auth::check() 
                ? "cart.user." . Auth::id()
                : "cart.guest." . Session::get('guest_cart_token');

            broadcast(new \App\Events\CartUpdated([
                'channel' => $channelName,
                'totals' => $this->cartTotals,
                'timestamp' => now()->toISOString()
            ]));
        } catch (\Exception $e) {
            // Silently fail broadcasting to not interrupt user experience
            logger()->warning('Failed to broadcast cart update: ' . $e->getMessage());
        }
    }

    /**
     * Get formatted price
     */
    public function formatPrice(float $price): string
    {
        return '₱' . number_format($price, 2);
    }

    public function render()
    {
        return view('livewire.cart-panel');
    }
}