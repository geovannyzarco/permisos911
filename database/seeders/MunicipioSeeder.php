<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {


        $path = database_path('seeders/datos/municipios_sv.csv');
        $rows = array_map('str_getcsv', file($path));
        unset($rows[0]); // encabezado

        foreach ($rows as $row) {
            [$departamentoNombre, $municipioNombre] = $row;

            $departamento = Departamento::where('nombre', $departamentoNombre)->first();

            if ($departamento) {
                Municipio::create([
                    'departamento_id' => $departamento->id,
                    'nombre' => $municipioNombre,
                ]);
            }
        }
    }
}

