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
use App\Filament\Admin\Widgets\TrendingItemsWidget;
use App\Filament\Admin\Widgets\RecentReviewsWidget;
use Filament\Support\Enums\MaxWidth;

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
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => [
                    50 => '#fef2f2',
                    100 => '#fee2e2',
                    200 => '#fecaca',
                    300 => '#fca5a5',
                    400 => '#f87171',
                    500 => '#ef4444', // Main red color
                    600 => '#dc2626',
                    700 => '#b91c1c',
                    800 => '#991b1b',
                    900 => '#7f1d1d',
                    950 => '#450a0a',
                ],
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->darkMode(true) // Enable dark mode by default
            ->darkModeForced(true) // Force dark mode
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
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
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
                \App\Http\Middleware\Enforce2FA::class,
                \App\Http\Middleware\EnsureAdminHasStall::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandName('Canteen Admin')
            ->brandLogo(asset('images/logo.png'))
            ->favicon(asset('images/favicon.ico'))
            ->maxContentWidth(MaxWidth::Full)
            ->navigationGroups([
                'Overview' => [
                    'label' => 'Overview',
                    'icon' => 'heroicon-o-chart-pie',
                    'sort' => 1,
                ],
                'Management' => [
                    'label' => 'Management',
                    'icon' => 'heroicon-o-building-storefront',
                    'sort' => 2,
                ],
                'HR' => [
                    'label' => 'Human Resources',
                    'icon' => 'heroicon-o-users',
                    'sort' => 3,
                ],
                'Analytics' => [
                    'label' => 'Analytics',
                    'icon' => 'heroicon-o-chart-bar',
                    'sort' => 4,
                ],
                'System' => [
                    'label' => 'System',
                    'icon' => 'heroicon-o-cog-6-tooth',
                    'sort' => 5,
                ],
            ])
            ->widgets([
                StatsOverviewWidget::class,
                LatestOrdersWidget::class,
                SalesChartWidget::class,
                TrendingItemsWidget::class,
                RecentReviewsWidget::class,
            ])
            ->topNavigation()
            ->sidebarCollapsibleOnDesktop()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->breadcrumbs(false)
            ->userMenuItems([
                'profile' => 'Profile',
                'logout' => 'Sign Out',
            ]);
    }
}
