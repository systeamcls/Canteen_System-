<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') && $user->admin_stall_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if (!$user->hasRole('admin') || $user->admin_stall_id === null) {
            return false;
        }

        // Check if the order contains products from the admin's stall
        return $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('stall_id', $user->admin_stall_id);
        })->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') && $user->admin_stall_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        if (!$user->hasRole('admin') || $user->admin_stall_id === null) {
            return false;
        }

        // Check if the order contains products from the admin's stall
        return $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('stall_id', $user->admin_stall_id);
        })->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $this->update($user, $order);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return $this->update($user, $order);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $this->delete($user, $order);
    }
}