<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class PermisosEstadoStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $year = Carbon::now()->year;

        $aprobados = Permiso::where('id_estado_aprobacion_grupo', 3)
            ->whereYear('desde', $year)
            ->count();

        $pendientes = Permiso::where('id_estado_aprobacion_grupo', 4)
            ->whereYear('desde', $year)
            ->count();

        $rechazados = Permiso::where('id_estado_aprobacion_grupo', 5)
            ->whereYear('desde', $year)
            ->count();
        return [
             Stat::make('Aprobados', $aprobados)
                ->description("Permisos aprobados en $year")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Pendientes', $pendientes)
                ->description("Permisos pendientes en $year")
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Rechazados', $rechazados)
                ->description("Permisos rechazados en $year")
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
