<?php
// 📁 app/Models/CartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'vendor_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'cart_id' => 'integer',
        'product_id' => 'integer',
        'vendor_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'float',  // ✅ Fixed
        'line_total' => 'float',  // ✅ Fixed
    ];

    // Relationships
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Stall::class, 'vendor_id');  // ✅ Fixed
    }

    // Domain Methods
    public function updateLineTotal(): void
    {
        $this->line_total = $this->quantity * $this->unit_price;
        $this->save();
    }

    public function getFormattedPrice(): string
    {
    return '₱' . number_format((float) $this->unit_price, 2);
    }

    public function getFormattedLineTotal(): string
    {
    return '₱' . number_format((float) $this->line_total, 2);
}
}