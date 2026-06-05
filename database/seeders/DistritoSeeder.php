<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DistritoSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/datos/distritos.json');
        
        if (!File::exists($path)) {
            $this->command->error("No se encontró distritos.json");
            return;
        }

        $distritos = json_decode(File::get($path), true);

        // Sort by ID to ensure order of auto-increment matches original IDs
        usort($distritos, function ($a, $b) {
            return (int)$a['id'] <=> (int)$b['id'];
        });

        DB::table('distritos')->delete();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlsrv') {
            DB::statement("DBCC CHECKIDENT (distritos, RESEED, 0)");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE distritos AUTO_INCREMENT = 1");
        }

        foreach ($distritos as $d) {
            DB::table('distritos')->insert([
                'municipio_id' => $d['municipio_id'],
                'nombre' => trim($d['nombre']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
