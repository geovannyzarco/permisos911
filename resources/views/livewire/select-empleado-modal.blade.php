@php
    use App\Models\Empleado;
@endphp

<div class="space-y-4">
    <input
        wire:model.debounce.300ms="search"
        type="text"
        placeholder="Buscar empleado..."
        class="w-full rounded-md border-gray-300 shadow-sm"
    />

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left text-sm font-semibold text-gray-600">Nombre</th>
                <th class="px-3 py-2 text-left text-sm font-semibold text-gray-600">ONI</th>
            </tr>
        </thead>
        <tbody>
            @foreach(Empleado::where('nombre', 'like', '%'.($search ?? '').'%')->limit(10)->get() as $empleado)
                <tr class="cursor-pointer hover:bg-gray-100"
                    wire:click="$dispatch('empleado-seleccionado', { id: {{ $empleado->id }}, nombre: '{{ addslashes($empleado->nombre) }}' })">
                    <td class="px-3 py-2 text-sm text-gray-800">{{ $empleado->nombre }}</td>
                    <td class="px-3 py-2 text-sm text-gray-500">{{ $empleado->oni }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@script
<script>
    document.addEventListener('empleado-seleccionado', e => {
        const { id, nombre } = e.detail;

        // Cierra el modal nativo de Filament
        window.filament?.modals?.close();

        // Emite un evento a Livewire principal del formulario
        Livewire.dispatch('empleado-seleccionado', { id, nombre });
    });
</script>
@endscript
