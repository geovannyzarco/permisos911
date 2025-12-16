<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TotalEmpleadosWidget;
use Filament\Panel;
use Filament\Pages\Dashboard as BaseDashboard;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;



class Dashboard extends BaseDashboard
{



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
