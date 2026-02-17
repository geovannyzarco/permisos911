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

        // Saltar encabezados
        fgetcsv($file);

        $registros = 0;

        while (($data = fgetcsv($file)) !== false) {

            /*
             CSV esperado:
             0 => id
             1 => name
             2 => hora_entrada
             3 => hora_salida
             4 => horas_jornada
             5 => horas_personales
             6 => cruza_medianoche
            */

            $nombre            = $data[1] ?? null;
            $hora_entrada      = $data[2] ?? null;
            $hora_salida       = $data[3] ?? null;
            $horas_jornada     = $data[4] ?? 0;
            $horas_personales  = $data[5] ?? 0;
            $cruza_medianoche  = isset($data[6]) ? (bool) $data[6] : false;

            if ($nombre) {
                DB::table('horarios')->insert([

                    'nombre'            => $nombre,
                    'hora_entrada'      => $hora_entrada,
                    'hora_salida'       => $hora_salida,
                    'horas_jornada'     => (int) $horas_jornada,
                    'horas_personales'  => (int) $horas_personales,
                    'cruza_medianoche'  => $cruza_medianoche,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $registros++;
            }
        }

        fclose($file);

        $this->command->info("Se importaron {$registros} registros en la tabla horarios.");
    }
}
