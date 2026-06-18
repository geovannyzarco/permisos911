<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PermisosAnualesWidget extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Resumen Permisos Empleados (Año actual)';

    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $year = Carbon::now()->year;

        $query = Permiso::whereYear('fecha_creacion', $year);

        $pendientes = (clone $query)->where('id_estado_aprobacion_jefe_division', 4)->count();
        $aprobados = (clone $query)->where('id_estado_aprobacion_jefe_division', 3)->count();
        $rechazados = (clone $query)->where('id_estado_aprobacion_jefe_division', 5)->count();

        return [
            Stat::make('Pendientes', $pendientes)
                ->description("Permisos pendientes en $year")
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Aprobados', $aprobados)
                ->description("Permisos aprobados en $year")
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Rechazados', $rechazados)
                ->description("Permisos rechazados en $year")
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
