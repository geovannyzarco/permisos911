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

class MarcacionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([

            ])
            ->columns([
                TextColumn::make('codigo')->label('Código'),
                TextColumn::make('marcacion')
                ->label('Fecha y Hora')
                ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
               // EditAction::make(),
            ])
            ->toolbarActions([

            ]);
    }
}
