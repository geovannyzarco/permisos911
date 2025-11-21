<?php

namespace App\Filament\Widgets;

use App\Models\Empleado;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmpleadosActivosWidget extends BaseWidget
{
    protected ?string $heading = 'Resumen de Empleados';

    protected function getStats(): array
    {
        $totalActivos = Empleado::where('estado_id',1)->count();
        $totalInactivos = Empleado::where('estado_id', 2)->count();
        $totalEmpleados = Empleado::count();

        return [
            Stat::make('Empleados activos', $totalActivos)
                ->description('Actualmente trabajando')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('Empleados inactivos', $totalInactivos)
                ->description('En baja o suspendidos')
                ->descriptionIcon('heroicon-o-user-minus')
                ->color('danger'),

            Stat::make('Total de empleados', $totalEmpleados)
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
        ];
    }
}
