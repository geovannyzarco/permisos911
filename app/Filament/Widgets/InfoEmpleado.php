<?php

namespace App\Filament\Widgets;

use App\Models\Empleado;
use Filament\Widgets\Widget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class InfoEmpleado extends Widget
{

    use HasWidgetShield;
    //protected static bool $isDiscovered = false;
    protected string $view = 'filament.widgets.info-empleado';
    protected static ?int $sort = 1;


    public static function getTitle(): string
    {
        return 'Información del Empleado';
    }

    public static function getIcon(): ?string
    {
        return 'heroicon-o-user-circle';
    }


    public function getViewData(): array
    {

        $user = Auth::user();
        $empleado = $user?->empleado;

        return [
            'empleado' => $empleado,
            'user' => $user,
        ];
    }
}
