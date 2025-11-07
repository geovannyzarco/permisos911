<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/horarios.csv');

        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo horarios.csv en: $path");
            return;
        }

        $file = fopen($path, 'r');

        // Saltar la primera línea (encabezados)
        fgetcsv($file);

        $registros = 0;

        while (($data = fgetcsv($file)) !== false) {
            // CSV esperado: "id","nombre","horas_jornada","horas_personales"
            $nombre = $data[1] ?? null;
            $horas_jornada = $data[2] ?? null;
            $horas_personales = $data[3] ?? null;

            if ($nombre) {
                DB::table('horarios')->insert([
                    'nombre' => $nombre,
                    'horas_jornada' => $horas_jornada,
                    'horas_personales' => $horas_personales,
                ]);
                $registros++;
            }
        }

        fclose($file);

        $this->command->info("Se importaron {$registros} registros en la tabla horarios.");
    }
}

