<?php

namespace App\Filament\Resources\TipoPermisos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TipoPermisoForm
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
