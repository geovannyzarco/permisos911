<?php

namespace App\Filament\Resources\Entidads\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EntidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
            ]);
    }
}
