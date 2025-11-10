<?php

namespace App\Filament\Resources\Permisos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermisosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('fecha_creacion')->label('Fecha de Creación')->date()->sortable(),
                TextColumn::make('tipoPermiso.nombre')->label('Tipo de Permiso')->sortable()->searchable(),
                TextColumn::make('empleado.oni')->label('ONI')->sortable()->searchable(),
                TextColumn::make('empleado.nombre')->label('Empleado')->sortable()->searchable(),
                TextColumn::make('desde')->label('Desde')->dateTime()->sortable(),
                TextColumn::make('hasta')->label('Hasta')->dateTime()->sortable(),
                TextColumn::make('motivo')->label('Motivo')->limit(50)->sortable(),
                TextColumn::make('estado.nombre')->label('Estado Aprobación Grupo')->sortable(),
                TextColumn::make('fecha_aprobacion')->label('Fecha Aprobación')->dateTime()->sortable(),
                TextColumn::make('estadoUnidad.nombre')->label('Aprobación Unidad')->sortable(),
                TextColumn::make('fecha_aprobacion_unidad')->label('Fecha Aprobación Unidad')->dateTime()->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
