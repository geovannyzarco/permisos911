<?php

namespace App\Services;

use App\Models\Marcacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarcacionImportService
{
    // Tamaño del lote para inserciones masivas
    private const BATCH_SIZE = 500;

    public function importFromTxt(string $path): array
    {
        $file = fopen($path, 'r');
        if (!$file) {
            return ['importadas' => 0, 'duplicadas' => 0];
        }

        // Leer y descartar la cabecera (primera línea)
        fgets($file);

        $tempRecords = [];
        $seenInFile  = []; // Evitar duplicidad dentro del mismo archivo
        $now         = now()->toDateTimeString();

        while (($row = fgetcsv($file, 0, "\t")) !== false) {
            $colsCount = count($row);

            // Validar que al menos tengamos 4 columnas
            if ($colsCount < 4) {
                continue;
            }

            // El código siempre está en la columna 3 (índice 2)
            $codigo = (int) trim($row[2]);

            // La fecha y hora siempre están en la última columna de la línea
            $fechaRaw = trim($row[$colsCount - 1]);

            if (empty($codigo) || empty($fechaRaw)) {
                continue;
            }

            // Normalizar espacios múltiples en la fecha (ej. "2026/01/07  16:12:14" -> "2026/01/07 16:12:14")
            $fechaRaw = preg_replace('/\s+/', ' ', $fechaRaw);

            try {
                $marcacion = Carbon::createFromFormat('Y/m/d H:i:s', $fechaRaw);
            } catch (\Exception $e) {
                try {
                    $marcacion = Carbon::createFromFormat('Y/m/d H:i', $fechaRaw);
                } catch (\Exception $ex) {
                    continue; // Formato de fecha no válido, se omite
                }
            }

            $uniqueKey = $codigo . '_' . $marcacion->toDateTimeString();

            // Omitir duplicados dentro del mismo archivo
            if (isset($seenInFile[$uniqueKey])) {
                continue;
            }
            $seenInFile[$uniqueKey] = true;

            $tempRecords[] = [
                'codigo'     => $codigo,
                'marcacion'  => $marcacion->toDateTimeString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($file);

        if (empty($tempRecords)) {
            return ['importadas' => 0, 'duplicadas' => 0];
        }

        // Ordenar cronológicamente ascendente
        usort($tempRecords, fn($a, $b) => $a['marcacion'] <=> $b['marcacion']);

        // Insertar en lotes usando insertOrIgnore para que la BD descarte duplicados
        // aprovechando el índice único (codigo, marcacion) ya existente en la tabla.
        // Esto reemplaza el N+1 de SELECT+INSERT individual por una sola operación por lote.
        $chunks     = array_chunk($tempRecords, self::BATCH_SIZE);
        $importadas = 0;

        foreach ($chunks as $chunk) {
            $affected = DB::table('marcaciones')->insertOrIgnore($chunk);
            $importadas += $affected;
        }

        $totalEnArchivo = count($tempRecords);
        $duplicadas     = $totalEnArchivo - $importadas;

        return compact('importadas', 'duplicadas');
    }
}

