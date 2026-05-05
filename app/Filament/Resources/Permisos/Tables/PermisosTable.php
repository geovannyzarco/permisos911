<?php

namespace App\Filament\Resources\Permisos\Tables;

use Filament\Actions\Action;
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

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('pdf')
                    ->label('Hoja de Permiso')
                    ->url(fn ($record) => route('permiso.pdf', ['id' => $record->id]))
                    ->openUrlInNewTab(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
