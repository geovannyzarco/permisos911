<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/grupos.csv');

        if (!File::exists($path)) {
            $this->command->error("El archivo grupos.csv no se encontró en: $path");
            return;
        }

        $file = fopen($path, 'r');

        // Saltar la primera línea (encabezados)
        fgetcsv($file);

        while (($data = fgetcsv($file)) !== false) {
            // CSV: "id","name"
            $nombre = $data[1] ?? null;

            if ($nombre) {
                DB::table('grupos')->insert([
                    'nombre' => $nombre,
                ]);
            }
        }

        fclose($file);

        $this->command->info('Datos de grupos importados correctamente.');
    }
}
