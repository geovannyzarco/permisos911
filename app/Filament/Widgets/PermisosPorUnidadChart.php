<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PermisosPorUnidadChart extends ChartWidget
{
    protected  ?string $heading = 'Permisos por Unidad (Año 2025)';
    protected  string $color = 'success'; // opcional
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $year = now()->year;
        $result = DB::select("
            SELECT
                dbo.unidades.nombre AS unidades,
                COUNT(dbo.permisos.id) AS permisos
            FROM dbo.permisos
            INNER JOIN dbo.empleados ON dbo.permisos.empleado_id = dbo.empleados.id
            INNER JOIN dbo.unidades ON dbo.empleados.unidad_id = dbo.unidades.id
            WHERE YEAR(dbo.permisos.desde) = ".$year."
            GROUP BY dbo.unidades.nombre
        ");

        $labels = [];
        $values = [];

        foreach ($result as $row) {
            $labels[] = $row->unidades;
            $values[] = $row->permisos;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Permisos',
                    'data' => $values,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
