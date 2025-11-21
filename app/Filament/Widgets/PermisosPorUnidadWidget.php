<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermisosPorUnidadWidget extends ChartWidget
{
    protected ?string $heading = 'Permisos aprobados por unidad (año actual)';

    protected function getData(): array
    {
  $anioActual = Carbon::now()->year;

        // Agrupamos por unidad, accediendo a través del empleado
        $data = Permiso::query()
            ->select('empleados.unidad_id', DB::raw('COUNT(permisos.id) as total'))
            ->join('empleados', 'permisos.empleado_id', '=', 'empleados.id')
            ->join('unidades', 'empleados.unidad_id', '=', 'unidades.id')
            ->where('permisos.id_estado_aprobacion_grupo', 3) // Aprobados
            ->whereYear('permisos.fecha_creacion', $anioActual)
            ->groupBy('empleados.unidad_id', 'unidades.nombre')
            ->orderByDesc('total')
            ->get([
                'unidades.nombre as unidad_nombre',
                DB::raw('COUNT(permisos.id) as total')
            ]);

        // Etiquetas = nombres de unidades
        $labels = $data->pluck('unidad_nombre');

        // Valores = totales de permisos aprobados
        $values = $data->pluck('total');

        return [
            'datasets' => [
                [
                    'label' => 'Permisos aprobados',
                    'data' => $values,
                    'backgroundColor' => [
                        '#3b82f6', // azul
                        '#10b981', // verde
                        '#f59e0b', // amarillo
                        '#ef4444', // rojo
                        '#8b5cf6', // violeta
                        '#ec4899', // rosa
                        '#14b8a6', // turquesa
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // gráfico de pastel
    }



}
