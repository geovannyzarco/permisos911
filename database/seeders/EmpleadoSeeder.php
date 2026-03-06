<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {

        $path = database_path('seeders/datos/empleados.csv');

        if (!file_exists($path)) {
            $this->command->error("No existe el archivo: ".$path);
            return;
        }

        $file = fopen($path, 'r');

        // leer encabezado
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {

            $data = array_combine($header, $row);

            // convertir vacíos a null
            foreach ($data as $k => $v) {
                if ($v === '') {
                    $data[$k] = null;
                }
            }

            DB::table('empleados')->insert([
                'id' => $data['id'],
                'estado_civil_id' => $data['estado_civil_id'],
                'departamento_id' => $data['departamento_id'],
                'municipio_id' => $data['municipio_id'],
                'distrito_id' => $data['distrito_id'],
                'oni' => $data['oni'],
                'nombre' => trim($data['nombre']),
                'foto' => $data['foto'],
                'firma' => $data['firma'],
                'fecha_ingreso' => $this->parseDate($data['fecha_ingreso']),
                'fecha_nacimiento' => $this->parseDate($data['fecha_nacimiento']),
                'codigo_huella' => $data['codigo_huella'],
                'nombre_conyuge' => $data['nombre_conyuge'],
                'numero_hijos' => $data['numero_hijos'],
                'email' => $data['email'],
                'telefono' => $data['telefono'],
                'direccion' => $data['direccion'],
                'genero' => $data['genero'],
                'grupo_id' => $data['grupo_id'],
                'categoria_id' => $data['categoria_id'],
                'horario_id' => $data['horario_id'],
                'unidad_id' => $data['unidad_id'],
                'nivel_id' => $data['nivel_id'],
                'estado_id' => $data['estado_id'],
                'nota' => $data['nota'],
                'created_at' => $this->parseDateTime($data['created_at']),
                'updated_at' => $this->parseDateTime($data['updated_at']),
            ]);
        }

        fclose($file);
    }

    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDateTime($value)
    {
        if (!$value) {
            return now();
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return now();
        }
    }
}
