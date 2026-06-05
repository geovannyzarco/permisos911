<?php

namespace Database\Seeders;

use App\Models\Entidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('entidades')->delete();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlsrv') {
            DB::statement("DBCC CHECKIDENT (entidades, RESEED, 0)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE entidades AUTO_INCREMENT = 1");
        }

        Entidad::create(['nombre' => 'Empleados']);
        Entidad::create(['nombre' => 'Permisos']);
    }
}
