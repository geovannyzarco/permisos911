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

        $this->call(CategoriaSeeder::class);
        //$this->call(CompensadoSeeder::class);
        //$this->call(DivisionSeeder::class);
        //$this->call(EmpleadoSeeder::class);
        //$this->call(EntidadSeeder::class);
       // $this->call(EstadoSeeder::class);
        //$this->call(GrupoSeeder::class);
        //$this->call(HorarioSeeder::class);
        //$this->call(NivelSeeder::class);
        //$this->call(PermisoSeeder::class);
        //$this->call(TipoPermisoSeeder::class);
        //$this->call(UnidadSeeder::class);
        //$this->call(UserSeeder::class);
        //$this->call(UserUnidadSeeder::class);
    }
}
