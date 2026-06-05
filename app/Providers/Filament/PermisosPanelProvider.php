<?php

namespace App\Providers\Filament;

use App\Filament\Helper\CustomLogin;
use App\Filament\Widgets\PermisosPorMesChart;
use App\Filament\Widgets\PermisosUsuarioPorTipoChart;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Navigation\NavigationItem;

class PermisosPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->profile(isSimple: false)
            ->globalSearch(false)
            ->favicon(asset('image/favicon.ico'))
            ->brandLogo(asset('image/logo.png'))
            ->brandLogoHeight('4rem')
            ->brandName('Gestor de Permisos')
            ->id('permisos')
            ->path('')
            ->login(CustomLogin::class)
            /*->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('Perfil')
                    ->url(fn (): string => \Filament\Auth\Pages\EditProfile::getUrl()),
                'logout' => \Filament\Navigation\MenuItem::make()
                    ->label('Salir'),
            ])*/
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,

            ])
            ->navigationItems([
                NavigationItem::make('Manual de Usuario')
                    ->url('/doc/manual_usuario.html', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-book-open')
                    ->group('Documentación')
                    ->sort(90)
                    ->visible(fn() => auth()->check()),

                NavigationItem::make('Manual del Administrador')
                    ->url('/doc/manual_administrador.html', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-cog-6-tooth')
                    ->group('Documentación')
                    ->sort(91)
                    ->visible(fn() => auth()->user()?->hasRole(['super_admin', 'admin'])),

                NavigationItem::make('Manual de Instalación')
                    ->url('/doc/manual_instalacion.html', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->group('Documentación')
                    ->sort(92)
                    ->visible(fn() => auth()->user()?->hasRole(['super_admin'])),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
                PermisosPorMesChart::class,
                PermisosUsuarioPorTipoChart::class,

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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->renderHook(
                'panels::body.end',
                fn() => Blade::render("
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Esperamos un momento a que el DOM y el plugin carguen
                            setTimeout(() => {
                                const canvases = document.querySelectorAll('canvas');

                                canvases.forEach(canvas => {
                                    // 1. Corregir el desplazamiento táctil
                                    canvas.style.touchAction = 'none';

                                    // 2. Función para ajustar coordenadas
                                    const adjustCanvas = () => {
                                        const rect = canvas.getBoundingClientRect();
                                        const ratio = window.devicePixelRatio || 1;

                                        // Esto resetea el buffer interno para que coincida con el tamaño visual
                                        if (canvas.width !== canvas.offsetWidth * ratio) {
                                            canvas.width = canvas.offsetWidth * ratio;
                                            canvas.height = canvas.offsetHeight * ratio;
                                            canvas.getContext('2d').scale(ratio, ratio);
                                        }
                                    };

                                    // Ajustar al cargar y al rotar la pantalla
                                    adjustCanvas();
                                    window.addEventListener('resize', adjustCanvas);
                                });
                            }, 500);
                        });
                    </script>
                ")
            );
    }
}
