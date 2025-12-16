<x-filament::section>
    <x-slot name="heading">
        Resumen de Permisos
    </x-slot>
    <p>
        <strong>Aprobados: </strong>  {{ $aprobados }}
    </p>

    <p>
        <strong>Pendientes: </strong>{{ $pendientes }}
    </p>

    <p>
        <strong>Denegados: </strong> {{ $denegados }}
    </p>

</x-filament::section>




