<?php

// 📁 app/Models/Cart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Domain Methods
    public static function resolveForUser(?User $user = null, ?string $guestToken = null): self
    {
        if ($user) {
            return static::firstOrCreate(['user_id' => $user->id]);
        }

        if ($guestToken) {
            return static::firstOrCreate(['guest_token' => $guestToken]);
        }

        // Generate new guest token
        $newGuestToken = Str::random(40);
        return static::create(['guest_token' => $newGuestToken]);
    }

    public function getItemCount(): int
    {
        return $this->items()->sum('quantity');
    }

    public function getSubtotal(): int
    {
        return $this->items()->sum('line_total');
    }

    public function getTotal(): int
    {
        return $this->getSubtotal(); // Can add taxes/fees here later
    }

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function getOwnerIdentifier(): string
    {
        return $this->user_id ? "user:{$this->user_id}" : "guest:{$this->guest_token}";
    }

    public function clear(): void
    {
        $this->items()->delete();
    }
}
