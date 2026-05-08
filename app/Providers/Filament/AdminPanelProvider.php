<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\LatestOrdersWidget;
use App\Filament\Admin\Widgets\RevenueChart;
use App\Filament\Admin\Widgets\StoreStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('NOVA Control')
            ->font('Noto Sans Arabic')
            ->darkMode(false)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => [
                    50 => '#FAF9F7',
                    100 => '#F0EDE8',
                    200 => '#D4AF7A',
                    300 => '#C5A06B',
                    400 => '#B8965A',
                    500 => '#8D6F4A',
                    600 => '#4A3860',
                    700 => '#3D2F4E',
                    800 => '#2D2438',
                    900 => '#241C2D',
                    950 => '#17111E',
                ],
                'gray' => [
                    50 => '#FAF9F7',
                    100 => '#F0EDE8',
                    200 => '#E2DDD5',
                    300 => '#D0C8BE',
                    400 => '#AFA5B7',
                    500 => '#857A90',
                    600 => '#756D7E',
                    700 => '#5F536E',
                    800 => '#4A3860',
                    900 => '#2D2438',
                    950 => '#17111E',
                ],
                'success' => [
                    50 => '#FAF9F7',
                    100 => '#F0EDE8',
                    200 => '#D4AF7A',
                    300 => '#C5A06B',
                    400 => '#B8965A',
                    500 => '#8D6F4A',
                    600 => '#4A3860',
                    700 => '#3D2F4E',
                    800 => '#2D2438',
                    900 => '#241C2D',
                    950 => '#17111E',
                ],
                'warning' => [
                    50 => '#FAF9F7',
                    100 => '#F4E8D6',
                    200 => '#D4AF7A',
                    300 => '#C9A06A',
                    400 => '#B8965A',
                    500 => '#9A7A49',
                    600 => '#7C5F37',
                    700 => '#604827',
                    800 => '#46331E',
                    900 => '#332415',
                    950 => '#1F150B',
                ],
                'danger' => [
                    50 => '#FDF4F2',
                    100 => '#FBEDE7',
                    200 => '#F1C7BC',
                    300 => '#E59A89',
                    400 => '#CE6F5D',
                    500 => '#B64B3B',
                    600 => '#8E3428',
                    700 => '#74291F',
                    800 => '#5B2119',
                    900 => '#471A14',
                    950 => '#2B0E0A',
                ],
                'info' => [
                    50 => '#FAF9F7',
                    100 => '#F0EDE8',
                    200 => '#D4AF7A',
                    300 => '#C5A06B',
                    400 => '#B8965A',
                    500 => '#8D6F4A',
                    600 => '#4A3860',
                    700 => '#3D2F4E',
                    800 => '#2D2438',
                    900 => '#241C2D',
                    950 => '#17111E',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountWidget::class,
                StoreStatsOverview::class,
                RevenueChart::class,
                LatestOrdersWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
