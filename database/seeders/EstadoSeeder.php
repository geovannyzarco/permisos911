<?php

namespace Database\Seeders;

use App\Models\Estado;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'ACTIVO','entidad_id' => 1],
            ['nombre' => 'INACTIVO','entidad_id' => 1],
            ['nombre' => 'APROBADO','entidad_id' => 2],
            ['nombre' => 'PENDIENTE','entidad_id' => 2],
            ['nombre' => 'ANULADO','entidad_id' => 2],
        ];

        foreach ($estados as $estado) {
            Estado::create($estado);
        }
    }
}
