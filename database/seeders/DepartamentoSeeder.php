<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {


        $departamentos = [
                "Ahuachapán",
                "Cabañas",
                "Chalatenango",
                "Cuscatlán",
                "La Libertad",
                "La Paz",
                "La Unión",
                "Morazán",
                "San Miguel",
                "San Salvador",
                "San Vicente",
                "Santa Ana",
                "Sonsonate",
                "Usulután",
        ];

        foreach ($departamentos as $nombre) {
            Departamento::create(['nombre' => $nombre]);
        }
    }
}

