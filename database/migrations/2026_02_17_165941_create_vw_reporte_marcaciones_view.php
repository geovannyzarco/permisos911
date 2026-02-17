<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW vw_reporte_marcaciones AS
            SELECT
                e.id AS empleado_id,
                e.oni,
                e.nombre AS nombre_empleado,
                g.nombre AS grupo,
                u.nombre AS unidad,
                h.id AS horario_id,
                h.nombre AS horario,
                h.horas_jornada,
                h.cruza_medianoche,
                h.hora_entrada,
                h.hora_salida,
                m.marcacion

            FROM empleados e
            INNER JOIN marcaciones m
                ON e.codigo_huella = m.codigo
            INNER JOIN horarios h
                ON e.horario_id = h.id
            INNER JOIN grupos g
                ON e.grupo_id = g.id
            INNER JOIN unidades u
                ON e.unidad_id = u.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_reporte_marcaciones");
    }
};
