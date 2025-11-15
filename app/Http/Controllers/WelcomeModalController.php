<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeModalController extends Controller
{
    /**
     * Show the welcome modal page.
     */
    public function show()
    {
        // If user is not authenticated, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Determine which panel the user should access based on their role
        $availablePanels = $this->getUserAvailablePanels($user);

        return view('welcome-modal', [
            'user' => $user,
            'availablePanels' => $availablePanels,
        ]);
    }

    /**
     * Complete the welcome modal and redirect to appropriate panel.
     */
    public function complete(Request $request)
    {
        // Mark welcome modal as completed in session
        session()->put('welcome_modal_completed', true);

        $user = Auth::user();

        // Get intended URL from session or determine based on role
        $intendedUrl = session()->pull('intended_panel_url');

        if ($intendedUrl) {
            return redirect($intendedUrl);
        }

        // Default redirection based on user role
        return redirect($this->getDefaultPanelUrl($user));
    }

    /**
     * Get available panels for the user based on their roles.
     */
    private function getUserAvailablePanels($user): array
    {
        $panels = [];

        if ($user->hasRole('admin')) {
            $panels[] = [
                'name' => 'Admin Panel',
                'url' => '/admin',
                'description' => 'Full system administration',
                'icon' => 'heroicon-o-cog-6-tooth',
            ];
        }

        if ($user->hasRole('tenant') && $user->is_active) {
            $panels[] = [
                'name' => 'Tenant Panel',
                'url' => '/tenant',
                'description' => 'Manage your stall',
                'icon' => 'heroicon-o-building-storefront',
            ];
        }

        if ($user->hasRole('cashier')) {
            $panels[] = [
                'name' => 'Cashier Panel',
                'url' => '/cashier',
                'description' => 'Point of sale system',
                'icon' => 'heroicon-o-banknotes',
            ];
        }

        return $panels;
    }

    /**
     * Get default panel URL based on user's primary role.
     */
    private function getDefaultPanelUrl($user): string
    {
        if ($user->hasRole('admin')) {
            return '/admin';
        }

        if ($user->hasRole('cashier')) {
            return '/cashier';
        }

        if ($user->hasRole('tenant') && $user->is_active) {
            return '/tenant';
        }

        // Fallback: logout if no valid role
        Auth::logout();
        return '/login';
    }
}
