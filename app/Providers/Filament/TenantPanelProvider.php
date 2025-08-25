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

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('tenant')
            ->login()
            ->authGuard('web')
            ->colors([
                'primary' => Color::Green,
            ])
            ->darkMode()
            ->brandName('Tenant Dashboard')
            ->discoverResources(
                in: app_path('Filament/Tenant/Resources'),
                for: 'App\\Filament\\Tenant\\Resources'
            )
            ->resources([
                \App\Filament\Tenant\Resources\TenantStallResource::class,
                \App\Filament\Tenant\Resources\TenantProductResource::class,
                \App\Filament\Tenant\Resources\TenantOrderResource::class,
            ])
            ->discoverPages(
                in: app_path('Filament/Tenant/Pages'),
                for: 'App\\Filament\\Tenant\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Tenant/Widgets'),
                for: 'App\\Filament\\Tenant\\Widgets'
            )
            ->navigationGroups([
                NavigationGroup::make('My Stall')
                    ->icon('heroicon-o-building-storefront'),
                NavigationGroup::make('Products')
                    ->icon('heroicon-o-cube'),
                NavigationGroup::make('Orders & Sales')
                    ->icon('heroicon-o-shopping-cart'),
                NavigationGroup::make('Reviews')
                    ->icon('heroicon-o-star'),
                NavigationGroup::make('Finance')
                    ->icon('heroicon-o-currency-dollar'),
                NavigationGroup::make('Reports')
                    ->icon('heroicon-o-chart-bar'),
                NavigationGroup::make('Account')
                    ->icon('heroicon-o-user-circle'),
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
            ->homeUrl('/tenant')
            ->sidebarCollapsibleOnDesktop();
            //->viteTheme('resources/css/filament/tenant/theme.css');
    }
}