<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/municipios.json');
        
        if (!File::exists($path)) {
            $this->command->error("No se encontró municipios.json");
            return;
        }

        $municipios = json_decode(File::get($path), true);

        // Sort by ID to ensure order of auto-increment matches original IDs
        usort($municipios, function ($a, $b) {
            return (int)$a['id'] <=> (int)$b['id'];
        });

        DB::table('municipios')->delete();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlsrv') {
            DB::statement("DBCC CHECKIDENT (municipios, RESEED, 0)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE municipios AUTO_INCREMENT = 1");
        }

        foreach ($municipios as $m) {
            DB::table('municipios')->insert([
                'departamento_id' => $m['departamento_id'],
                'nombre' => trim($m['nombre']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
