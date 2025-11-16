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
use App\Filament\Admin\Pages\TwoFactorChallenge;



class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path(config('app.admin_prefix', 'admin')) // 🔥 Use .env prefix
            ->login(false) // 🔥 Disable default login (we use WelcomeModal)
            ->authGuard('web')
            ->colors([
            'primary' => Color::Red,    
        ])
            ->darkMode()
            ->brandName('Canteen Admin')
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
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
            TwoFactorChallenge::class,
            \App\Filament\Admin\Pages\FinancialDashboard::class,
            ])
            ->widgets([
                
                \App\Filament\Admin\Widgets\SalesAnalyticsWidget::class,
                \App\Filament\Admin\Widgets\RentalAnalyticsWidget::class,

                // Dashboard widgets (register them here!)
                \App\Filament\Admin\Widgets\AdminQuickStatsWidget::class,
                \App\Filament\Admin\Widgets\AdminSalesChartWidget::class,
                \App\Filament\Admin\Widgets\AdminPerformanceWidget::class,
                \App\Filament\Admin\Widgets\AdminOrderStatusWidget::class,
                \App\Filament\Admin\Widgets\AdminTopSellerWidget::class,
                \App\Filament\Admin\Widgets\AdminPopularHoursWidget::class,
                \App\Filament\Admin\Widgets\AdminLatestOrdersWidget::class,
                \App\Filament\Admin\Widgets\AdminTrendingItemsWidget::class,

                \App\Filament\Admin\Widgets\FinancialOverviewWidget::class,
                \App\Filament\Admin\Widgets\AdminSalesChartWidget::class,
                \App\Filament\Admin\Widgets\AdminOrderStatsWidget::class,
                \App\Filament\Admin\Widgets\AdminRevenueVsExpenseWidget::class,
                \App\Filament\Admin\Widgets\AdminExpenseBreakdownWidget::class,
                \App\Filament\Admin\Widgets\AdminRentalPaymentsWidget::class,
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
                \App\Http\Middleware\EnsurePanelAccess::class . ':admin',
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\FilamentTwoFactorAuth::class, // ✅ Keep 2FA for Admin
            ])
            ->loginRouteSlug('login')
            ->homeUrl(function () {
                /** @var \App\Models\User $user */
                $user = Auth::user();

                if (!$user) return '/';
                
                if ($user->hasRole('admin') || $user->hasRole('cashier')) {
                    return '/admin';
                } elseif ($user->hasRole('tenant')) {
                    return '/tenant';
                } else {
                    Auth::logout();
                    return '/';
                }
            });
    }
}