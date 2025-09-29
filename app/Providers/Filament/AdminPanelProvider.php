<?php

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



class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->colors([
                'primary' => Color::Red,
            ])
            ->darkMode()
            ->brandName('Canteen Admin')
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: 'App\\Filament\\Admin\\Pages'
            )
            ->pages([
            \App\Filament\Admin\Pages\DailyAttendance::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
            )
            ->widgets([
                \App\Filament\Admin\Widgets\SalesAnalyticsWidget::class,
                \App\Filament\Admin\Widgets\RentalAnalyticsWidget::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Canteen Management'),
                NavigationGroup::make('Admin Management'),
                NavigationGroup::make('System'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // CRITICAL: Redirect after login based on role
            ->loginRouteSlug('login')
            ->homeUrl(function () {
                /** @var \App\Models\User $user */
                $user = Auth::user();

                if (!$user) return '/login';
                
                if ($user->hasRole('admin') || $user->hasRole('cashier')) {
                    return '/admin';
                } elseif ($user->hasRole('tenant')) {
                    return '/tenant';
                } else {
                    // Unauthorized users get logged out and redirected
                    Auth::logout();
                    return '/login';
                }
            });
    }
}