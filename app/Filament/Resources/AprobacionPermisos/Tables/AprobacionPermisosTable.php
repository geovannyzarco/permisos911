<?php

namespace App\Filament\Resources\AprobacionPermisos\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AprobacionPermisosTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->headerActions([

            ])
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),

                TextColumn::make('empleado.oni')
                    ->label('ONI'),
                ImageColumn::make('empleado.foto')
                    ->label('Foto'),
                TextColumn::make('empleado.nombre')
                    ->label('Nombre')
                    ->tooltip(function ($record) {
                        $empleado = $record->empleado;

                        return 'Unidad: '.optional($empleado->unidad)->nombre."\n".
                            'Grupo: '.optional($empleado->grupo)->nombre."\n".
                            'Horario: '.optional($empleado->horario)->nombre;
                    }),
                TextColumn::make('tipoPermiso.nombre')
                    ->label('Tipo'),
                TextColumn::make('desde')
                    ->label('Desde'),
                TextColumn::make('hasta')
                    ->label('Hasta'),
                TextColumn::make('duracion')
                    ->label('Duración'),
                TextColumn::make('estadoVB.nombre')
                    ->label('Visto Bueno'),
                TextColumn::make('jefeVB')
                    ->label('Jefe Visto Bueno'),
                TextColumn::make('estadoAprobado.nombre')
                    ->label('Aprobación'),
                TextColumn::make('jefeAprobacion')
                    ->label('Jefe Aprobación'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver Permiso'),
                EditAction::make()
                ->label('Aprobar/Rechazar')
                ->visible(function ($record){
                    $empleado = auth()->user()->empleado;

                    // no puede aprobar su propio permiso
                    if ($record->empleado_id == $empleado->id) {
                        return false;
                    }

                    // JEFE DE GRUPO
                    if ($empleado->nivel_id == 2) {
                        return $record->empleado->grupo_id == $empleado->grupo_id && $record->id_estado_vb === null;
                    }

                    // JEFE DE UNIDAD
                    if ($empleado->nivel_id == 3) {
                        return $record->empleado->unidad_id == $empleado->unidad_id
                        && $record->id_estado_vb !== null
                        && $record->id_estado_aprobacion === null;
                    }
                    return false; // otros empleados no pueden aprobar permisos
                }),
            ])
            ->toolbarActions([

            ]);
    }
}
