<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property float $price
 * @property string|null $image
 * @property \App\Models\Stall|null $stall
 *  @property bool $is_available
 */

class Product extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'name',
        'description',
        'price',
        'image',
        'category',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    
}