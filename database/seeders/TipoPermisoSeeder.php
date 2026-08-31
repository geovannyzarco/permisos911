<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $tipoPermisos = [
            'PERMISO PERSONAL',
            'POR TIEMPO COMPENSATORIO',
            'CUMPLEAÑOS',
            'LICENCIA DE 8 DIAS POR MATERNIDAD',
            'DELEGACIONES DEPORTIVAS, CULTURAL O CIENTIFICAS',
            'TRATAMIENTO DE ENFERMEDAD EN EL EXTRANJERO',
            'CONSULTA MEDICA',
            'ENFERMEDAD O DUELO',
            'ESTUDIOS/HORAS SOCIALES',
            'DILIGENCIAS JUDICIALES/EXTRAJUDICIALES',
            'FALTA DE MARCACION',
            'LICENCIA POR ENFERMEDAD SIN INCAPACIDAD',
            'MISION OFICIAL',
            'PATERNIDAD',
            'POR LACTANCIA',
            'POR IMPARTIR CLASES',
            'MATRIMONIO',
            'LICENCIA/PERMISO SIN GOCE DE SUELDO',
        ];

        DB::table('tipo_permisos')->delete();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlsrv') {
            DB::statement("DBCC CHECKIDENT (tipo_permisos, RESEED, 0)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE tipo_permisos AUTO_INCREMENT = 1");
        }

        foreach ($tipoPermisos as $nombre) {
            DB::table('tipo_permisos')->insert([
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
