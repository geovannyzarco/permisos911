<?php

namespace App\Livewire;

use App\Models\Horario;
use App\Models\Marcacion;
use App\Models\Unidad;
use App\Models\Grupo;
use Dom\Text;
use Dompdf\FrameDecorator\Image;
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
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;

class ReporteMarcaciones extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Marcacion::query()
                    ->fromSub(function ($query) {
                        $query->from('marcaciones')
                            ->join('empleados as e', 'e.codigo_huella', '=', 'marcaciones.codigo')
                            ->join('horarios as h', 'h.id', '=', 'e.horario_id')
                            ->leftJoin('unidades as u', 'u.id', '=', 'e.unidad_id')
                            ->leftJoin('grupos as g', 'g.id', '=', 'e.grupo_id')
                            ->selectRaw('
                                MIN(marcaciones.id) as id,
                                e.foto,
                                e.oni,
                                e.nombre as nombre_empleado,
                                e.horario_id,
                                e.unidad_id,
                                e.grupo_id,
                                u.nombre as nombre_unidad,
                                g.nombre as nombre_grupo,
                                h.nombre as nombre_horario,
                                h.horas_jornada,
                                h.hora_entrada as hora_entrada_esperada,
                                h.hora_salida as hora_salida_esperada,
                                CAST(marcaciones.marcacion AS DATE) as fecha,
                                MIN(marcaciones.marcacion) as entrada_marcada,
                                CASE WHEN COUNT(marcaciones.id) > 1 THEN MAX(marcaciones.marcacion) ELSE NULL END as salida_marcada,
                                COUNT(marcaciones.id) as total_marcaciones,
                                DATEDIFF(
                                    MINUTE,
                                    MIN(marcaciones.marcacion),
                                    MAX(marcaciones.marcacion)
                                ) as minutos_trabajados
                            ')
                            ->groupBy(
                                'marcaciones.codigo',
                                'e.oni',
                                'e.foto',
                                'e.nombre',
                                'e.horario_id',
                                'e.unidad_id',
                                'e.grupo_id',
                                'u.nombre',
                                'g.nombre',
                                'h.nombre',
                                'h.horas_jornada',
                                'h.hora_entrada',
                                'h.hora_salida',
                                DB::raw('CAST(marcaciones.marcacion AS DATE)')
                            );
                    }, 'marcaciones')
            )
            ->defaultSort('fecha', 'desc')
            ->columns([
                TextColumn::make('oni')
                    ->label('Oni')
                    ->searchable(),
                ImageColumn::make('foto')
                    ->disk('local')
                    ->circular()
                    ->label('Foto'),
                TextColumn::make('nombre_empleado')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('nombre_unidad')
                    ->label('Unidad'),
                TextColumn::make('nombre_grupo')
                    ->label('Grupo'),
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
                    ->formatStateUsing(fn($state) => round($state / 60, 2) . ' h')
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
                        if ($record->total_marcaciones == 1) {
                            return 'Marcación Incompleta';
                        }

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
                        if ($record->total_marcaciones == 1) {
                            return 'danger';
                        }

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
                            $query->where('fecha', '>=', $data['fecha_inicio']);
                        }
                        if ($data['fecha_fin']) {
                            $query->where('fecha', '<=', $data['fecha_fin']);
                        }
                    }),
                SelectFilter::make('nombre_horario')
                    ->label('Horario')
                    ->options(function () {
                        return Horario::pluck('nombre', 'id');
                    })
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->where('horario_id', $data['value']);
                        }
                    }),
                Filter::make('filtro_unidad_grupo')
                    ->form([
                        Select::make('unidad_id')
                            ->label('Unidad')
                            ->options(fn() => Unidad::pluck('nombre', 'id'))
                            ->live(), // Hace que el formulario reaccione al cambio de valor
                        Select::make('grupo_id')
                            ->label('Grupo')
                            ->options(function ($get) {
                                $unidadId = $get('unidad_id');
                                if ($unidadId) {
                                    return Grupo::where('unidad_id', $unidadId)->pluck('nombre', 'id');
                                }
                                return Grupo::pluck('nombre', 'id');
                            }),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['unidad_id'])) {
                            $query->where('unidad_id', $data['unidad_id']);
                        }
                        if (!empty($data['grupo_id'])) {
                            $query->where('grupo_id', $data['grupo_id']);
                        }
                    }),

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
