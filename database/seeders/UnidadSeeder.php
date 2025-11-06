<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/datos/unidades.csv');
        if (!File::exists($path)) {
            $this->command->error("El archivo unidades.csv no se encontró en la ruta: $path");
            return;
        }

        $this->command->info("Iniciando la importación de unidades desde $path");

        // Detectar el delimitador automáticamente (coma o punto y coma)
        $firstLine = File::lines($path)->first();
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

          // Abrir archivo
          if (($handle = fopen($path, 'r')) !== false) {
            // Leer la primera línea como encabezado
            $header = fgetcsv($handle, 1000, $delimiter);

            $count = 0;

            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                // Limpiar comillas y espacios
                $data = array_map(fn($v) => trim($v, "\"'"), $data);

                // El archivo tiene columnas: id, id_division, name, id_jefe
                $divisionId = $data[1] ?? null;
                $nombre = $data[2] ?? null;

                if($nombre && $divisionId){
                    DB::table('unidades')->insert([
                        'nombre' => $nombre,
                        'division_id' => $divisionId,
                    ]);
                    $count++;
                } else {
                    $this->command->warn("Registro omitido por datos incompletos: " . implode(',', $data));
                }
            }
            fclose($handle);
            $this->command->info("Se importaron $count unidades correctamente.");
        }else{
            $this->command->error("No se pudo abrir el archivo CSV.");
        }
    }
}
