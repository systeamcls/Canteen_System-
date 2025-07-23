<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'name',
        'description',
        'price',
        'image',
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