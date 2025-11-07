<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TipoPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/tipo_permisos.csv');

        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo tipo_permisos.csv en: $path");
            return;
        }

        $file = fopen($path, 'r');

        // Saltar la primera línea (encabezados)
        fgetcsv($file);

        $registros = 0;

        while (($data = fgetcsv($file)) !== false) {
            // CSV esperado: "id","nombre"
            $nombre = $data[1] ?? null;

            if ($nombre) {
                DB::table('tipo_permisos')->insert([
                    'nombre' => $nombre,
                ]);
                $registros++;
            }
        }

        fclose($file);

        $this->command->info("Se importaron {$registros} registros en la tabla tipo_permisos.");
    }
}
