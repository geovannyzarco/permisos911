<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Permiso;
use Illuminate\Support\Facades\DB;

class PermisosPorTipoChart extends ChartWidget
{
    protected  ?string $heading = 'Permisos por Tipo (Año actual)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $year = now()->year;

        // Permisos del año actual agrupados por tipo_permiso
        $data = Permiso::select('tipo_permisos.nombre as tipo', DB::raw('COUNT(*) as total'))
            ->join('tipo_permisos', 'permisos.tipo_permiso_id', '=', 'tipo_permisos.id')
            ->whereYear('desde', $year)
            ->groupBy('tipo_permisos.nombre')
            ->with('tipoPermiso')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->tipo,
                    'value' => $item->total,
                ];
            });

        return [
            'labels' => $data->pluck('label')->toArray(),
            'datasets' => [
                [
                    'label' => 'Cantidad',
                    'data' => $data->pluck('value')->toArray(),
                    'backgroundColor' => [
                        '#1E88E5',
                        '#43A047',
                        '#FB8C00',
                        '#E53935',
                        '#8E24AA',
                        '#00ACC1',
                        '#6D4C41',
                        '#3949AB',
                    ],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Barras horizontales
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
