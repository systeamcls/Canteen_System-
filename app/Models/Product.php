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
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stall_id',
        'name',
        'description',
        'price',
        'image',            // keep existing
        'image_url',        // keep from enhanced if you’re storing URLs
        'category_id',      // use proper foreign key
        'is_available',
        'is_published',
        'preparation_time',
        'created_by',
    ];

    protected $casts = [
        'stall_id'        => 'integer',
        'price'           => 'integer', // store as money with 2 decimals
        'is_available'    => 'boolean',
        'is_published'    => 'boolean',
        'category_id'     => 'integer',
        'preparation_time'=> 'integer',
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

    /* ---------------- Domain Methods ---------------- */

    public function getFormattedPrice(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    public function isAvailable(): bool
    {
        return $this->is_available && $this->stall?->is_active;
    }

    public function toggleAvailability(): void
    {
        $this->update(['is_available' => !$this->is_available]);
    }

    /**
     * Get reviews for this product
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
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
}
