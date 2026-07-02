<?php

namespace App\Filament\Resources\GestionPermisos\Tables;

use App\Models\Empleado;
use App\Models\Grupo;
use App\Models\Unidad;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use Filament\Forms\Components\DatePicker;

class GestionPermisosTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->paginated([5, 10, 25, 50, 100, 'all'])

            /* =======================
             * COLUMNS
             * ======================= */
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fecha_creacion')
                    ->label('CREACIÓN')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('empleado.oni')
                    ->label('ONI')
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('empleado.foto')
                    ->circular()
                    ->label('FOTO'),

                TextColumn::make('empleado.nombre')
                    ->label('NOMBRE')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tipoPermiso.nombre')
                    ->label('TIPO DE PERMISO')
                    ->sortable(),

                TextColumn::make('desde')
                    ->label('DESDE')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('hasta')
                    ->label('HASTA')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('duracion')
                    ->label('DURACIÓN')
                    ->sortable(),

                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->limit(50)
                    ->sortable(),

                TextColumn::make('estadoVB.nombre')
                    ->label('VB')
                    ->badge()
                    ->color(fn($record) => $record->id_estado_aprobacion_jefe_division == 3 ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('fecha_vb')
                    ->label('FECHA VB')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('jefeVB.nombre')
                    ->label('JEFE VB')
                    ->sortable(),

                TextColumn::make('estadoAprobado.nombre')
                    ->label('APROBACIÓN JEFATURA')
                    ->badge()
                    ->color(fn($record) => $record->id_estado_aprobacion == 3 ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('fecha_aprobacion')
                    ->label('FECHA APROBACIÓN JEFATURA')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('jefeAprobacion.nombre')
                    ->label('JEFE APROBOCIÓN JEFATURA')
                    ->sortable(),

                TextColumn::make('estadoAprobacionJefeDivision.nombre')
                    ->label('APROBACIÓN DIVISIÓN')
                    ->badge()
                    ->color(fn($record) => $record->id_estado_aprobacion_jefe_division == 3 ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('fecha_aprobacion_jefe_division')
                    ->label('FECHA APROBACIÓN DIVISIÓN')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('jefeDivision.nombre')
                    ->label('JEFE APROBACIÓN DIVISIÓN')
                    ->sortable(),

                ToggleColumn::make('tramitado')
                    ->label('TRAMITADO'),

                TextColumn::make('adjunto')
                    ->label('ADJUNTO')
                    ->icon('heroicon-o-paper-clip')
                    ->formatStateUsing(fn($state) => filled($state) ? 'Descargar' : '')
                    ->url(
                        fn($record) => $record?->adjunto
                            ? route('descargar.archivo', $record->adjunto)
                            : null
                    )
                    ->openUrlInNewTab(),
            ])

            /* =======================
             * FILTERS
             * ======================= */
            ->filters([

                // Filtro por rango de fechas en el campo desde
                Filter::make('desde')
                    ->label('Fecha Desde')
                    ->form([
                        DatePicker::make('desde_inicio')->label('Desde (Inicio)'),
                        DatePicker::make('desde_fin')->label('Desde (Fin)'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['desde_inicio']) {
                            $query->whereDate('desde', '>=', $data['desde_inicio']);
                        }
                        if ($data['desde_fin']) {
                            $query->whereDate('desde', '<=', $data['desde_fin']);
                        }
                    }),

                SelectFilter::make('tipo_permiso_id')
                    ->label('Tipo de Permiso')
                    ->relationship('tipoPermiso', 'nombre'),

                SelectFilter::make('estado_vb')
                    ->label('Visto Bueno')
                    ->relationship(
                        name: 'estadoVB',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('estado_aprobacion')
                    ->label('Aprobación Jefatura')
                    ->relationship(
                        name: 'estadoAprobado',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('id_estado_aprobacion_jefe_division')
                    ->label('Estado Aprobación Jefe División')
                    ->relationship(
                        name: 'estadoAprobacionJefeDivision',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('tramitado')
                    ->label('Tramitado')
                    ->options([
                        1 => 'Sí',
                        0 => 'No',
                    ]),

                Filter::make('filtros_dependientes')
                    ->form([
                        Select::make('unidad_id')
                            ->label('Unidad')
                            ->options(Unidad::pluck('nombre', 'id'))
                            ->reactive(),

                        Select::make('grupo_id')
                            ->label('Grupo')
                            ->options(function (callable $get) {
                                $unidadId = $get('unidad_id');

                                return Grupo::query()
                                    ->when(
                                        $unidadId,
                                        fn($query) => $query->where('unidad_id', $unidadId)
                                    )
                                    ->pluck('nombre', 'id');
                            })
                            ->disabled(fn(callable $get) => ! $get('unidad_id')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['unidad_id'] ?? null, function ($query, $unidadId) {
                                $query->whereHas(
                                    'empleado',
                                    fn($q) => $q->where('unidad_id', $unidadId)
                                );
                            })
                            ->when($data['grupo_id'] ?? null, function ($query, $grupoId) {
                                $query->whereHas(
                                    'empleado',
                                    fn($q) => $q->where('grupo_id', $grupoId)
                                );
                            });
                    }),
            ])

            /* =======================
             * ROW ACTIONS
             * ======================= */
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('pdf')
                    ->label('Ver Hoja de Permiso')
                    ->url(fn($record) => route('permiso.pdf', ['id' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->id_estado_aprobacion_jefe_division == 3),
            ])

            /* =======================
             * BULK ACTIONS
             * ======================= */
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),

                    BulkAction::make('cambiar_estado_vb')
                        ->label('Cambiar Vo.Bo.')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Select::make('estado_vb')
                                ->label('Estado Vo.Bo.')
                                ->relationship(
                                    name: 'estadoVB',
                                    titleAttribute: 'nombre',
                                    modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                                )
                                ->required(),

                            Select::make('jefe_vb')
                                ->label('Jefe Vo.Bo.')
                                ->relationship('jefeVB', 'nombre')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->getSearchResultsUsing(function (string $search) {
                                    return Empleado::query()
                                        ->where('nombre', 'like', "%{$search}%")
                                        ->orWhere('oni', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn($e) => [
                                            $e->id => "{$e->oni} - {$e->nombre}",
                                        ]);
                                })
                                ->getOptionLabelUsing(function ($value) {
                                    $empleado = Empleado::find($value);

                                    return $empleado
                                        ? "{$empleado->oni} - {$empleado->nombre}"
                                        : '';
                                }),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'id_estado_vb' => $data['estado_vb'],
                                    'fecha_vb' => now(),
                                    'id_jefe_vb' => $data['jefe_vb'],
                                ]);
                            }
                        }),

                    BulkAction::make('cambiar_estado_aprobacion')
                        ->label('Cambiar Aprobación')
                        ->icon('heroicon-o-shield-check')
                        ->form([
                            Select::make('estado_aprobacion')
                                ->label('Estado de Aprobación')
                                ->relationship(
                                    name: 'estadoAprobado',
                                    titleAttribute: 'nombre',
                                    modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                                )
                                ->required(),

                            Select::make('jefe_aprobacion')
                                ->label('Jefe Aprobador')
                                ->relationship('jefeAprobacion', 'nombre')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->getSearchResultsUsing(function (string $search) {
                                    return Empleado::query()
                                        ->where('nombre', 'like', "%{$search}%")
                                        ->orWhere('oni', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn($e) => [
                                            $e->id => "{$e->oni} - {$e->nombre}",
                                        ]);
                                })
                                ->getOptionLabelUsing(function ($value) {
                                    $empleado = Empleado::find($value);

                                    return $empleado
                                        ? "{$empleado->oni} - {$empleado->nombre}"
                                        : '';
                                }),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'id_estado_aprobacion' => $data['estado_aprobacion'],
                                    'fecha_aprobacion' => now(),
                                    'id_jefe_aprobacion' => $data['jefe_aprobacion'],
                                ]);
                            }
                        }),
                ])->label('Acciones Masivas'),
            ]);
    }
}
