<?php

namespace App\Filament\Resources\Estados\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EstadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('entidad_id')
                    ->label('Entidad')
                    ->options(function () {
                        return \App\Models\Entidad::query()->pluck('nombre', 'id');
                    })
                    ->searchable(),
            ]);
    }
}
