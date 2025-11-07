<?php

namespace App\Filament\Resources\Nivels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NivelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nivel')
                    ->required(),
            ]);
    }
}
