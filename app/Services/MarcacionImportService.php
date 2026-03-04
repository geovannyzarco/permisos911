<?php

namespace App\Services;

use App\Models\Marcacion;
use Carbon\Carbon;

class MarcacionImportService
{
    public function importFromTxt(string $path): array
    {
        $file = fopen($path, 'r');

        // Leer encabezado (TAB como delimitador)
        fgetcsv($file, 0, "\t");

        $importadas = 0;
        $duplicadas = 0;

        while (($row = fgetcsv($file, 0, "\t")) !== false) {

            // Validar que existan las columnas necesarias
            if (!isset($row[2]) || !isset($row[7])) {
                continue;
            }

            $codigo = (int) trim($row[2]);

            if (empty($codigo) || empty($row[7])) {
                continue;
            }

            // Normalizar espacios múltiples en la fecha
            $fechaRaw = preg_replace('/\s+/', ' ', trim($row[7]));

            try {
                $marcacion = Carbon::createFromFormat(
                    'Y/m/d H:i:s',
                    $fechaRaw
                );
            } catch (\Exception $e) {
                continue;
            }

            $exists = Marcacion::where('codigo', $codigo)
                ->where('marcacion', $marcacion)
                ->exists();

            if ($exists) {
                $duplicadas++;
                continue;
            }

            Marcacion::create([
                'codigo'    => $codigo,
                'marcacion' => $marcacion,
            ]);

            $importadas++;
        }

        fclose($file);

        return compact('importadas', 'duplicadas');
    }
}
