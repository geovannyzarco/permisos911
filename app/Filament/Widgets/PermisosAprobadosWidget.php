<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PermisosAprobadosWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Permisos Aprobados';

    protected function getStats(): array
    {
        $añoActual = Carbon::now()->year;
        $permisosAprobados = Permiso::where('id_estado_aprobacion_grupo', 3)
            ->whereYear('desde', $añoActual)
            ->count();

        $permisosPendientes = Permiso::where('id_estado_aprobacion_grupo', 4)
            ->whereYear('desde', $añoActual)
            ->count();
        $permisosRechazados = Permiso::where('id_estado_aprobacion_grupo', 5)
            ->whereYear('desde', $añoActual)
            ->count();

        return [
            Stat::make('Aprobados', $permisosAprobados)
            ->description('Año '.$añoActual)
            ->descriptionIcon('heroicon-o-check-circle')
            ->color('success'),

            Stat::make('Pendientes', $permisosPendientes)
            ->description('Año '.$añoActual)
            ->descriptionIcon('heroicon-o-clock')
            ->color('warning'),

            Stat::make('Rechazados', $permisosRechazados)
            ->description('Año '.$añoActual)
            ->descriptionIcon('heroicon-o-x-circle')
            ->color('danger'),
        ];

    }
}
