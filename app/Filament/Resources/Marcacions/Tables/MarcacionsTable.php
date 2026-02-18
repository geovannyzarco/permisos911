<?php

namespace App\Filament\Resources\Marcacions\Tables;

use App\Models\Marcacion;
use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Imports\MarcacionImporter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

class MarcacionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([

            ])
            ->columns([
                TextColumn::make('codigo')->label('Código')->sortable()->searchable(),
                TextColumn::make('marcacion')
                ->label('Fecha y Hora')
                ->dateTime('Y-m-d H:i:s')
                ->sortable(),
            ])
            ->filters([
                Filter::make('fecha')
                    ->label('Fecha')
                    ->form([
                        DatePicker::make('fecha_inicio')->label('Fecha de Inicio'),
                        DatePicker::make('fecha_fin')->label('Fecha de Fin'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['fecha_inicio']) {
                            $query->whereDate('marcacion', '>=', $data['fecha_inicio']);
                        }
                        if ($data['fecha_fin']) {
                            $query->whereDate('marcacion', '<=', $data['fecha_fin']);
                        }
                    }),
            ])
            ->recordActions([
               // EditAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
