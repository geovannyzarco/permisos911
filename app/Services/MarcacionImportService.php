<?php

namespace App\Services;

use App\Models\Marcacion;
use Carbon\Carbon;

class MarcacionImportService
{
    public function importFromTxt(string $path): array
    {
        $file = fopen($path, 'r');
        if (!$file) {
            return ['importadas' => 0, 'duplicadas' => 0];
        }

        // Leer y descartar la cabecera (primera línea)
        fgets($file);

        $tempRecords = [];
        $duplicadas = 0;
        $seenInFile = []; // Evitar duplicidad dentro del mismo archivo

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

            // Omitir si ya se procesó en este archivo
            if (isset($seenInFile[$uniqueKey])) {
                $duplicadas++;
                continue;
            }
            $seenInFile[$uniqueKey] = true;

            // Omitir si ya existe en la base de datos
            $exists = Marcacion::where('codigo', $codigo)
                ->where('marcacion', $marcacion)
                ->exists();

            if ($exists) {
                $duplicadas++;
                continue;
            }

            $tempRecords[] = [
                'codigo'    => $codigo,
                'marcacion' => $marcacion,
            ];
        }

        fclose($file);

        // Ordenar cronológicamente ascendente
        usort($tempRecords, function ($a, $b) {
            return $a['marcacion']->timestamp <=> $b['marcacion']->timestamp;
        });

        // Insertar registros en la base de datos
        $importadas = 0;
        foreach ($tempRecords as $record) {
            Marcacion::create([
                'codigo'    => $record['codigo'],
                'marcacion' => $record['marcacion'],
            ]);
            $importadas++;
        }

        return compact('importadas', 'duplicadas');
    }
}
