<?php

namespace Database\Seeders;

use App\Models\Nivel;
use Illuminate\Database\Seeder;

class NivelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Nivel::create(['nivel' => 'Empleado']);
        Nivel::create(['nivel' => 'Jefe de Grupo']);
        Nivel::create(['nivel' => 'Jefe de Unidad']);
        Nivel::create(['nivel' => 'Jefe de Division']);
    }
}
