<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\EmpleadosActivosWidget;
use App\Filament\Widgets\PermisosEstadoStats;
use App\Filament\Widgets\PermisosPorUnidadWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';
    public function getWidgets(): array
    {
        return [
            EmpleadosActivosWidget::class,
            PermisosEstadoStats::class,
            PermisosPorUnidadWidget::class
        ];
    }
}
