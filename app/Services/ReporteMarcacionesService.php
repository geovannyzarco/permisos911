<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReporteMarcacionesService
{
    public function generar(array $filtros = []): Collection
    {
        $query = DB::table('vw_reporte_marcaciones')
            ->orderBy('oni')
            ->orderBy('marcacion');

        // 🔹 Filtro por horario
        if (!empty($filtros['horario_id'])) {
            $query->where('horario_id', $filtros['horario_id']);
        }

        // 🔹 Filtro por grupo
        if (!empty($filtros['grupo'])) {
            $query->where('grupo', $filtros['grupo']);
        }

        // 🔹 Filtro por unidad
        if (!empty($filtros['unidad'])) {
            $query->where('unidad', $filtros['unidad']);
        }

        // 🔹 Filtro por rango de fechas
        if (!empty($filtros['desde'])) {
            $query->whereDate('marcacion', '>=', $filtros['desde']);
        }

        if (!empty($filtros['hasta'])) {
            $query->whereDate('marcacion', '<=', $filtros['hasta']);
        }

        $marcaciones = $query->get();

        return $this->procesar($marcaciones);
    }

    private function procesar(Collection $marcaciones): Collection
    {
        $registros = collect();

        // Agrupar por empleado
        $porEmpleado = $marcaciones->groupBy('empleado_id');

        foreach ($porEmpleado as $items) {

            $items = $items->values();
            $i = 0;

            while ($i < $items->count()) {

                $entrada = Carbon::parse($items[$i]->marcacion);
                $fila = $items[$i];

                $limite = $fila->cruza_medianoche
                    ? $entrada->copy()->addDay()->endOfDay()
                    : $entrada->copy()->endOfDay();

                $salida = $entrada;
                $j = $i + 1;

                while ($j < $items->count()) {
                    $marca = Carbon::parse($items[$j]->marcacion);

                    if ($marca->lte($limite)) {
                        $salida = $marca;
                        $j++;
                    } else {
                        break;
                    }
                }

                $minutos = $entrada->diffInMinutes($salida);
                $horasTrabajadas = round($minutos / 60, 2);
                $diferencia = round($horasTrabajadas - $fila->horas_jornada, 2);

                $estado = match (true) {
                    $diferencia > 0 => 'Horas extra',
                    $diferencia < 0 => 'Incumplimiento',
                    default => 'Cumplido',
                };

                $registros->push([
                    //'empleado_id' => $fila->empleado_id,
                    'oni' => $fila->oni,
                    'nombre_empleado' => $fila->nombre_empleado,
                    'grupo' => $fila->grupo,
                    'unidad' => $fila->unidad,
                    'horario' => $fila->horario,
                    'fecha_base' => $entrada->format('Y-m-d'),
                    'entrada_marcada' => $entrada,
                    'salida_marcada' => $salida,
                    'horas_trabajadas' => $horasTrabajadas,
                    'horas_jornada' => $fila->horas_jornada,
                    'diferencia' => $diferencia,
                    'estado' => $estado,
                ]);

                $i = $j;
            }
        }

        return $registros;
    }
}
