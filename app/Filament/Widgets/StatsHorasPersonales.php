<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsHorasPersonales extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Horas Personales';

    protected function getStats(): array
    {
        $user = auth()->user();
        $empleado = $user->empleado;

        if (! $empleado || ! $empleado->horario) {
            return [];
        }

        $year = Carbon::now()->year;

        // MODIFICACIÓN: Obtenemos el resumen centralizado desde PermisoService para incluir horas pendientes
        $permisoService = app(\App\Services\PermisoService::class);
        $resumen = $permisoService->obtenerResumenHorasPersonales($empleado);

        $horasAsignadas = $resumen['asignadas'];
        $horasUsadas = $resumen['usadas'];
        $horasPendientes = $resumen['pendientes'];
        $horasDisponibles = $resumen['disponibles'];

        return [
            Stat::make('Horas Personales Asignadas', $horasAsignadas . ' hrs')
                ->description('Total de horas anuales asignadas')
                ->icon('heroicon-o-calendar-days')
                ->color('info'),

            Stat::make('Horas Personales Usadas', $horasUsadas . ' hrs')
                ->description('Aprobadas en ' . $year)
                ->icon('heroicon-o-check-circle')
                ->color($horasUsadas > ($horasAsignadas * 0.8) ? 'danger' : 'primary'),

            Stat::make('Horas Pendientes', $horasPendientes . ' hrs')
                ->description('En trámite de aprobación')
                ->icon('heroicon-o-clock')
                ->color($horasPendientes > 0 ? 'warning' : 'gray'),

            Stat::make('Horas Personales Disponibles', $horasDisponibles . ' hrs')
                ->description('Saldo real para solicitar')
                ->icon('heroicon-o-academic-cap')
                ->color($horasDisponibles > 0 ? 'success' : 'danger'),
        ];
    }
}
