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

class CashierPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cashier')
            ->path(config('app.cashier_prefix', 'cashier'))
            ->login(false)
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
            ->font('Inter')
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            
            // 🔥 ONLY use discovery, remove explicit resources
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
            
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                \App\Http\Middleware\EnsurePanelAccess::class . ':cashier',
            ])
            
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\CheckCashierAccess::class,
            ])
            
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->homeUrl('/cashier');
    }
}