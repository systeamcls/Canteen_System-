<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;


/**
 * @property int $id    
 * @property string $name
 * @property float $price
 * @property string|null $image
 * @property bool $is_available
 * @property bool $is_published
 * @property int|null $preparation_time
 * @property int|null $created_by
 * @property int $stock_quantity
 * @property int $low_stock_alert
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stall_id',
        'name',
        'description',
        'price',
        'image',            
        'image_url',        
        'category_id',      
        'is_available',
        'is_published',
        'preparation_time',
        'created_by',
        // New stock management fields
        'stock_quantity',
        'low_stock_alert',
    ];

    protected $casts = [
        'stall_id'        => 'integer',
        'price'           => 'integer', 
        'is_available'    => 'boolean',
        'is_published'    => 'boolean',
        'category_id'     => 'integer',
        'preparation_time'=> 'integer',
        // New stock management casts
        'stock_quantity'  => 'integer',
        'low_stock_alert' => 'integer',
    ];

    /* ---------------- Relationships ---------------- */

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get reviews for this product
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /* ---------------- Scopes ---------------- */

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->whereHas('stall', fn($q) => $q->where('is_active', true));
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= low_stock_alert AND stock_quantity > 0');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /* ---------------- Domain Methods (Existing) ---------------- */

    public function getFormattedPrice(): string
    {
        return '₱' . number_format($this->price / 100, 2); // Divide by 100 since price is stored as cents
    }

    public function isAvailable(): bool
    {
        return $this->is_available && $this->stall?->is_active && $this->stock_quantity > 0;
    }

    public function toggleAvailability(): void
    {
        // Only allow enabling if there's stock
        if (!$this->is_available && $this->stock_quantity <= 0) {
            return; // Don't enable if out of stock
        }
        
        $this->update(['is_available' => !$this->is_available]);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/default-product.png');
        }

        // Check if file exists and return proper URL
        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return asset('images/default-product.png');
    }

    /* ---------------- Stock Management Methods (New) ---------------- */

    /**
     * Decrease stock quantity and auto-disable if out of stock
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            
            // Auto-disable if out of stock
            if ($this->stock_quantity <= 0) {
                $this->update(['is_available' => false]);
                
                // Log for admin
                logger("Product {$this->name} (ID: {$this->id}) automatically disabled - out of stock");
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Increase stock quantity and auto-enable if was disabled due to stock
     */
    public function increaseStock(int $quantity): void
    {
        $wasOutOfStock = $this->stock_quantity <= 0;
        
        $this->increment('stock_quantity', $quantity);
        
        // Re-enable if stock is added and was previously disabled due to stock
        if ($wasOutOfStock && $this->stock_quantity > 0 && !$this->is_available) {
            $this->update(['is_available' => true]);
            logger("Product {$this->name} (ID: {$this->id}) automatically enabled - stock available");
        }
    }

    /**
     * Set exact stock quantity
     */
    public function setStock(int $quantity): void
    {
        $oldQuantity = $this->stock_quantity;
        $this->update(['stock_quantity' => $quantity]);
        
        // Auto-enable/disable based on stock
        if ($quantity > 0 && $oldQuantity <= 0 && !$this->is_available) {
            $this->update(['is_available' => true]);
            logger("Product {$this->name} (ID: {$this->id}) automatically enabled - stock set to {$quantity}");
        } elseif ($quantity <= 0 && $this->is_available) {
            $this->update(['is_available' => false]);
            logger("Product {$this->name} (ID: {$this->id}) automatically disabled - stock set to {$quantity}");
        }
    }

    /**
     * Check if product has low stock
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_alert && $this->stock_quantity > 0;
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Get stock status as string
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get stock status color for UI
     */
    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'in_stock' => 'success',
        };
    }

    /**
     * Get human-readable stock status
     */
    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
        };
    }

    /**
     * Check if product can be ordered with given quantity
     */
    public function canOrder(int $quantity = 1): bool
    {
        return $this->is_available 
            && $this->is_published 
            && $this->stall?->is_active 
            && $this->stock_quantity >= $quantity;
    }

    /**
     * Get maximum orderable quantity
     */
    public function getMaxOrderableQuantity(): int
    {
        if (!$this->isAvailable()) {
            return 0;
        }
        
        return $this->stock_quantity;
    }
}