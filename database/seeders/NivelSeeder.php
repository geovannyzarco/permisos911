<?php

namespace Database\Seeders;

use App\Models\Nivel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('niveles')->delete();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlsrv') {
            DB::statement("DBCC CHECKIDENT (niveles, RESEED, 0)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE niveles AUTO_INCREMENT = 1");
        }

        Nivel::create(['nivel' => 'Empleado']);
        Nivel::create(['nivel' => 'Jefe de Grupo']);
        Nivel::create(['nivel' => 'Jefe de Unidad']);
        Nivel::create(['nivel' => 'Jefe de Division']);
    }
}
