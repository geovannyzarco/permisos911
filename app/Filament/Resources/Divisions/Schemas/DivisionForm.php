<?php

namespace App\Filament\Resources\Divisions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DivisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('ubicacion'),

                TextInput::make('telefono')
                    ->tel(),

                TextInput::make('email')
                    ->label('Email address')
                    ->email(),

            ]);
    }
}
