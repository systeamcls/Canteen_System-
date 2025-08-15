<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $currentUser, User $user): bool
    {
        // Admin can only view tenants and cashiers, not other admins or customers
        return $currentUser->hasRole('admin') && 
               ($user->hasRole('tenant') || $user->hasRole('cashier'));
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
    public function update(User $currentUser, User $user): bool
    {
        // Admin can only update tenants and cashiers
        return $currentUser->hasRole('admin') && 
               ($user->hasRole('tenant') || $user->hasRole('cashier'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $user): bool
    {
        // Admin can only delete tenants and cashiers, not themselves
        return $currentUser->hasRole('admin') && 
               $currentUser->id !== $user->id &&
               ($user->hasRole('tenant') || $user->hasRole('cashier'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $currentUser, User $user): bool
    {
        return $this->update($currentUser, $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $currentUser, User $user): bool
    {
        return $this->delete($currentUser, $user);
    }
}