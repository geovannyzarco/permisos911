<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PermisosPorTipoChart extends ChartWidget
{
    protected ?string $heading = 'Permisos Por Tipo (Año Actual)';
    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
            $añoActual = Carbon::now()->year;
            $data = Permiso::selectRaw('tipo_permiso_id, COUNT(*) as total')
                ->whereYear('desde', $añoActual)
                ->groupBy('tipo_permiso_id')
                ->with('tipoPermiso')
                ->get();
        return [
            'datasets' => [
                [
                    'label' => 'Permisos por Tipo',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                    ],
                ],
            ],
            'labels' => $data->map(fn ($permiso) => $permiso->tipoPermiso->nombre ?? 'Desconocido'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // 🔹 Hace que el gráfico sea horizontal
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Permisos aprobados por unidad (barras horizontales)',
                ],
            ],
            'scales' => [
                'x' => ['beginAtZero' => true],
            ],
        ];
    }
}
