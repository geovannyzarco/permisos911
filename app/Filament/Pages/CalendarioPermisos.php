<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;

class CalendarioPermisos extends Page
{
    use HasPageShield;

    protected string $view = 'filament::filament.pages.calendario-permisos';

    protected static string | UnitEnum | null $navigationGroup = 'Consultas';
    protected static ?string $title = 'Calendario de Permisos';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;
}
