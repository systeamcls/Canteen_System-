<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddToCartButton extends Component
{
    public Product $product;
    
    #[Validate('required|integer|min:1|max:99')]
    public int $quantity = 1;
    
    public bool $isLoading = false;
    public bool $showQuantitySelector = false;
    public string $buttonText = 'Add to Cart';
    public string $buttonSize = 'medium'; // small, medium, large
    public bool $showPrice = true;
    public bool $disabled = false;
    public string $message = '';
    public string $messageType = 'success';

    protected CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(
        Product $product,
    int $quantity = 1,
    bool $showQuantitySelector = false,
    string $buttonText = 'Add to Cart',
    string $buttonSize = 'medium',
    bool $showPrice = true
): void {
    $this->product = $product;
    $this->quantity = $quantity;
    $this->showQuantitySelector = $showQuantitySelector;
    $this->buttonText = $buttonText;
    $this->buttonSize = $buttonSize;
    $this->showPrice = $showPrice;
    
    // Disable if product is unavailable OR out of stock
    $this->disabled = !$product->is_available || $product->stock_quantity <= 0;
    }

    /**
     * Add product to cart
     */
    public function addToCart(): void
    {
        // Check availability
    if (!$this->product->is_available) {
        $this->dispatch('show-notification', [
            'message' => 'This product is currently unavailable',
            'type' => 'error'
        ]);
        return;
    }

    // Check stock quantity
    if ($this->product->stock_quantity <= 0) {
        $this->dispatch('show-notification', [
            'message' => 'This product is out of stock',
            'type' => 'error'
        ]);
        return;
    }

    // Check if requested quantity exceeds stock
    if ($this->quantity > $this->product->stock_quantity) {
        $this->dispatch('show-notification', [
            'message' => "Only {$this->product->stock_quantity} units available",
            'type' => 'error'
        ]);
        return;
        }

        $this->validate();
        $this->isLoading = true;

        try {
            $result = $this->cartService->addToCart($this->product->id, $this->quantity);

            if ($result['success']) {
                // Dispatch event to update CartPanel
                $this->dispatch('cart-item-added', [
                    'product_id' => $this->product->id,
                    'quantity' => $this->quantity,
                    'cart_totals' => $result['cart_totals']
                ]);

                // Show success message
                $this->dispatch('show-notification', [
                    'message' => $result['message'],
                    'type' => 'success'
                ]);

                // Reset quantity if selector is shown
                if ($this->showQuantitySelector) {
                    $this->quantity = 1;
                }

                // Brief success state
                $this->showSuccessState();
            } else {
                $this->dispatch('show-notification', [
                    'message' => $result['message'],
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'message' => 'Error adding item to cart',
                'type' => 'error'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Increment quantity
     */
    public function incrementQuantity(): void
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    /**
     * Decrement quantity
     */
    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * Set specific quantity
     */
    public function setQuantity(int $quantity): void
    {
        if ($quantity >= 1 && $quantity <= 99) {
            $this->quantity = $quantity;
        }
    }

    /**
     * Toggle quantity selector visibility
     */
    public function toggleQuantitySelector(): void
    {
        $this->showQuantitySelector = !$this->showQuantitySelector;
    }

    /**
     * Show success state briefly
     */
    private function showSuccessState(): void
    {
        $originalText = $this->buttonText;
        $this->buttonText = 'Added!';
        
        // Reset button text after 2 seconds
        $this->dispatch('reset-button-text', [
            'originalText' => $originalText,
            'delay' => 2000
        ]);
    }

    /**
     * Reset button text (called from frontend)
     */
    public function resetButtonText(string $text): void
    {
        $this->buttonText = $text;
    }

    /**
     * Get button size classes
     */
    public function getButtonSizeClasses(): string
    {
        return match ($this->buttonSize) {
            'small' => 'px-3 py-1.5 text-sm',
            'large' => 'px-6 py-3 text-lg',
            default => 'px-4 py-2 text-base',
        };
    }

    /**
     * Get quantity selector size classes
     */
    public function getQuantitySelectorClasses(): string
    {
        return match ($this->buttonSize) {
            'small' => 'w-6 h-6 text-xs',
            'large' => 'w-10 h-10 text-lg',
            default => 'w-8 h-8 text-sm',
        };
    }

    /**
     * Format price for display
     */
    public function formatPrice(float $price): string
    {
        return '₱' . number_format($price / 100, 2);    
    }

    /**
     * Check if product is in stock
     */
    public function getIsInStockProperty(): bool
{
    return $this->product->is_available && $this->product->stock_quantity > 0;
}

    /**
     * Get total price for current quantity
     */
    public function getTotalPriceProperty(): float
    {
        return $this->product->price * $this->quantity;
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }

    public function getStockStatusProperty(): array
{
    $stock = $this->product->stock_quantity;
    $lowStockAlert = $this->product->low_stock_alert;

    if ($stock <= 0) {
        return [
            'text' => 'Out of Stock',
            'class' => 'out-of-stock',
            'color' => 'danger'
        ];
    }

    if ($stock <= $lowStockAlert) {
        return [
            'text' => "Only {$stock} left!",
            'class' => 'low-stock',
            'color' => 'warning'
        ];
    }

    return [
        'text' => "{$stock} available",
        'class' => 'in-stock',
        'color' => 'success'
    ];
}
}