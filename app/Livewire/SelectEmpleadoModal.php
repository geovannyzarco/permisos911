<?php

namespace App\Livewire;

use App\Models\Empleado;
use Livewire\Component;

class SelectEmpleadoModal extends Component
{
    public $search = '';
    public $selectedEmpleado = null;
    public $empleados = [];

    protected $listeners = ['openSelectEmpleadoModal' => 'openModal'];

    public function open()
    {
        $this->reset(['search', 'empleados']);
        $this->dispatchBrowserEvent('open-modal',['id'=>'empleadoModal']);
    }

    public function updatedSearch()
    {
        $this->empleados = Empleado::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('oni', 'like', '%' . $this->search . '%')
            ->take(10)
            ->get();
    }

    public function selectEmpleado($id)
    {
        $this->selectedEmpleado = Empleado::find($id);
        $this->emit('empleadoSelected', ['id' => $this->selectedEmpleado->id, 'nombre' => $this->selectedEmpleado->nombre]);
        $this->dispatchBrowserEvent('close-modal',['id'=>'empleadoModal']);
    }

    public function render()
    {
        return view('livewire.select-empleado-modal');
    }
}
