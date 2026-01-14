<?php

namespace Database\Seeders;

use App\Models\EstadoCivil;
use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoCivilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $estadosCiviles = [
            'Soltero/a',
            'Casado/a',
            'Divorciado/a',
            'Viudo/a',
            'Unión Libre',
        ];

        foreach ($estadosCiviles as $nombre) {
            EstadoCivil::create(['nombre' => $nombre]);
        }
    }
}
