<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Permiso;
use App\Models\Empleado;

class InfoPermisosEmpleado extends Component
{
    public int $aprobados = 0;
    public int $pendientes = 0;
    public int $denegados = 0;

    public function mount()
    {
        $empleado = Empleado::where('oni', auth()->user()->oni)->first();


        if (!$empleado) {
            return;
        }
        $this->aprobados = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_grupo', 3)
            ->count();

        $this->pendientes = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_grupo', 4)
            ->count();

        $this->denegados = Permiso::where('empleado_id', $empleado->id)
            ->where('id_estado_aprobacion_grupo', 5)
            ->count();
    }

    public function render()
    {

        return view('livewire.info-permisos-empleado');
    }
}
