<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Empleado;
use App\Models\TipoPermiso;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/permisos.csv');

        if (!file_exists($path)) {
            $this->command->error("Archivo no encontrado: $path");
            return;
        }

        // Cargar IDs válidos como sets (memoria eficiente)
        $empleadosValidos = array_flip(
            Empleado::pluck('id')->toArray()
        );

        $tiposValidos = array_flip(
            TipoPermiso::pluck('id')->toArray()
        );

        $file = fopen($path, 'r');

        // CSV con ; y comillas
        $header = fgetcsv($file, 0, ';');
        $header = array_map(fn ($h) => trim($h, "\" \t\n\r\0\x0B"), $header);

        $insertados = 0;
        $saltados   = 0;
        $linea      = 1;

        DB::disableQueryLog();

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            $linea++;

            if (count($row) !== count($header)) {
                $this->command->warn("Línea $linea ignorada (columnas inconsistentes)");
                $saltados++;
                continue;
            }

            $data = array_combine($header, $row);

            $empleadoId = $data['empleado_id'];
            $tipoId     = $data['tipo_permiso_id'];

            // Validaciones
            if (!isset($empleadosValidos[$empleadoId])) {
                $this->command->warn("Empleado inexistente (línea $linea): $empleadoId");
                $saltados++;
                continue;
            }

            if (!isset($tiposValidos[$tipoId])) {
                $this->command->warn("Tipo permiso inexistente (línea $linea): $tipoId");
                $saltados++;
                continue;
            }

            DB::table('permisos')->insert([
               // 'id'                     => $data['id'],
                'fecha_creacion'         => $data['fecha_creacion'] ?: null,
                'desde'                  => $data['desde'] ?: null,
                'hasta'                  => $data['hasta'] ?: null,
                'motivo'                 => $data['motivo'],
                'adjunto'                => $data['adjunto'],
                'comentarios'            => $data['comentarios'],
                'empleado_id'            => $empleadoId,
                'tipo_permiso_id'        => $tipoId,
                'id_estado_vb'           => $data['id_estado_vb'] ?: null,
                'id_jefe_vb'             => $data['id_jefe_vb'] ?: null,
                'fecha_vb'               => $data['fecha_vb'] ?: null,
                'id_estado_aprobacion'   => $data['id_estado_aprobacion'] ?: null,
                'id_jefe_aprobacion'     => $data['id_jefe_aprobacion'] ?: null,
                'fecha_aprobacion'       => $data['fecha_aprobacion'] ?: null,
                'created_at'             => $data['created_at'] ?: now(),
                'updated_at'             => $data['updated_at'] ?: now(),
            ]);

            $insertados++;
        }

        fclose($file);

        $this->command->info("Seeder finalizado correctamente");
        $this->command->info("Insertados: $insertados");
        $this->command->info("Saltados: $saltados");
    }
}
