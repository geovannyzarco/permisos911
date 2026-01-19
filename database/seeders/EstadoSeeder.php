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
            ['nombre' => 'FUERA DE LIMITE','entidad_id' => 2],
            ['nombre' => 'INCOMPLETO (SIN COMPENSADOS)','entidad_id' => 2],
            ['nombre' => 'ABANDONO','entidad_id' => 1],
            ['nombre' => 'DETENIDO','entidad_id' => 1],
            ['nombre' => 'COMISION DE SERVICIO','entidad_id' => 1],
            ['nombre' => 'CON PERMISO SIN GOCE DE SUELDO','entidad_id' => 1],
            ['nombre' => 'SUSPENDIDO','entidad_id' => 1],
            ['nombre' => 'TRASLADADO','entidad_id' => 1],
        ];

        foreach ($estados as $estado) {
            Estado::create($estado);
        }
    }
}
