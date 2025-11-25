<?php

namespace Database\Seeders;

use App\Models\Empleado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/empleados.csv');

        if (!file_exists($path)) {
            $this->command->error("No se encontró el archivo: {$path}");
            return;
        }

        // 🔥 Cargar valores válidos
        $gruposValidos = DB::table('grupos')->pluck('id')->toArray();
        $categoriasValidos = DB::table('categorias')->pluck('id')->toArray();
        $horariosValidos = DB::table('horarios')->pluck('id')->toArray();
        $unidadesValidos = DB::table('unidades')->pluck('id')->toArray();
        $estadosValidos = DB::table('estados')->pluck('id')->toArray();

        // 🔥 Valores por defecto si no existen
        $DEFAULT_CATEGORIA = 29;
        $DEFAULT_GRUPO = 22;
        $DEFAULT_HORARIO = 6;
        $DEFAULT_UNIDAD = 19;

        $csv = fopen($path, 'r');
        $headers = fgetcsv($csv);

        $insertados = 0;
        $invalidos = [];

        DB::statement('ALTER TABLE empleados NOCHECK CONSTRAINT ALL;');

        while (($data = fgetcsv($csv)) !== false) {

            $row = array_combine($headers, $data);

            // 📌 Verificar y asignar valores por defecto si no existen
            $grupo = in_array($row['id_grupo'], $gruposValidos)
                        ? $row['id_grupo']
                        : $DEFAULT_GRUPO;

            $categoria = in_array($row['id_categoria'], $categoriasValidos)
                        ? $row['id_categoria']
                        : $DEFAULT_CATEGORIA;

            $horario = in_array($row['id_horario'], $horariosValidos)
                        ? $row['id_horario']
                        : $DEFAULT_HORARIO;

            $unidad = in_array($row['id_unidad'], $unidadesValidos)
                        ? $row['id_unidad']
                        : $DEFAULT_UNIDAD;

            // Estado sí es obligatorio, si no existe, se reporta pero se usa NULL
            if (!in_array($row['id_estado'], $estadosValidos)) {
                $invalidos[] = "Estado inválido '{$row['id_estado']}' para empleado {$row['oni']}.";
                $row['id_estado'] = null;
            }

            // Insertar registro
            Empleado::create([
                'oni' => $row['oni'],
                'nombre' => $row['nombre'],
                'foto' => '',
                'firma' => '',
                'grupo_id' => $grupo,
                'categoria_id' => $categoria,
                'horario_id' => $horario,
                'unidad_id' => $unidad,
                'nivel_id' => 1,
                'estado_id' => $row['id_estado'],
            ]);

            $insertados++;
        }

        fclose($csv);

        DB::statement('ALTER TABLE empleados WITH CHECK CHECK CONSTRAINT ALL;');

        $this->command->info("Empleados insertados: $insertados");

        if (!empty($invalidos)) {
            $this->command->warn("Advertencias durante la importación:");
            foreach ($invalidos as $msg) {
                $this->command->warn(" - $msg");
            }
        }
    }
}
