<?php

namespace App\Livewire;

use App\Models\Horario;
use App\Models\Marcacion;
use Dom\Text;
use Filament\Actions\BulkActionGroup;
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
use Filament\Tables\Filters\SelectFilter;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;

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
                ->join('empleados as e', 'e.codigo_huella', '=', 'marcaciones.codigo')
                ->join('horarios as h', 'h.id', '=', 'e.horario_id')
                ->selectRaw('
                    MIN(marcaciones.id) as id,
                    e.oni,
                    e.nombre as nombre_empleado,
                    h.nombre as nombre_horario,
                    h.horas_jornada,
                    h.hora_entrada as hora_entrada_esperada,
                    h.hora_salida as hora_salida_esperada,
                    DATE(marcaciones.marcacion) as fecha,
                    MIN(marcaciones.marcacion) as entrada_marcada,
                    MAX(marcaciones.marcacion) as salida_marcada,
                    TIMESTAMPDIFF(MINUTE,
                        MIN(marcaciones.marcacion),
                        MAX(marcaciones.marcacion)
                    ) as minutos_trabajados
                ')
                ->groupBy(
                    'e.oni',
                    'e.nombre',
                    'h.nombre',
                    'h.horas_jornada',
                    'h.hora_entrada',
                    'h.hora_salida',
                    DB::raw('DATE(marcaciones.marcacion)')
                )
        )
        ->columns([
            TextColumn::make('oni')
                ->label('Oni')
                ->searchable(query: function ($query, $search) {
                        $query->where('e.oni', 'like', "%{$search}%");
                    }),
            TextColumn::make('nombre_empleado')
                ->label('Nombre')
                ->searchable(query: function ($query, $search) {
                    $query->where('e.nombre', 'like', "%{$search}%");
                }),
            TextColumn::make('nombre_horario')
                ->label('Horario'),
            TextColumn::make('fecha')
                ->label('Fecha')
                ->date('d/m/Y')
                ->sortable(),
            TextColumn::make('horas_jornada')
                ->label('Horas Jornada')
                ->alignCenter(),
            TextColumn::make('hora_entrada_esperada')
                ->label('Entrada Esperada')
                ->time('H:i'),
            TextColumn::make('hora_salida_esperada')
                ->label('Salida Esperada')
                ->time('H:i'),
            TextColumn::make('entrada_marcada')
                ->label('Entrada Marcada')
                ->dateTime('d/m/Y H:i'),
            TextColumn::make('salida_marcada')
                ->label('Salida Marcada')
                ->dateTime('d/m/Y H:i'),
            TextColumn::make('minutos_trabajados')
                ->label('Horas Trabajadas')
                ->formatStateUsing(fn ($state) => round($state / 60, 2) . ' h')
                ->alignCenter(),
            TextColumn::make('horas_jornada')
                ->label('Horas Jornada')
                ->alignCenter(),
            TextColumn::make('diferencia')
                ->label('Diferencia')
                ->getStateUsing(function ($record) {
                    $horasTrabajadas = $record->minutos_trabajados / 60;
                    return round($horasTrabajadas - $record->horas_jornada, 2);
                })
                ->alignCenter(),

            TextColumn::make('estado')
                ->label('Estado')
                ->getStateUsing(function ($record) {

                    $horasTrabajadas = $record->minutos_trabajados / 60;
                    $diferencia = $horasTrabajadas - $record->horas_jornada;

                    if ($diferencia > 0) {
                        return 'Compensado';
                    }

                    if ($diferencia < 0) {
                        return 'Incompleto';
                    }

                    return 'Cumplido';
                })
                ->badge()
                ->color(function ($record) {

                    $horasTrabajadas = $record->minutos_trabajados / 60;
                    $diferencia = $horasTrabajadas - $record->horas_jornada;

                    if ($diferencia > 0) return 'info';
                    if ($diferencia < 0) return 'danger';

                    return 'success';
                }),
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
                SelectFilter::make('nombre_horario')
                ->label('Horario')
                ->options(function () {
                    return Horario::pluck('nombre','id');
                })
                ->query(function($query,$data){
                    if ($data['value']) {
                        $query->where('h.id', $data['value']);
                    }
                })
        ])
         ->toolbarActions([
            BulkActionGroup::make([
                ExportBulkAction::make()
            ])
         ]);

    }



     public function render(): View
    {
        return view('livewire.reporte-marcaciones');
    }
}
