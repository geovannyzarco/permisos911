<?php

namespace App\Filament\Resources\Empleados\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Schemas\Schema;

class EmpleadoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('oni')
                ->label('ONI'),
                TextEntry::make('nombre'),
                TextEntry::make('grupo.nombre')
                    ->label('Grupo'),

                TextEntry::make('categoria.nombre')
                    ->label('Categoría'),

                TextEntry::make('horario.nombre')
                    ->label('Horario'),

                TextEntry::make('unidad.nombre')
                    ->label('Unidad'),

                TextEntry::make('nivel.nivel')
                    ->label('Nivel'),
                TextEntry::make('estado.nombre')
                    ->label('Estado'),



                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime(),
            ]);
    }
}
