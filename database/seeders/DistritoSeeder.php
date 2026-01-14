<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Distrito;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;

class DistritoSeeder extends Seeder
{
    public function run(): void
    {

        $path = database_path('seeders/datos/distritos_sv.csv');
        $rows = array_map('str_getcsv', file($path));
        unset($rows[0]);

        foreach ($rows as $row) {
            [$municipioNombre, $distritoNombre] = $row;

            $municipio = Municipio::where('nombre', $municipioNombre)->first();

            if ($municipio) {
                Distrito::create([
                    'municipio_id' => $municipio->id,
                    'nombre' => $distritoNombre,
                ]);
            }
        }
    }
}

