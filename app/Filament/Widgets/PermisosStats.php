<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Empleado;
use App\Models\Permiso;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class PermisosStats extends StatsOverviewWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Resumen de mis permisos';
    protected static ?int $sort = 1;
   // protected static bool $isDiscovered = false;
    protected function getStats(): array
    {

        $user = auth()->user();

        if (!$user) {
            return [];
        }

        $empleado = Empleado::where('oni', $user->oni)->first();

        if (!$empleado) {
            return [];
        }

        // Ajusta los IDs de estado según tu BD
        $aprobados = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_grupo', 3)
            ->count();

        $pendientes = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_grupo', 4)
            ->count();

        $denegados = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_grupo', 5)
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
