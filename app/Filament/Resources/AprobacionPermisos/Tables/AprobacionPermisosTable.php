<?php

namespace App\Filament\Resources\AprobacionPermisos\Tables;

use App\Models\Estado;
use App\Services\PermisoService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

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
                TextColumn::make('jefeVB')->label('Jefe Visto Bueno'),
                TextColumn::make('estadoAprobado.nombre')->label('Aprobación'),
                TextColumn::make('jefeAprobacion')->label('Jefe Aprobación'),
            ])
            ->filters([
                //
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
                    ->visible(fn ($record) => auth()->user()->can('aprobarPermiso', $record))
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
            ]);
    }
}
