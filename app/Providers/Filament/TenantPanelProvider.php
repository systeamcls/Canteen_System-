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
use App\Filament\Tenant\Widgets;

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('tenant')
            ->login()
            ->colors([
                'primary' => [
                    50 => '#fff8e1',
                    100 => '#ffecb3',
                    200 => '#ffe082',
                    300 => '#ffd54f',
                    400 => '#ffca28',
                    500 => '#ffc107',
                    600 => '#ffb300',
                    700 => '#ffa000',
                    800 => '#ff8f00',
                    900 => '#ff6f00',
                ],
            ])
            ->discoverResources(
                in: app_path('Filament/Tenant/Resources'),
                for: 'App\\Filament\\Tenant\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Tenant/Pages'),
                for: 'App\\Filament\\Tenant\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Tenant/Widgets'),
                for: 'App\\Filament\\Tenant\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                Authenticate::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandName('Stall Dashboard')
            ->navigationGroups([
                'Menu Management',
                'Orders',
                'Reviews',
                'Reports',
                'Settings'
            ])
            ->widgets([
                Widgets\StallStatsWidget::class,
                Widgets\TodayOrdersWidget::class,
                Widgets\SalesChartWidget::class,
            ]);
            
    }
}