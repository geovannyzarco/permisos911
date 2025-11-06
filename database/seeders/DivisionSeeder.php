<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('divisiones')->insert([
            'nombre'    => 'Division de Emergencias 911',
            'ubicacion' => 'Final Autopista Norte, Col. El Refugio, Edificio No. 3, División de Tránsito Terrestre, San Salvador, El Salvador',
            'telefono'  => '2222-0000',
            'email'     => 'secretaria911@pnc.gob.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
