<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EntidadSeeder::class,
            EstadoSeeder::class,
            NivelSeeder::class,
            TipoPermisoSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            DistritoSeeder::class,
            UserSeeder::class,
        ]);
    }
}
