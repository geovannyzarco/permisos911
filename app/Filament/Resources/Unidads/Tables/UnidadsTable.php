<?php

namespace App\Filament\Resources\Unidads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Table;

class UnidadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('nombre')
                ->label('Unidades')
                    ->searchable(),
                TextColumn::make('division.nombre')
                    ->label('División')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('limite_permisos')
                    ->label('Límite de Permisos')
                    ->sortable(),
                ColorColumn::make('color')
                    ->label('Color'),
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
