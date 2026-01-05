<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/permisos.csv');

        if (!file_exists($path)) {
            $this->command->error("No se encontró el archivo: $path");
            return;
        }

        // 🔥 Cargar todos los IDs válidos
        $empleadosValidos = DB::table('empleados')->pluck('id')->toArray();
        $tiposValidos = DB::table('tipo_permisos')->pluck('id')->toArray();

        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        $invalidos = []; // Para reportar errores
        $insertados = 0;

        while (($row = fgetcsv($file)) !== false) {

            [
                $id,
                $id_empleado,
                $id_tipo_permiso,
                $fecha_creacion,
                $desde,
                $hasta,
                $motivo,
                $adjunto,
                $comentarios,
                $id_estado
            ] = $row;

            // ❌ Validar empleado
            if (!in_array($id_empleado, $empleadosValidos)) {
                $invalidos[] = "Empleado inexistente: empleado_id=$id_empleado (permiso $id)";
                continue;
            }

            // ❌ Validar tipo permiso
            if (!in_array($id_tipo_permiso, $tiposValidos)) {
                $invalidos[] = "TipoPermiso inexistente: tipo_permiso_id=$id_tipo_permiso (permiso $id)";
                continue;
            }

            // ✔ Insertar solo si es válido
            DB::table('permisos')->insert([
                'fecha_creacion' => $this->parseDate($fecha_creacion),
                'desde' => $this->parseDate($desde),
                'hasta' => $this->parseDate($hasta),
                'motivo' => $motivo,
                'adjunto' => $adjunto ?? '',
                'comentarios' => $comentarios ?? '',
                'empleado_id' => $id_empleado,
                'tipo_permiso_id' => $id_tipo_permiso,
                'id_estado_vb' => null,
                'id_jefe_vb' => null,
                'fecha_vb' => null,
                'id_estado_aprobacion' => $id_estado,
                'id_jefe_aprobacion' => null,
                'fecha_aprobacion' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $insertados++;
        }

        fclose($file);

        // ✔ Reporte final
        $this->command->info("Permisos insertados: $insertados");

        if (!empty($invalidos)) {
            $this->command->warn("Registros inválidos encontrados:");
            foreach ($invalidos as $linea) {
                $this->command->warn(" - $linea");
            }
        }
    }

    private function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            if (str_contains($date, ':')) {
                return Carbon::createFromFormat('d/m/Y H:i:s', $date)->format('Y-m-d H:i:s');
            } else {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}

