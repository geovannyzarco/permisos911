<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Empleado;
use App\Models\User;

class UsuariosDesdeEmpleadosSeeder extends Seeder
{
    public function run(): void
    {
        // Empleado que NO se debe crear como usuario
        $excluir = 'ep00116';

        // Obtener todos los empleados excepto el excluido
        $empleados = Empleado::where('oni', '!=', $excluir)->get();

        foreach ($empleados as $empleado) {

            // Verificar si el usuario ya existe por ONI o email
            if (User::where('oni', $empleado->oni)->exists()) {
                continue;
            }

            if (User::where('email', $empleado->oni . '@se911.com')->exists()) {
                continue;
            }

            User::create([
                'name'     => $empleado->nombre,
                'oni'      => $empleado->oni,
                'email'    => $empleado->oni . '@se911.com',
                'password' => Hash::make('123456'),
            ]);
        }
    }
}
