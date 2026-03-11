<?php

namespace App\Filament\Resources\Empleados\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;

use Filament\Schemas\Components\Section;

class EmpleadoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('foto'),

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

                Section::make('Licencias y permisos')
                ->schema([

                    IconEntry::make('permiso_portacion_arma')
                        ->label('Portación de arma')
                        ->boolean(),

                    TextEntry::make('numero_permiso_arma')
                        ->label('Número permiso arma'),

                    IconEntry::make('licencia_conducir')
                        ->label('Licencia de conducir')
                        ->boolean(),

                    TextEntry::make('tipo_licencia')
                        ->label('Tipo licencia'),

                    TextEntry::make('numero_licencia')
                        ->label('Número licencia'),

                    IconEntry::make('licencia_moto')
                        ->label('Licencia moto')
                        ->boolean(),

                    TextEntry::make('numero_licencia_moto')
                        ->label('Número licencia moto'),

                    IconEntry::make('permiso_estudio')
                        ->label('Permiso para estudiar')
                        ->boolean(),

                ])->columns(2)
                 ->columnSpanFull(),
            ]);
    }
}
