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

        // 1. Horas Asignadas
        $horasAsignadas = $empleado->horario->horas_personales ?? 0;

        // 2. Horas Usadas (tipo_permiso_id = 1, id_estado_aprobacion = 3, año actual)
        // Calculamos la diferencia en minutos y la pasamos a horas
        $minutosUsados = Permiso::where('empleado_id', $empleado->id)
            ->where('tipo_permiso_id', 1) // Horas Personales
            ->where('id_estado_aprobacion', 3) // Aprobado
            ->whereYear('desde', $year)
            ->get()
            ->sum(function ($permiso) {
                if (! $permiso->desde || ! $permiso->hasta) {
                    return 0;
                }
                $desde = Carbon::parse($permiso->desde);
                $hasta = Carbon::parse($permiso->hasta);

                return $desde->diffInMinutes($hasta);
            });

        $horasUsadas = round($minutosUsados / 60, 2);

        // 3. Horas Disponibles
        $horasDisponibles = $horasAsignadas - $horasUsadas;

        return [
            Stat::make('Horas Personales Asignadas', $horasAsignadas.' hrs')
                ->description('Total de horas anuales asignadas')
                ->icon('heroicon-o-calendar-days')
                ->color('info'),

            Stat::make('Horas Personales Usadas', $horasUsadas.' hrs')
                ->description('Consumo acumulado en '.$year)
                ->icon('heroicon-o-clock')
                ->color($horasUsadas > ($horasAsignadas * 0.8) ? 'danger' : 'warning'),

            Stat::make('Horas Personales Disponibles', $horasDisponibles.' hrs')
                ->description('Saldo restante para el año')
                ->icon('heroicon-o-academic-cap')
                ->color($horasDisponibles > 0 ? 'success' : 'danger'),
        ];
    }
}
