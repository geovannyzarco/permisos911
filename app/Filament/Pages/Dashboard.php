<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PermisosAprobadosWidget;
use App\Filament\Widgets\PermisosPorTipoChart;
use App\Filament\Widgets\PermisosPorUnidadWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Panel;

class Dashboard extends BaseDashboard
{

public function panel(Panel $panel):Panel
{
    return $panel
    ->pages([
        Dashboard::class,
    ]);
}

public function getWidgets(): array
{


    return [

        //StatsOverviewWidget::class,
        PermisosPorTipoChart::class,
        PermisosAprobadosWidget::class,
        PermisosPorUnidadWidget::class,
    ];
}
}
