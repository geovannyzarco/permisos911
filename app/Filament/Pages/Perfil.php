<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InfoEmpleado;
use App\Filament\Widgets\PermisosStats;
use Filament\Pages\Page;

use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;




class Perfil extends Page
{


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected string $view = 'filament.pages.perfil';


    protected function getWidgets(): array
    {
        return [
            InfoEmpleado::class,
            PermisosStats::class,
        ];
    }


}
