<?php

namespace App\Filament\Resources\Permisos\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PermisosTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('fecha_creacion')->label('Fecha de Creación')->date('d/m/Y')->sortable(),
                TextColumn::make('tipoPermiso.nombre')->label('Tipo de Permiso')->sortable()->searchable(),
                TextColumn::make('empleado.oni')->label('ONI')->sortable()->searchable(),
                TextColumn::make('empleado.nombre')->label('Empleado')->sortable()->searchable(),
                TextColumn::make('desde')->label('Desde')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('hasta')->label('Hasta')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('duracion')->label('Duración'),
                TextColumn::make('motivo')->label('Motivo')->limit(50)->sortable(),
                TextColumn::make('estadoVB.nombre')->label('VB')->sortable(),
                TextColumn::make('fecha_vb')->label('Fecha VB')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('estadoAprobado.nombre')->label('Aprobación')->sortable(),
                TextColumn::make('fecha_aprobacion')->label('Fecha Aprobación')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('id_oni_jefe_division')->label('ONI Jefe División')->sortable(),
                TextColumn::make('fecha_aprobacion_jefe_division')->label('Fecha Aprobación Jefe División')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('id_estado_aprobacion_jefe_division')->label('Estado Aprobación Jefe División')->sortable(),
                BooleanColumn::make('tramitado')->label('Tramitado'),

            ])
            ->filters([
                Filter::make('id_oni_jefe_division')
                    ->form([
                        TextInput::make('id_oni_jefe_division')->label('ONI Jefe División'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['id_oni_jefe_division'] ?? null, function ($query, $value) {
                            $query->where('id_oni_jefe_division', 'like', "%{$value}%");
                        });
                    }),
                SelectFilter::make('id_estado_vb')
                    ->label('Visto Bueno')
                    ->relationship(
                        name: 'estadoVB',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('id_estado_aprobacion')
                    ->label('Aprobación')
                    ->relationship(
                        name: 'estadoAprobado',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('id_estado_aprobacion_jefe_division')
                    ->label('Estado Aprobación Jefe División')
                    ->relationship(
                        name: 'estadoAprobacionJefeDivision',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('tramitado')
                    ->label('Tramitado')
                    ->options([
                        1 => 'Sí',
                        0 => 'No',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('pdf')
                    ->label('Hoja de Permiso')
                    ->url(fn ($record) => route('permiso.pdf', ['id' => $record->id]))
                    ->openUrlInNewTab(),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
