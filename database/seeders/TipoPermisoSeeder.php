<?php

namespace Database\Seeders;

use App\Models\TipoPermiso;
use Illuminate\Database\Seeder;

class TipoPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoPermiso::factory()
            ->count(5)
            ->create();
    }
}
