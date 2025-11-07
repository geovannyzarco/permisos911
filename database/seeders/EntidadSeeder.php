<?php

namespace Database\Seeders;

use App\Models\Entidad;
use Illuminate\Database\Seeder;

class EntidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entidad::create(['nombre' => 'Empleados']);
        Entidad::create(['nombre' => 'Permisos']);
    }
}
