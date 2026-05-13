<x-filament-widgets::widget>
    <x-filament::section>
    <div class="p-4 bg-white rounded-xl shadow">
        <x-slot name="heading">
            Mi Información de Empleado
        </x-slot>

    @if($empleado)
        <p><strong>Nombre:</strong> {{ $empleado->nombre }}</p>
        <p><strong>Unidad:</strong> {{ $empleado->unidad->nombre ?? 'N/A' }}</p>
        <p><strong>Grupo:</strong> {{ $empleado->grupo->nombre ?? 'N/A' }}</p>
        <p><strong>Horario:</strong> {{ $empleado->horario->nombre ?? 'N/A' }}</p>
        <p><strong>Categoria:</strong> {{ $empleado->categoria->nombre ?? 'N/A' }}</p>
        <p><strong>Nivel:</strong> {{ $empleado->nivel->nivel ?? 'N/A' }}</p>
    @else
        <p class="text-danger-600">No se encontró información de empleado asociada a su usuario.</p>
    @endif

    </div>
    </x-filament::section>
</x-filament-widgets::widget>
