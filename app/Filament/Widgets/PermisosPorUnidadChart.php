<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Permiso;
use Illuminate\Support\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class PermisosPorUnidadChart extends ChartWidget
{

    use HasWidgetShield;
    protected static ?int $sort = 4;
    protected ?string $heading = 'Permisos por Unidad (Año actual)';

    protected function getData(): array
    {
        $year = now()->year;

        // ---- CONSULTA ELOQUENT ----
        $result = Permiso::query()
            ->selectRaw('unidades.nombre AS unidad, COUNT(permisos.id) AS total')
            ->join('empleados', 'permisos.empleado_id', '=', 'empleados.id')
            ->join('unidades', 'empleados.unidad_id', '=', 'unidades.id')
            ->whereYear('permisos.desde', $year)
            ->groupBy('unidades.nombre')
            ->get();

        $labels = $result->pluck('unidad')->toArray();
        $data   = $result->pluck('total')->toArray();

        // ---- COLORES FIJOS (uno por unidad) ----
        // Puedes agregar más si tienes más unidades
        $fixedColors = [
                        '#1E88E5',
                        '#43A047',
                        '#FB8C00',
                        '#E53935',
                        '#8E24AA',
                        '#00ACC1',
                        '#6D4C41',
                        '#3949AB',
        ];

        // Ajustar colores según la cantidad de unidades
        $colors = array_slice($fixedColors, 0, count($labels));

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Permisos',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#fff',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    // ---- OCULTAR LEYENDA DEBAJO ----
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false, // Oculta etiquetas de colores
                ],
            ],
        ];
    }


}
