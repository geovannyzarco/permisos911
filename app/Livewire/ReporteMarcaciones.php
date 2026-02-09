<?php

namespace App\Livewire;

use App\Models\Marcacion;
use Dom\Text;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Forms\Components\DatePicker;

class ReporteMarcaciones extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table( Table $table): Table
    {
    return $table
        ->query(
            Marcacion::query()
                ->join('empleados as e', 'e.codigo_huella', 'marcaciones.codigo')
                ->selectRaw('
                    MIN(marcaciones.id) as id,
                    e.oni,
                    e.nombre,
                    DATE(marcaciones.marcacion) as fecha,
                    TIME(MIN(marcaciones.marcacion)) as hora_entrada,
                    TIME(MAX(marcaciones.marcacion)) as hora_salida
                ')
                ->groupBy('e.oni', 'e.nombre', DB::raw('DATE(marcaciones.marcacion)'))
        )
        ->columns([
            TextColumn::make('oni')
            ->label('Oni')
            ->searchable(),
            TextColumn::make('nombre')
            ->label('Nombre')
            ->searchable(),
            TextColumn::make('fecha')
            ->label('Fecha')
            ->date('d/m/Y')
            ->sortable(),
            TextColumn::make('hora_entrada')->label('Hora Entrada'),
            TextColumn::make('hora_salida')->label('Hora Salida'),
        ])
        ->filters([
            Filter::make('fecha')
                ->form([
                    DatePicker::make('fecha_inicio')->label('Fecha Inicio'),
                    DatePicker::make('fecha_fin')->label('Fecha Fin'),
                ])
                ->query(function ($query, $data) {
                    if ($data['fecha_inicio']) {
                        $query->whereDate('marcaciones.marcacion', '>=', $data['fecha_inicio']);
                    }
                    if ($data['fecha_fin']) {
                        $query->whereDate('marcaciones.marcacion', '<=', $data['fecha_fin']);
                    }
                }),
        ]);
    }



     public function render(): View
    {
        return view('livewire.reporte-marcaciones');
    }
}
