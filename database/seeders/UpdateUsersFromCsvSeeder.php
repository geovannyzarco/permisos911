<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class UpdateUsersFromCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Iniciando limpieza de espacios (Trimming)...");

        // 1. Limpiar espacios en blanco (Trimming) en SQL Server
        DB::table('users')->update([
            'oni' => DB::raw("LTRIM(RTRIM(oni))"),
            'email' => DB::raw("LTRIM(RTRIM(email))"),
        ]);
        
        if (Schema::hasTable('empleados')) {
            DB::table('empleados')->update([
                'oni' => DB::raw("LTRIM(RTRIM(oni))")
            ]);
        }

        // 2. Cargar datos del CSV
        $csvPath = database_path('seeders/dui/personal911.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("Archivo CSV no encontrado en: $csvPath");
            return;
        }

        $file = fopen($csvPath, 'r');
        fgetcsv($file); // Saltar cabecera ONI,DUI

        $role = Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);
        $count = 0;
        $notFound = 0;

        $this->command->info("Procesando CSV y actualizando contraseñas...");

        while (($data = fgetcsv($file)) !== FALSE) {
            if (count($data) < 2) continue;

            $oni = trim($data[0]);
            $password = trim($data[1]);

            $user = User::where('oni', $oni)->first();
            
            if ($user) {
                // Actualizamos password y nos aseguramos que el ONI esté limpio en la BD
                $user->password = Hash::make($password);
                $user->oni = $oni; 
                $user->save();

                // Asignar rol panel_user si no es admin
                if (!$user->hasRole(['super_admin', 'panel_user'])) {
                    $user->assignRole($role);
                }
                $count++;
            } else {
                $notFound++;
            }
        }

        fclose($file);

        $this->command->info("========================================");
        $this->command->info("PROCESO COMPLETADO:");
        $this->command->info("- Usuarios actualizados: $count");
        if ($notFound > 0) {
            $this->command->warn("- ONIs del CSV no encontrados en BD: $notFound");
        }
        $this->command->info("========================================");
    }
}
