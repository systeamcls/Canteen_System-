<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    private const GUEST_TOKEN_KEY = 'guest_cart_token';
    private const TOKEN_LENGTH = 32;

    /**
     * Get or create cart for current user/guest
     */
    public function getCart(): Cart
    {
        if (Auth::check()) {
            return $this->getUserCart();
        }

        return $this->getGuestCart();
    }

    /**
     * Add product to cart
     */
    public function addToCart(int $productId, int $quantity = 1): array
    {
        $product = Product::with('stall')->findOrFail($productId);

        if (!$product->is_available) {
            return [
                'success' => false,
                'message' => 'Product is currently unavailable'
            ];
        }

        if ($quantity <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid quantity'
            ];
        }

        $cart = $this->getCart();
        $existingItem = $cart->items()->where('product_id', $productId)->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            return $this->updateQuantity($existingItem->id, $newQuantity);
        }

        $cartItem = $cart->items()->create([
            'product_id' => $productId,
            'vendor_id' => $product->stall_id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'line_total' => $product->price * $quantity,
        ]);

        $this->recalculateCart($cart);

        return [
            'success' => true,
            'message' => 'Product added to cart',
            'cart_item' => $cartItem,
            'cart_totals' => $this->getCartTotals($cart)
        ];
    }

    /**
     * Remove product from cart
     */
    public function removeFromCart(int $cartItemId): array
    {
        $cart = $this->getCart();
        $cartItem = $cart->items()->findOrFail($cartItemId);

        $cartItem->delete();
        $this->recalculateCart($cart);

        return [
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_totals' => $this->getCartTotals($cart)
        ];
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $cartItemId, int $quantity): array
    {
        if ($quantity < 0) {
            return [
                'success' => false,
                'message' => 'Invalid quantity'
            ];
        }

        $cart = $this->getCart();
        $cartItem = $cart->items()->findOrFail($cartItemId);

        if ($quantity === 0) {
            return $this->removeFromCart($cartItemId);
        }

        // Check product availability
        $product = $cartItem->product;
        if (!$product->is_available) {
            return [
                'success' => false,
                'message' => 'Product is no longer available'
            ];
        }

        $cartItem->update([
            'quantity' => $quantity,
            'unit_price' => $product->price, // Update price in case it changed
            'line_total' => $product->price * $quantity,
        ]);

        $this->recalculateCart($cart);

        return [
            'success' => true,
            'message' => 'Quantity updated',
            'cart_item' => $cartItem->fresh(),
            'cart_totals' => $this->getCartTotals($cart)
        ];
    }

    /**
     * Get cart totals and summary
     */
    public function getCartTotals(?Cart $cart = null): array
    {
        $cart = $cart ?? $this->getCart();
        $items = $cart->items()->with(['product', 'vendor'])->get();

        $subtotal = $items->sum('line_total');
        $itemCount = $items->sum('quantity');
        $vendorCount = $items->pluck('vendor_id')->unique()->count();

        return [
            'subtotal' => $subtotal,
            'tax' => 0, // Implement tax calculation if needed
            'total' => $subtotal,
            'item_count' => $itemCount,
            'vendor_count' => $vendorCount,
            'items' => $items
        ];
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): array
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        
        return [
            'success' => true,
            'message' => 'Cart cleared'
        ];
    }

    /**
     * Get cart items grouped by vendor
     */
    public function getCartItemsByVendor(): Collection
    {
        $cart = $this->getCart();
        
        return $cart->items()
            ->with(['product', 'vendor'])
            ->get()
            ->groupBy('vendor_id');
    }

    /**
     * Transfer guest cart to user cart on login
     */
    public function transferGuestCartToUser(int $userId): void
    {
        $guestToken = Session::get(self::GUEST_TOKEN_KEY);
        
        if (!$guestToken) {
            return;
        }

        $guestCart = Cart::where('guest_token', $guestToken)->first();
        
        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        // Transfer items from guest cart to user cart
        foreach ($guestCart->items as $guestItem) {
            $existingItem = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existingItem) {
                // Merge quantities
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $guestItem->quantity,
                    'line_total' => $existingItem->unit_price * ($existingItem->quantity + $guestItem->quantity),
                ]);
            } else {
                // Move item to user cart
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        // Delete guest cart and clear session
        $guestCart->delete();
        Session::forget(self::GUEST_TOKEN_KEY);

        $this->recalculateCart($userCart);
    }

    /**
     * Validate cart items (check availability and prices)
     */
    public function validateCart(): array
    {
        $cart = $this->getCart();
        $items = $cart->items()->with('product')->get();
        $issues = [];
        $hasChanges = false;

        foreach ($items as $item) {
            $product = $item->product;

            // Check availability
            if (!$product->is_available) {
                $issues[] = [
                    'type' => 'unavailable',
                    'item' => $item,
                    'message' => "{$product->name} is no longer available"
                ];
                $item->delete();
                $hasChanges = true;
                continue;
            }

            // Check price changes
            if ($item->unit_price != $product->price) {
                $issues[] = [
                    'type' => 'price_change',
                    'item' => $item,
                    'old_price' => $item->unit_price,
                    'new_price' => $product->price,
                    'message' => "{$product->name} price has changed"
                ];

                $item->update([
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $item->quantity,
                ]);
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $this->recalculateCart($cart);
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'cart_totals' => $this->getCartTotals($cart)
        ];
    }

    /**
     * Get user cart
     */
    private function getUserCart(): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Get guest cart
     */
    private function getGuestCart(): Cart
    {
        $token = Session::get(self::GUEST_TOKEN_KEY);

        if (!$token) {
            $token = Str::random(self::TOKEN_LENGTH);
            Session::put(self::GUEST_TOKEN_KEY, $token);
        }

        return Cart::firstOrCreate([
            'guest_token' => $token
        ]);
    }

    /**
     * Recalculate cart totals
     */
    private function recalculateCart(Cart $cart): void
    {
        // This method can be extended to update cart-level totals
        // if you add total columns to the carts table
        $cart->touch(); // Update timestamp to indicate cart was modified
    }
}