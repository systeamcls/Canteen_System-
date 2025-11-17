<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        // ⭐ Check both availability AND stock
        $this->disabled = !$product->is_available || $product->stock_quantity <= 0;
    }

    /**
     * Add product to cart WITH STOCK CHECKING
     */
    public function addToCart(): void
    {
        // ⭐ Check availability
        if (!$this->product->is_available) {
            $this->dispatch('show-notification', [
                'message' => 'This product is currently unavailable',
                'type' => 'error'
            ]);
            return;
        }

        // ⭐ Check stock before validation
        if ($this->product->stock_quantity <= 0) {
            $this->dispatch('show-notification', [
                'message' => 'Sorry, this item is out of stock',
                'type' => 'error'
            ]);
            $this->disabled = true;
            return;
        }

        // ⭐ Check if requested quantity exceeds stock
        if ($this->quantity > $this->product->stock_quantity) {
            $this->dispatch('show-notification', [
                'message' => "Only {$this->product->stock_quantity} items available in stock",
                'type' => 'error'
            ]);
            $this->quantity = $this->product->stock_quantity;
            return;
        }

        $this->validate();
        $this->isLoading = true;

        try {
            // ⭐ Use database transaction with row locking for race condition protection
            DB::beginTransaction();

            // Lock the product row to prevent race conditions
            $product = Product::where('id', $this->product->id)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                DB::rollBack();
                $this->dispatch('show-notification', [
                    'message' => 'Product not found',
                    'type' => 'error'
                ]);
                return;
            }

            // ⭐ Re-check stock after locking (in case another customer just bought it)
            if ($product->stock_quantity < $this->quantity) {
                DB::rollBack();
                
                if ($product->stock_quantity <= 0) {
                    $this->dispatch('show-notification', [
                        'message' => 'Sorry! This item just sold out',
                        'type' => 'error'
                    ]);
                    $this->disabled = true;
                } else {
                    $this->dispatch('show-notification', [
                        'message' => "Only {$product->stock_quantity} items left in stock",
                        'type' => 'error'
                    ]);
                    $this->quantity = $product->stock_quantity;
                }
                
                // Refresh the product data
                $this->product = $product;
                return;
            }

            // Add to cart using CartService
            $result = $this->cartService->addToCart($this->product->id, $this->quantity);

            if ($result['success']) {
                // ⭐ OPTIONAL: Reserve stock when added to cart
                // Uncomment the next line if you want to reserve stock immediately
                // $product->decrement('stock_quantity', $this->quantity);
                
                DB::commit();

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
                
                // Refresh product to get updated stock
                $this->product = $product;
            } else {
                DB::rollBack();
                $this->dispatch('show-notification', [
                    'message' => $result['message'],
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('show-notification', [
                'message' => 'Error adding item to cart',
                'type' => 'error'
            ]);
            Log::error('Add to cart error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Increment quantity WITH STOCK CHECK
     */
    public function incrementQuantity(): void
    {
        // ⭐ Check against available stock
        if ($this->quantity < 99 && $this->quantity < $this->product->stock_quantity) {
            $this->quantity++;
        } else if ($this->quantity >= $this->product->stock_quantity) {
            $this->dispatch('show-notification', [
                'message' => "Maximum available: {$this->product->stock_quantity}",
                'type' => 'warning'
            ]);
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
     * Toggle quantity selector
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
        $this->message = 'Added to cart!';
        $this->messageType = 'success';
    }

    /**
     * Format price for display
     */
    public function formatPrice(int $priceInCents): string
    {
        return '₱' . number_format($priceInCents / 100, 2);
    }

    /**
     * Check if product has low stock
     */
    public function isLowStock(): bool
    {
        return $this->product->stock_quantity > 0 && 
               $this->product->stock_quantity <= ($this->product->low_stock_alert ?? 10);
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->product->stock_quantity <= 0;
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}