<?php

namespace Database\Seeders;

use App\Models\Empleado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        // Ruta del archivo CSV (carpeta datos dentro del proyecto)
        $path = database_path('seeders/datos//empleados.csv');

        if (!file_exists($path)) {
            $this->command->error("No se encontró el archivo: {$path}");
            return;
        }

        // Abrir y leer el CSV
        $csv = fopen($path, 'r');

        // Leer la primera línea (cabeceras)
        $headers = fgetcsv($csv);

        $count = 0;
         DB::statement('PRAGMA foreign_keys = OFF;');
        while (($data = fgetcsv($csv)) !== false) {
            $row = array_combine($headers, $data);

            Empleado::create([
                'oni' => $row['oni'],
                'nombre' => $row['nombre'],
                'foto' => '', // sin imagen por ahora
                'firma' => '', // sin firma por ahora
                'grupo_id' => $row['id_grupo'],
                'categoria_id' => $row['id_categoria'],
                'horario_id' => $row['id_horario'],
                'unidad_id' => $row['id_unidad'],
                'nivel_id' => 1, // valor por defecto o ajusta según tus necesidades
                'estado_id' => $row['id_estado'],
            ]);

            $count++;
        }
        DB::statement('PRAGMA foreign_keys = ON;');

        fclose($csv);

        $this->command->info("✅ Se insertaron {$count} empleados desde empleados.csv");
    }
}
