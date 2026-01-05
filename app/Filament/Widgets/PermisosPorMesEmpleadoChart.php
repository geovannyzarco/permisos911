<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\Permiso;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class PermisosPorMesChart extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Mis permisos solicitados por mes (Año actual)';

    protected static ?int $sort = 1;

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

        $permisos = Permiso::query()
            ->selectRaw('MONTH(desde) AS mes, COUNT(*) AS total')
            ->where('empleado_id', $empleado->id)
            ->whereYear('desde', now()->year)
            ->where('id_estado_aprobacion', 3)
            ->groupByRaw('MONTH(desde)')
            ->orderByRaw('MONTH(desde)')
            ->get();

        // Inicializar todos los meses en 0
        $data = array_fill(1, 12, 0);

        foreach ($permisos as $permiso) {
            $data[(int) $permiso->mes] = $permiso->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Permisos',
                    'data' => array_values($data),
                ],
            ],
            'labels' => [
                'Enero', 'Febrero', 'Marzo', 'Abril',
                'Mayo', 'Junio', 'Julio', 'Agosto',
                'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }


}
