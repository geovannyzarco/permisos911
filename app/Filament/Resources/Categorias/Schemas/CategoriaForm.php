<?php

namespace App\Filament\Resources\Categorias\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class CategoriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('grado')
                    ->options([
                        'ADMINISTRATIVO' => 'ADMINISTRATIVO',
                        'OPERATIVO' => 'OPERATIVO',
                    ])
                    ->native(false),
            ]);
    }
}
