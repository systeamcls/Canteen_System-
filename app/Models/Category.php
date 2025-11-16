<?php

// 📁 app/Models/Category.php (Enhanced existing model if needed)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Domain Methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getActiveProductsCount(): int
    {
        return $this->products()->where('is_available', true)->count();
    }

    /**
     * 🔥 NEW: Get Font Awesome icon class based on category name (fallback)
     */
    public function getIconClass(): string
    {
        $iconMap = [
            'fresh meals' => 'utensils',
            'sandwiches' => 'bread-slice',
            'beverages' => 'mug-hot',
            'snacks' => 'cookie',
            'boxed meals' => 'box',
            'desserts' => 'ice-cream',
            'breakfast' => 'egg',
            'lunch' => 'bowl-rice',
            'dinner' => 'drumstick-bite',
            'coffee' => 'coffee',
            'drinks' => 'glass-water',
            'pizza' => 'pizza-slice',
            'burger' => 'burger',
            'noodles' => 'bowl-food',
        ];

        $name = strtolower($this->name);
        
        // Try exact match first
        if (isset($iconMap[$name])) {
            return $iconMap[$name];
        }

        // Try partial match
        foreach ($iconMap as $keyword => $icon) {
            if (str_contains($name, $keyword)) {
                return $icon;
            }
        }

        // Default icon
        return 'utensils';
    }

    /**
     * 🔥 NEW: Get full image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}