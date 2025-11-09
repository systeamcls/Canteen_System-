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
    public function view(User $currentUser, User $targetUser): bool
    {
        // Admins can view tenants, cashiers, and customers (not other admins)
        return $currentUser->hasRole('admin') && 
               $targetUser->hasAnyRole(['tenant', 'cashier', 'customer']) &&
               !$targetUser->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create users
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $currentUser, User $targetUser): bool
    {
        // Admins can update tenants, cashiers, and customers (not other admins)
        return $currentUser->hasRole('admin') && 
               $targetUser->hasAnyRole(['tenant', 'cashier', 'customer']) &&
               !$targetUser->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $targetUser): bool
    {
        // Admins can delete non-admin users, but not themselves
        return $currentUser->hasRole('admin') && 
               $currentUser->id !== $targetUser->id &&
               !$targetUser->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $currentUser, User $targetUser): bool
    {
        return $this->update($currentUser, $targetUser);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $currentUser, User $targetUser): bool
    {
        return $this->delete($currentUser, $targetUser);
    }
}