<?php

namespace App\Filament\Resources\Unidads\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Division;
use Dom\Text;

class UnidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('division_id')
                    ->label('División')
                    ->options(Division::query()->pluck('nombre', 'id'))
                    ->searchable(),
                TextInput::make('limite_permisos')
                    ->label('Límite de Permisos')
                    ->numeric()
                    ->required(),
            ]);
    }
}
