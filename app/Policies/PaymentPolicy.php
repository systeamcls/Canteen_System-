<?php

namespace App\Policies;

use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine if the user can use onsite payment methods.
     */
    public function useOnsitePayment(?User $user): bool
    {
        // Only authenticated users can use onsite payment
        return $user !== null;
    }

    /**
     * Determine if the user can use online payment methods.
     */
    public function useOnlinePayment(?User $user): bool
    {
        // Both guests and authenticated users can use online payment
        return true;
    }

    /**
     * Determine if the user can browse as guest.
     */
    public function browseAsGuest(?User $user): bool
    {
        // Anyone can browse as guest
        return true;
    }

    /**
     * Determine if the user can access cashier features.
     */
    public function accessCashier(?User $user): bool
    {
        return $user?->hasAnyRole(['admin', 'cashier']) ?? false;
    }

    /**
     * Determine if the user can manage stalls.
     */
    public function manageStalls(?User $user): bool
    {
        return $user?->hasAnyRole(['admin', 'tenant']) ?? false;
    }

    /**
     * Determine if the user can view reports.
     */
    public function viewReports(?User $user): bool
    {
        return $user?->hasAnyRole(['admin', 'tenant']) ?? false;
    }
}
