<?php

namespace App\Filament\Resources\Empleados\Schemas;

use Dom\Text;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;

class EmpleadoInfolist
{
    public static function configure(Schema $schema): Schema
    {
               return $schema
            ->components([

                Section::make('Información Personal')
                    ->schema([

                        ImageEntry::make('foto')
                            ->disk('local')
                            ->circular(),

                        TextEntry::make('nombre'),

                        TextEntry::make('fecha_nacimiento')
                            ->date(),

                        TextEntry::make('genero')
                            ->formatStateUsing(fn ($state) =>
                                $state === 'M' ? 'Masculino' : 'Femenino'
                            ),

                        TextEntry::make('oni'),

                        TextEntry::make('email'),

                        TextEntry::make('telefono'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Ubicación')
                    ->schema([

                        TextEntry::make('departamento.nombre')
                            ->label('Departamento'),

                        TextEntry::make('municipio.nombre')
                            ->label('Municipio'),

                        TextEntry::make('distrito.nombre')
                            ->label('Distrito'),

                        TextEntry::make('direccion'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Información Laboral')
                    ->schema([

                        TextEntry::make('fecha_ingreso')
                            ->date(),

                        TextEntry::make('grupo.nombre'),

                        TextEntry::make('categoria.nombre'),

                        TextEntry::make('horario.nombre'),

                        TextEntry::make('unidad.nombre'),

                        TextEntry::make('nivel.nivel'),

                        TextEntry::make('estado.nombre'),

                        TextEntry::make('codigo_huella'),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Licencias y permisos')
                    ->schema([

                        TextEntry::make('permiso_portacion_arma')
                            ->label('Permiso portación arma')
                            ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),

                        TextEntry::make('numero_permiso_arma'),

                        TextEntry::make('licencia_conducir')
                            ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),

                        TextEntry::make('tipo_licencia'),

                        TextEntry::make('numero_licencia'),

                        TextEntry::make('licencia_moto')
                            ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),

                        TextEntry::make('numero_licencia_moto'),

                        TextEntry::make('permiso_estudio')
                            ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Información Familiar')
                    ->schema([

                        TextEntry::make('estadoCivil.nombre')
                            ->label('Estado Civil'),

                        TextEntry::make('nombre_conyuge'),

                        TextEntry::make('numero_hijos'),

                        RepeatableEntry::make('hijos')
                            ->schema([
                                TextEntry::make('nombre'),
                                TextEntry::make('fecha_nacimiento')
                                    ->date(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Firma')
                    ->schema([

                        ImageEntry::make('firma')
                            ->label('Firma del empleado')


                    ]),

            ]);

    }
}
