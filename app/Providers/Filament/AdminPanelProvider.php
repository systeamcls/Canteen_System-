<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Dashboard;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use App\Filament\Admin\Widgets\LatestOrdersWidget;
use App\Filament\Admin\Widgets\SalesChartWidget;


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
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: 'App\\Filament\\Admin\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: 'App\\Filament\\Admin\\Pages'
            )
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class, // ⬅️ Add this
            ])
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
            ->brandName('Canteen Admin')
            ->navigationGroups([
                'Stall Management',
                'Orders',
                'Staff',
                'Reports',
                'Settings'
            ])
            ->widgets([
                StatsOverviewWidget::class,
                LatestOrdersWidget::class,
                SalesChartWidget::class,
            ]);
    }
}
