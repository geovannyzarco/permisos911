<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departamentos')->delete();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlsrv') {
            DB::statement("DBCC CHECKIDENT (departamentos, RESEED, 0)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE departamentos AUTO_INCREMENT = 1");
        }

        $departamentos = [
            "Ahuachapán",
            "Cabañas",
            "Chalatenango",
            "Cuscatlán",
            "La Libertad",
            "La Paz",
            "La Unión",
            "Morazán",
            "San Miguel",
            "San Salvador",
            "San Vicente",
            "Santa Ana",
            "Sonsonate",
            "Usulután",
        ];

        foreach ($departamentos as $nombre) {
            Departamento::create(['nombre' => $nombre]);
        }
    }
}
