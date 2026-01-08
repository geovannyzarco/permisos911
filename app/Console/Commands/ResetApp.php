<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ResetApp extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:reset';

    /**
     * The console command description.
     */
    protected $description = 'Reset completo: migraciones, seeders, shield y asignación de rol admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 🔒 Protección básica
        if (app()->environment('production')) {
            $this->error('❌ Este comando no puede ejecutarse en producción.');
            return Command::FAILURE;
        }

        // ⚠️ Confirmación
        if (! $this->confirm('⚠️ Esto borrará TODA la base de datos. ¿Deseas continuar?')) {
            $this->warn('Operación cancelada.');
            return Command::FAILURE;
        }

        // 1️⃣ Migraciones
        $this->info('🔄 Ejecutando migrate:fresh...');
        $this->call('migrate:fresh');

        // 2️⃣ Seeders
        $this->info('🌱 Ejecutando db:seed...');
        $this->call('db:seed');

        // 3️⃣ Filament Shield
        $this->info('🛡️ Generando permisos con Filament Shield...');
        $this->call('shield:generate', [
            '--all' => true,
        ]);

        // 4️⃣ Crear / asegurar rol admin
        $this->info('👤 Creando / verificando rol admin...');
        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // 5️⃣ Asignar todos los permisos al rol admin
        $this->info('🔑 Asignando todos los permisos al rol admin...');
        $role->syncPermissions(Permission::all());

        // 6️⃣ Asignar rol admin a usuario específico
        $oni = 'EP00116'; // ← ajusta si es necesario

        $user = User::where('oni', $oni)->first();

        if (! $user) {
            $this->warn("⚠️ Usuario con ONI {$oni} no encontrado. No se asignó el rol.");
        } else {
            $user->assignRole('admin');
            $this->info("✅ Rol admin asignado al usuario {$oni}");
        }

        $this->info('🎉 Reset completo finalizado correctamente.');

        return Command::SUCCESS;
    }
}
