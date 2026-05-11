<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Limpiar espacios en ONI y Email (Tabla Users)
        // Usamos SQL puro para asegurar compatibilidad con SQL Server (LTRIM/RTRIM)
        DB::table('users')->update([
            'oni' => DB::raw("LTRIM(RTRIM(oni))"),
            'email' => DB::raw("LTRIM(RTRIM(email))"),
        ]);

        // 2. Limpiar espacios en ONI (Tabla Empleados)
        if (Schema::hasTable('empleados')) {
            DB::table('empleados')->update([
                'oni' => DB::raw("LTRIM(RTRIM(oni))"),
            ]);
        }

        // 3. Asegurar existencia del rol panel_user
        // Nota: El guard_name suele ser 'web' por defecto
        $panelRole = Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);

        // 4. Asignar rol panel_user a usuarios existentes que no tengan acceso al panel
        // Esto evita que usuarios actuales se queden bloqueados por el cambio en canAccessPanel
        User::all()->each(function ($user) {
            // Si el usuario ya existe y no tiene roles de acceso, le damos panel_user
            if (!$user->hasRole(['super_admin', 'panel_user'])) {
                $user->assignRole('panel_user');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir la limpieza de espacios, 
        // pero podríamos quitar el rol si fuera crítico.
    }
};
