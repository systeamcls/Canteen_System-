<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'payment_reference',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }
}