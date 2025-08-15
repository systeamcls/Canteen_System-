<?php

namespace App\Policies;

use App\Models\Stall;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StallPolicy
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
    public function view(User $user, Stall $stall): bool
    {
        return $user->hasRole('admin') && 
               $user->admin_stall_id !== null && 
               $stall->id === $user->admin_stall_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only super admins can create stalls
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Stall $stall): bool
    {
        return $user->hasRole('admin') && 
               $user->admin_stall_id !== null && 
               $stall->id === $user->admin_stall_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Stall $stall): bool
    {
        // Only super admins can delete stalls
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Stall $stall): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Stall $stall): bool
    {
        return $user->hasRole('super_admin');
    }
}