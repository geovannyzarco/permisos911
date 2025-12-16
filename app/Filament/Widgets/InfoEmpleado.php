<?php

namespace App\Filament\Widgets;

use App\Models\Empleado;
use Filament\Widgets\Widget;

class InfoEmpleado extends Widget
{
    protected static bool $isDiscovered = false;
    protected string $view = 'filament.widgets.info-empleado';
    public static function canView(): bool
    {
        return true;
    }

    public static function getTitle(): string
    {
        return 'Información del Empleado';
    }

    public static function getIcon(): ?string
    {
        return 'heroicon-o-user-circle';
    }

    public function getData(): array
    {
        $empleado = Empleado::where('oni', auth()->user()->username)->first();

        return [

            compact('empleado'),
        ];
    }
}
