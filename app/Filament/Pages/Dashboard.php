<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InfoEmpleado;
use App\Filament\Widgets\TotalEmpleadosWidget;

use Filament\Panel;
use Filament\Pages\Dashboard as BaseDashboard;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

use function Laravel\Prompts\info;

class Dashboard extends BaseDashboard
{

    use HasPageShield;

    public function panel (Panel $panel):Panel
    {
        return $panel
        ->pages([
            Dashboard::class,
        ]);
    }

    public function getWidgets():array
    {
        return [
            TotalEmpleadosWidget::class,

        ];
    }

}
