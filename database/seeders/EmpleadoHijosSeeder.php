<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpleadoHijosSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/empleado_hijos.csv');

        if (!file_exists($path)) {
            $this->command->error("Archivo no encontrado: $path");
            return;
        }

        $file = fopen($path, 'r');

        // leer encabezado
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {

            $data = array_combine($header, $row);

            DB::table('empleado_hijos')->insert([
                'empleado_id' => $data['empleado_id'],
                'nombre' => $data['nombre'],
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        fclose($file);
    }
}
