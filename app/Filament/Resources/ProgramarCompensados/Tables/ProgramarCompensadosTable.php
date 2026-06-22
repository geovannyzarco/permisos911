<?php

namespace App\Filament\Resources\ProgramarCompensados\Tables;

use App\Models\Empleado;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgramarCompensadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Configura las opciones de paginación de la tabla
            ->paginated([5, 10, 25, 50, 100, 'all'])
            
            // Define el listado de columnas a desplegar
            ->columns([
                // ID del permiso
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                // Fecha de creación del registro
                TextColumn::make('fecha_creacion')
                    ->label('CREACIÓN')
                    ->date('d/m/Y')
                    ->sortable(),

                // ONI del empleado telefonista
                TextColumn::make('empleado.oni')
                    ->label('ONI')
                    ->sortable()
                    ->searchable(),

                // Foto del empleado en formato circular
                ImageColumn::make('empleado.foto')
                    ->circular()
                    ->label('FOTO'),

                // Nombre completo del empleado
                TextColumn::make('empleado.nombre')
                    ->label('NOMBRE')
                    ->sortable()
                    ->searchable(),

                // Fecha y hora de inicio de la ausencia
                TextColumn::make('desde')
                    ->label('DESDE')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                // Fecha y hora de fin de la ausencia
                TextColumn::make('hasta')
                    ->label('HASTA')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                // Duración descriptiva calculada por el modelo (días, horas, minutos)
                TextColumn::make('duracion')
                    ->label('DURACIÓN')
                    ->sortable(),

                // Justificación o motivo de la ausencia
                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->limit(50)
                    ->sortable(),

                // Estado de aprobación final del permiso con formato de placa (badge)
                TextColumn::make('estadoAprobado.nombre')
                    ->label('ESTADO')
                    ->badge()
                    ->sortable(),
            ])
            
            // Acciones disponibles en cada fila del listado (Edición)
            ->actions([
                EditAction::make(),
            ]);
    }
}
