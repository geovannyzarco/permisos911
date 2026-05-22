<?php

namespace App\Filament\Resources\AprobacionPermisos\Tables;

use App\Models\Empleado;
use App\Models\Estado;
use App\Models\Grupo;
use App\Models\Unidad;
use App\Services\PermisoService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AprobacionPermisosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                //
            ])
            ->columns([
                TextColumn::make('id')->label('ID'),

                TextColumn::make('empleado.oni')->label('ONI'),

                ImageColumn::make('empleado.foto')->label('Foto'),

                TextColumn::make('empleado.nombre')
                    ->label('Nombre')
                    ->tooltip(function ($record) {
                        $empleado = $record->empleado;

                        return 'Unidad: ' . optional($empleado->unidad)->nombre . "\n" .
                               'Grupo: ' . optional($empleado->grupo)->nombre . "\n" .
                               'Horario: ' . optional($empleado->horario)->nombre;
                    }),

                TextColumn::make('tipoPermiso.nombre')->label('Tipo'),
                TextColumn::make('desde')->label('Desde'),
                TextColumn::make('hasta')->label('Hasta'),
                TextColumn::make('duracion')->label('Duración'),
                TextColumn::make('estadoVB.nombre')->label('Visto Bueno'),
                TextColumn::make('jefeVB.nombre')->label('Jefe Visto Bueno'),
                TextColumn::make('estadoAprobado.nombre')->label('Aprobación'),
                TextColumn::make('jefeAprobacion.nombre')->label('Jefe Aprobación'),
                TextColumn::make('id_oni_jefe_division')->label('ONI Jefe División')->sortable(),
                TextColumn::make('fecha_aprobacion_jefe_division')->label('Fecha Aprobación Jefe División')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('estadoAprobacionJefeDivision.nombre')->label('Estado Aprobación Jefe División')->sortable(),
                TextColumn::make('comentarios')->label('Comentarios')->limit(50)

            ])
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

                // Filtro por tipo de permiso
                SelectFilter::make('tipo_permiso_id')
                    ->label('Tipo de Permiso')
                    ->relationship('tipoPermiso', 'nombre'),

                // Filtro por visto bueno (id_estado_vb)
                SelectFilter::make('id_estado_vb')
                    ->label('Visto Bueno')
                    ->relationship(
                        name: 'estadoVB',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                    ),

                // Filtro por aprobación (id_estado_aprobacion)
                SelectFilter::make('id_estado_aprobacion')
                    ->label('Aprobación')
                    ->relationship(
                        name: 'estadoAprobado',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                    ),

                // Filtro por estado de aprobación jefe de división
                SelectFilter::make('id_estado_aprobacion_jefe_division')
                    ->label('Estado Aprobación Jefe División')
                    ->relationship(
                        name: 'estadoAprobacionJefeDivision',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                    ),

                // Filtros dependientes por Unidad y Grupo (habilitados para Jefe de División / Nivel 4)
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
                                    ->when($unidadId, fn ($query) => $query->where('unidad_id', $unidadId))
                                    ->pluck('nombre', 'id');
                            })
                            ->disabled(fn (callable $get) => !$get('unidad_id')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['unidad_id'] ?? null, function ($query, $unidadId) {
                                $query->whereHas('empleado', fn ($q) => $q->where('unidad_id', $unidadId));
                            })
                            ->when($data['grupo_id'] ?? null, function ($query, $grupoId) {
                                $query->whereHas('empleado', fn ($q) => $q->where('grupo_id', $grupoId));
                            });
                    })
                    ->visible(fn () => auth()->user()->empleado?->nivel_id == 4),
            ])
            ->bulkActions([
                // Acciones masivas para procesar múltiples registros a la vez
                BulkActionGroup::make([
                    // Acción masiva para que los jefes de grupo (nivel 2) aprueben/rechacen con visto bueno
                    BulkAction::make('cambiar_estado_vb')
                        ->label('Cambiar Visto Bueno')
                        ->icon('heroicon-o-check-circle')
                        ->visible(fn () => auth()->user()->empleado?->nivel_id == Empleado::NIVEL_JEFE_GRUPO)
                        ->form([
                            Select::make('id_estado_vb')
                                ->label('Estado Visto Bueno')
                                ->options(
                                    Estado::where('entidad_id', 2)
                                        ->whereIn('id', [3, 4, 5])
                                        ->pluck('nombre', 'id')
                                  )
                                ->required(),

                            // Campo de comentarios habilitado para la acción masiva
                            Textarea::make('comentarios')
                                ->label('Comentarios')
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->requiresConfirmation()
                        ->action(function ($records, array $data) {
                            // Actualización masiva de los permisos seleccionados
                            foreach ($records as $record) {
                                $record->update([
                                    'id_estado_vb' => $data['id_estado_vb'],
                                    'id_jefe_vb' => auth()->user()->empleado->id, // ID del jefe logueado
                                    'fecha_vb' => now(), // fecha actual de la actualización
                                    'comentarios' => $data['comentarios'] ?? $record->comentarios,
                                ]);
                            }
                        }),

                    // Acción masiva para que los jefes de unidad (nivel 3) aprueben/rechacen con aprobación final
                    BulkAction::make('cambiar_estado_aprobacion')
                        ->label('Cambiar Aprobación')
                        ->icon('heroicon-o-shield-check')
                        ->visible(fn () => auth()->user()->empleado?->nivel_id == Empleado::NIVEL_JEFE_UNIDAD)
                        ->form([
                            Select::make('id_estado_aprobacion')
                                ->label('Estado de Aprobación')
                                ->options(
                                    Estado::where('entidad_id', 2)
                                        ->whereIn('id', [3, 4, 5])
                                        ->pluck('nombre', 'id')
                                )
                                ->required(),

                            // Campo de comentarios habilitado para la acción masiva
                            Textarea::make('comentarios')
                                ->label('Comentarios')
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->requiresConfirmation()
                        ->action(function ($records, array $data, PermisoService $s) {
                            foreach ($records as $record) {
                                if (auth()->user()->can('aprobarFinal', $record)) {
                                    $s->aprobarFinal($record, (int) $data['id_estado_aprobacion'], auth()->user(), $data['comentarios'] ?? null);
                                }
                            }
                        }),

                    // Acción masiva para que los jefes de división (nivel 4) aprueben/rechacen
                    BulkAction::make('cambiar_estado_aprobacion_jefe_division')
                        ->label('Cambiar Aprobación Jefe División')
                        ->icon('heroicon-o-check-badge')
                        ->visible(fn () => auth()->user()->empleado?->nivel_id == 4)
                        ->form([
                            Select::make('id_estado_aprobacion_jefe_division')
                                ->label('Estado Aprobación Jefe División')
                                ->options(
                                    Estado::where('entidad_id', 2)
                                        ->whereIn('id', [3, 4, 5])
                                        ->pluck('nombre', 'id')
                                )
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                if (auth()->user()->can('aprobarJefeDivision', $record)) {
                                    $record->update([
                                        'id_estado_aprobacion_jefe_division' => (int) $data['id_estado_aprobacion_jefe_division'],
                                        'id_oni_jefe_division' => auth()->user()->empleado->oni,
                                        'fecha_aprobacion_jefe_division' => now(),
                                    ]);
                                }
                            }
                        }),
                ])->label('Acciones Masivas'),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver Permiso'),

                // Visto Bueno
                Action::make('VB')
                    ->label('Visto Bueno')
                    ->visible(fn ($record) => auth()->user()->can('aprobarVB', $record))
                    ->form([
                        Select::make('id_estado_vb')
                            ->label('Visto Bueno')
                            ->options(
                                Estado::where('entidad_id', 2)
                                    ->whereIn('id', [3, 4, 5])
                                    ->pluck('nombre', 'id')
                            )
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(fn ($record, $data, PermisoService $s) =>
                        $s->aprobarVB($record, $data['id_estado_vb'], auth()->user())
                    ),

                // Aprobar Permiso
                Action::make('Aprobar')
                    ->label('Aprobar Permiso')
                    ->icon('heroicon-o-check-badge')
                    // CORRECCIÓN: Se cambió 'aprobarPermiso' por 'aprobarFinal' para alinearlo con el Gate definido en AppServiceProvider y el método en AprobacionPermisoPolicy.
                    ->visible(fn ($record) => auth()->user()->can('aprobarFinal', $record))
                    ->form([
                        Select::make('id_estado_aprobacion')
                            ->label('Aprobación')
                            ->options(
                                Estado::where('entidad_id', 2)
                                    ->whereIn('id', [3, 4, 5])
                                    ->pluck('nombre', 'id')
                            )
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(fn ($record, $data, PermisoService $s) =>
                        $s->aprobarFinal($record, $data['id_estado_aprobacion'], auth()->user())
                    ),

                // Aprobar Jefe División
                Action::make('AprobarJefeDivision')
                    ->label('Aprobación División')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn ($record) => auth()->user()->can('aprobarJefeDivision', $record))
                    ->form([
                        Select::make('id_estado_aprobacion_jefe_division')
                            ->label('Aprobación Jefe División')
                            ->options(
                                Estado::where('entidad_id', 2)
                                    ->whereIn('id', [3, 4, 5])
                                    ->pluck('nombre', 'id')
                            )
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->action(fn ($record, $data) =>
                        $record->update([
                            'id_estado_aprobacion_jefe_division' => $data['id_estado_aprobacion_jefe_division'],
                            'id_oni_jefe_division' => auth()->user()->empleado->oni,
                            'fecha_aprobacion_jefe_division' => now(),
                        ])
                    ),
            ]);
    }
}
