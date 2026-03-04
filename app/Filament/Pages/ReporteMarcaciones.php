<?php

namespace App\Filament\Pages;

use App\Models\Marcacion;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;


class ReporteMarcaciones extends Page implements HasTable
{
    use InteractsWithTable;
    use HasPageShield;

    protected string $view = 'filament::filament.pages.reporte-marcaciones';

    protected static string | UnitEnum | null $navigationGroup = 'Reportes';
    protected static ?string $title = 'Reporte de Marcaciones';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;


}
