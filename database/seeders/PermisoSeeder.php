<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $path = database_path('seeders\datos\permisos.csv'); // Ruta del archivo CSV

        if (!file_exists($path)) {
            $this->command->error("No se encontró el archivo: $path");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file); // Leer encabezado

        while (($row = fgetcsv($file)) !== false) {
            // Mapear columnas del CSV a los campos de la tabla
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

            // Convertir fechas al formato adecuado (YYYY-MM-DD o YYYY-MM-DD HH:MM:SS)
            $fecha_creacion = $this->parseDate($fecha_creacion);
            $desde = $this->parseDate($desde);
            $hasta = $this->parseDate($hasta);

            DB::table('permisos')->insert([
                'fecha_creacion' => $fecha_creacion,
                'desde' => $desde,
                'hasta' => $hasta,
                'motivo' => $motivo,
                'adjunto' => $adjunto ?? '',
                'comentarios' => $comentarios ?? '',
                'empleado_id' => $id_empleado,
                'tipo_permiso_id' => $id_tipo_permiso,
                'id_estado_aprobacion_grupo' => $id_estado,
                'id_jefe_grupo' => 0, // Asignar por defecto (si aún no hay jefe)
                'fecha_aprobacion_grupo' => null,
                'id_aprobacion_unidad' => 0,
                'id_jefe_unidad' => 0,
                'fecha_aprobacion_unidad' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        fclose($file);
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Convierte fechas del formato "d/m/Y H:i:s" al formato MySQL.
     */
    private function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Detectar si viene con hora
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
