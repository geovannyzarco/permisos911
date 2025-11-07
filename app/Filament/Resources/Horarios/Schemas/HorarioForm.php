<?php

namespace App\Filament\Resources\Horarios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HorarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('horas_jornada')

                    ->numeric(),
                TextInput::make('horas_personales')
                    ->required()
                    ->numeric(),
            ]);
    }
}
