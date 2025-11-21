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

        $csv = fopen($path, 'r');
        $headers = fgetcsv($csv);

        $insertados = 0;
        $invalidos = [];

        DB::statement('ALTER TABLE empleados NOCHECK CONSTRAINT ALL;');

        while (($data = fgetcsv($csv)) !== false) {
            $row = array_combine($headers, $data);

            // Validaciones
            if (!in_array($row['id_grupo'], $gruposValidos)) {
                $invalidos[] = "Grupo inválido: {$row['id_grupo']} (empleado {$row['oni']})";
                continue;
            }

            if (!in_array($row['id_categoria'], $categoriasValidos)) {
                $invalidos[] = "Categoría inválida: {$row['id_categoria']} (empleado {$row['oni']})";
                continue;
            }

            if (!in_array($row['id_horario'], $horariosValidos)) {
                $invalidos[] = "Horario inválido: {$row['id_horario']} (empleado {$row['oni']})";
                continue;
            }

            if (!in_array($row['id_unidad'], $unidadesValidos)) {
                $invalidos[] = "Unidad inválida: {$row['id_unidad']} (empleado {$row['oni']})";
                continue;
            }

            if (!in_array($row['id_estado'], $estadosValidos)) {
                $invalidos[] = "Estado inválido: {$row['id_estado']} (empleado {$row['oni']})";
                continue;
            }

            // Insertar registro
            Empleado::create([
                'oni' => $row['oni'],
                'nombre' => $row['nombre'],
                'foto' => '',
                'firma' => '',
                'grupo_id' => $row['id_grupo'],
                'categoria_id' => $row['id_categoria'],
                'horario_id' => $row['id_horario'],
                'unidad_id' => $row['id_unidad'],
                'nivel_id' => 1,
                'estado_id' => $row['id_estado'],
            ]);

            $insertados++;
        }

        fclose($csv);

        // Ahora sí reactivar FK (porque ya no hay datos inválidos)
        DB::statement('ALTER TABLE empleados WITH CHECK CHECK CONSTRAINT ALL;');

        $this->command->info("Empleados insertados: $insertados");

        if (!empty($invalidos)) {
            $this->command->warn("Registros inválidos encontrados:");
            foreach ($invalidos as $msg) {
                $this->command->warn(" - $msg");
            }
        }
    }
}
