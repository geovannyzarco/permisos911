<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PermisosUsuarioPorTipoChart extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Mis permisos por tipo (Año actual)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    public static function getShieldPermissions(): array
    {
        return [
            'view',
        ];
    }

    protected function getData(): array
    {
        $empleado = Auth::user()->empleado;

        if (! $empleado) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $year = now()->year;

        $results = Permiso::query()
            ->selectRaw('tipo_permiso_id, COUNT(*) as total')
            ->where('empleado_id', $empleado->id)
            ->whereYear('desde', $year)
            ->where('id_estado_aprobacion_jefe_division', 3)
            ->groupBy('tipo_permiso_id')
            ->with('tipoPermiso')
            ->get();

        $labels = $results->map(fn($row) => $row->tipoPermiso->nombre)->toArray();
        $data = $results->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Permisos',
                    'data' => $data,
                    'backgroundColor' => [
                        '#36A2EB',
                        '#FF6384',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
