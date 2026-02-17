<?php

namespace App\Filament\Resources\Horarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\ToggleButtons;

class HorariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('hora_entrada')
                    ->label('Hora Entrada')
                    ->time('H:i'),
                TextColumn::make('hora_salida')
                    ->label('Hora Salida')
                    ->time('H:i'),
                IconColumn::make('cruza_medianoche')
                    ->boolean()
                    ->label('Cruza Medianoche'),
                TextColumn::make('horas_jornada')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('horas_personales')
                    ->numeric()
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
            ]);
    }
}
