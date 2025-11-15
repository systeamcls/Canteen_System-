<?php
// app/Providers/Filament/CashierPanelProvider.php - FIXED VERSION

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Auth;

class CashierPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cashier')
            ->path('cashier')
            ->login()
            ->authGuard('web')
            ->colors([
                'primary' => Color::Emerald,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'info' => Color::Blue,
                'gray' => Color::Slate,
            ])
            ->darkMode()
            ->brandName('💰Cashier Panel')
            ->favicon(asset('favicon.ico'))
            
            // Basic panel configuration
            ->font('Inter')
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()

            ->resources([
            \App\Filament\Cashier\Resources\CashierProductResource::class,
            \App\Filament\Cashier\Resources\CashierOrderResource::class,
        ])
            
            // Discovery paths for cashier resources
            ->discoverResources(
                in: app_path('Filament/Cashier/Resources'),
                for: 'App\\Filament\\Cashier\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Cashier/Pages'),
                for: 'App\\Filament\\Cashier\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Cashier/Widgets'),
                for: 'App\\Filament\\Cashier\\Widgets'
            )
            
            // Navigation groups
            ->navigationGroups([
                NavigationGroup::make('🎯 Operations')
                    ->label('Daily Operations')
                    ->icon('heroicon-o-bolt')
                    ->collapsed(false),
                    
                NavigationGroup::make('📊 Analytics')
                    ->label('Reports & Analytics')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed(true),
                    
                NavigationGroup::make('⚙️ Settings')
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ])
            
            // Middleware stack
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
            ])
            
            // Auth middleware with cashier access check
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\CheckCashierAccess::class,
                \App\Http\Middleware\EnsureWelcomeModalCompleted::class,
                // Removed 2FA middleware for now to avoid issues
            ])
            
            // Global search configuration
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            // Custom home URL with role-based redirect
            ->homeUrl(function () {
                $user = Auth::user();

                if (!$user) {
                    return '/login';
                }

                // Redirect based on user role
                if ($user->hasRole('cashier') || $user->hasRole('admin')) {
                    return '/cashier';
                } elseif ($user->hasRole('tenant') && $user->is_active) {
                    return '/tenant';
                } else {
                    // Customers or users without panel access
                    return '/home';
                }
            });
    }
}