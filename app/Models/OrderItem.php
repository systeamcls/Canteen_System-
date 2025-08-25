<?php

// 📁 app/Models/OrderItem.php (UPDATED - Safe merge with your existing model)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        // Your existing fields
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'price',
        'subtotal',
        'special_instructions',

        // New fields from Module 2 (will be added via migration)
        'product_name',  // denormalized for history
        'line_total',    // in cents
    ];

    protected $casts = [
        // Your existing casts
        'unit_price' => 'decimal:2',
        'price' => 'integer',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',

        // New casts
        'line_total' => 'integer', // in cents
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Your existing + enhanced)
    |--------------------------------------------------------------------------
    */

    // ENHANCED: Handle both price fields for compatibility
    public function getPriceAttribute()
    {
        return $this->unit_price ?? $this->attributes['price'] ?? 0;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
        $this->attributes['price'] = $value;
    }

    // ENHANCED: Calculate subtotal with fallback to new line_total
    public function getSubtotalAttribute(): float
    {
        // If we have line_total in cents, use that
        if ($this->line_total) {
            return $this->line_total / 100;
        }

        // Fallback to old calculation
        return $this->quantity * $this->price;
    }

    /*
    |--------------------------------------------------------------------------
    | New Domain Methods
    |--------------------------------------------------------------------------
    */

    // NEW: Get formatted unit price
    public function getFormattedUnitPrice(): string
    {
        // Handle both old (decimal) and new (cents) price formats
        $price = $this->line_total 
            ? ($this->line_total / $this->quantity) / 100
            : (float) $this->price;

        return '₱' . number_format($price, 2);
    }

    // NEW: Get formatted line total
    public function getFormattedLineTotal(): string
    {
        $total = $this->line_total 
            ? $this->line_total / 100
            : $this->subtotal;

        return '₱' . number_format($total, 2);
    }

    // NEW: Get total price in cents
    public function getTotalInCents(): int
    {
        if ($this->line_total) {
            return $this->line_total;
        }

        // Convert old subtotal to cents
        return (int) round($this->subtotal * 100);
    }

    // NEW: Get unit price in cents
    public function getUnitPriceInCents(): int
    {
        if ($this->line_total) {
            return (int) round($this->line_total / $this->quantity);
        }

        // Convert old price to cents
        return (int) round((float) $this->price * 100);
    }

    // NEW: Update line total when quantity or price changes
    public function updateLineTotal(): void
    {
        if ($this->line_total !== null) {
            // Working with cents
            $unitPriceInCents = $this->getUnitPriceInCents();
            $this->line_total = $this->quantity * $unitPriceInCents;
        } else {
            // Working with decimals (old format)
            $this->subtotal = $this->quantity * $this->price;
        }
        
        $this->save();
    }

    // NEW: Get product name with fallback
    public function getProductNameAttribute(): string
    {
        return $this->attributes['product_name'] ?? $this->product?->name ?? 'Unknown Product';
    }
}

