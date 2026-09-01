<?php

namespace App\Filament\Resources\ProgramarCompensados\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgramarCompensadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([5, 10, 25, 50, 100, 'all'])
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
                    ->disk('local')
                    ->circular()
                    ->label('FOTO'),

                TextColumn::make('empleado.nombre')
                    ->label('NOMBRE')
                    ->sortable()
                    ->searchable(),

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

                TextColumn::make('estadoAprobado.nombre')
                    ->label('ESTADO')
                    ->badge()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}
