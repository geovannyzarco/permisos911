<?php

namespace App\Filament\Widgets;

use App\Models\Empleado;
use App\Models\Permiso;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PermisosStats extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Resumen de mis permisos';

    protected static ?int $sort = 3;

    // protected static bool $isDiscovered = false;
    protected function getStats(): array
    {

        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $empleado = Empleado::where('oni', $user->oni)->first();

        if (! $empleado) {
            return [];
        }

        $year = now()->year;
        // Ajusta los IDs de estado según tu BD
        $aprobados = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_jefe_division', 3)
            ->whereYear('desde', $year)
            ->count();

        $pendientes = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_jefe_division', 4)
            ->whereYear('desde', $year)
            ->count();

        $denegados = Permiso::where('empleado_id', $empleado->id)
            ->whereIn('id_estado_aprobacion_jefe_division', [5, 6, 7])
            ->whereYear('desde', $year)
            ->count();

        return [
            Stat::make('Aprobados', $aprobados)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Pendientes', $pendientes)
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Denegados', $denegados)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
