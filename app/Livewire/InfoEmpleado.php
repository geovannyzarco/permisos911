<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Empleado;
use App\Models\Permiso;

class InfoEmpleado extends Component
{
    public $empleado;
    public $horasDisponibles;

    public function mount()
    {
        // 1. Obtener empleado logueado por ONI
        $this->empleado = Empleado::where('oni', auth()->user()->oni)->first();

        if (!$this->empleado) {
            return;
        }
        // 2. Obtener permisos personales del año actual
        $permisos = Permiso::where('empleado_id', $this->empleado->id)
            ->where('tipo_permiso_id', 1)
            ->whereYear('desde', now()->year)
            ->get();

        // 3. Calcular horas usadas
        $horasUsadas = $permisos->sum(function ($p){
            return $p->desde->floatDiffInHours($p->hasta);
        });

        // 4. Horas asignadas en el horario del empleado
        $horasAsignadas = $this->empleado->horario->horas_personales ?? 0;

        // 5. Calcular horas disponibles
        $this->horasDisponibles = $horasAsignadas - $horasUsadas;

    }

    public function render()
    {

        return view('livewire.info-empleado', [
            'empleado' => $this->empleado,
            'horasDisponibles' => $this->horasDisponibles,
        ]);
    }
}
