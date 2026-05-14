<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PermisosPorMesChart;
use App\Filament\Widgets\PermisosUsuarioPorTipoChart;
use App\Filament\Widgets\TotalEmpleadosWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasPageShield;

    public function getWidgets(): array
    {
        return [
            TotalEmpleadosWidget::class,
            PermisosPorMesChart::class,
            PermisosUsuarioPorTipoChart::class,
        ];
    }
}
