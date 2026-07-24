<?php

namespace App\Filament\Resources\Grupos\Tables;

use App\Models\Unidad;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class GruposTable
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
                TextColumn::make('unidad.nombre'),
                TextColumn::make('permisos_diarios')
                    ->label('Permisos Diarios')
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
                    // DeleteBulkAction::make(),
                    BulkAction::make('asignar_unidad')
                        ->label('Asignar Unidad')
                        ->icon('heroicon-o-building-office')
                        ->form([
                            Select::make('unidad_id')
                                ->label('Unidad')
                                ->options(Unidad::pluck('nombre', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'unidad_id' => $data['unidad_id'],
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
