<?php

namespace App\Filament\Resources\Empleados\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Image;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;

class EmpleadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('foto')
                    ->label('Foto')

                    ->circular(),
                TextColumn::make('oni')
                    ->label('ONI')
                    ->searchable(),
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('grupo.nombre')
                    ->label('Grupo')
                    ->sortable(),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->sortable(),
                TextColumn::make('horario.nombre')
                    ->label('Horario')
                    ->sortable(),
                TextColumn::make('unidad.nombre')
                    ->label('Unidad')
                    ->sortable(),
                TextColumn::make('nivel.nivel')
                    ->label('Nivel')
                    ->sortable(),
                TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->sortable(),
                TextColumn::make('codigo_huella')
                    ->label('Código de Huella')
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('grupo')
                    ->label('Filtrar por Grupo')
                    ->relationship('grupo', 'nombre'),
                SelectFilter::make('categoria')
                    ->label('Filtrar por Categoría')
                    ->relationship('categoria', 'nombre'),
                SelectFilter::make('unidad')
                    ->label('Filtrar por Unidad')
                    ->relationship('unidad', 'nombre'),
                SelectFilter::make('estado')
                    ->label('Filtrar por Estado')
                    ->relationship('estado', 'nombre'),
                SelectFilter::make('nivel')
                    ->label('Filtrar por Nivel')
                    ->relationship('nivel', 'nivel'),
                SelectFilter::make('horario')
                    ->label('Filtrar por Horario')
                    ->relationship('horario', 'nombre'),
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
