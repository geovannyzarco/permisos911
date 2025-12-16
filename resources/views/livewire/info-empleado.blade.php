<div class="p-4 bg-white rounded-xl shadow">
    <x-slot name="heading">
        Información del Empleado
    </x-slot>


    <p><strong>Nombre:</strong> {{ $empleado->nombre }}</p>
    <p><strong>Unidad:</strong> {{ $empleado->unidad->nombre ?? 'N/A' }}</p>
    <p><strong>Grupo:</strong> {{ $empleado->grupo->nombre ?? 'N/A' }}</p>
    <p><strong>Horario:</strong> {{ $empleado->horario->nombre ?? 'N/A' }}</p>
    <p><strong>Categoria:</strong> {{ $empleado->categoria->nombre ?? 'N/A' }}</p>
    <p><strong>Nivel:</strong> {{ $empleado->nivel->nivel ?? 'N/A' }}</p>
    <p><strong>Horas Personales Asignadas:</strong> {{ $empleado->horario->horas_personales ?? 'N/A' }}</p>
    <p><strong>Horas personales Disponibles</strong> {{ $horasDisponibles ?? 'N/A' }}</p>

</div>
