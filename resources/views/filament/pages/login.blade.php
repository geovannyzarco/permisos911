<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::button type="submit" class="w-full mt-4">
            Iniciar sesión
        </x-filament-panels::button>
    </x-filament-panels::form>
</x-filament-panels::page.simple>
