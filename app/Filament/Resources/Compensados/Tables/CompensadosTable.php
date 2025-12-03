<?php

namespace App\Filament\Resources\Compensados\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CompensadosTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('permiso_id')
                    ->label('Permiso ID')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('desde')
                    ->label('Inicio Actividad')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('Fin Actividad')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('justificacion')
                    ->limit(100)
                    ->wrap(),
                TextColumn::make('adjunto')
                    ->label('Adjunto')
                    ->getStateUsing(function ($record): string {
                        $url = Storage::url($record->adjunto);
                        $url = basename($record->adjunto);
                        return "<a href='{$url}' target='_blank'>Ver Adjunto</a>";
                    })
                    ->html()
                    ->limit(100)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Fecha Actualización')
                    ->dateTime()
                    ->sortable(),

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
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Agregar compensado')
                    ->modalButton('Guardar'),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Editar compensado')
                    ->modalButton('Actualizar'),

                DeleteAction::make(),
            ]);
            ;
    }
}
