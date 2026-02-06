<?php

namespace App\Services;

use App\Models\Marcacion;
use Carbon\Carbon;

class MarcacionImportService
{
    public function importFromTxt(string $path): array
    {
        $file = fopen($path, 'r');

        $header = fgetcsv($file, 0, ";");

        $importadas = 0;
        $duplicadas = 0;

        while (($row = fgetcsv($file, 0, ";")) !== false) {

            if (count($row) !== count($header)) {
                continue;
            }

            $data = array_combine($header, $row);

            if (empty($data['EnNo']) || empty($data['DateTime'])) {
                continue;
            }

            $codigo = (int) trim($data['EnNo']);

            $marcacion = Carbon::createFromFormat(
                'Y/m/d H:i:s',
                trim($data['DateTime'])
            );

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
