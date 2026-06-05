<?php

namespace App\Filament\Resources\DelegarAprobacions\Tables;

use App\Models\Grupo;
use App\Models\Unidad;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class DelegarAprobacionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('jefe.nombre')
                    ->label('Jefe / Supervisor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo_delegacion')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => $state === 'grupo' ? 'Grupo' : 'Unidad')
                    ->badge()
                    ->color(fn ($state) => $state === 'grupo' ? 'info' : 'success')
                    ->sortable(),
                TextColumn::make('entidad_delegada_id')
                    ->label('Entidad Delegada')
                    ->formatStateUsing(function ($record) {
                        if ($record->tipo_delegacion === 'grupo') {
                            $grupo = Grupo::find($record->entidad_delegada_id);
                            return $grupo ? $grupo->nombre : 'N/A';
                        }
                        if ($record->tipo_delegacion === 'unidad') {
                            $unidad = Unidad::find($record->entidad_delegada_id);
                            return $unidad ? $unidad->nombre : 'N/A';
                        }
                        return 'N/A';
                    })
                    ->searchable(),
                TextColumn::make('fecha_desde')
                    ->label('Desde')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('fecha_hasta')
                    ->label('Hasta')
                    ->date('d/m/Y')
                    ->sortable(),
                ToggleColumn::make('activo')
                    ->label('Activo'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
