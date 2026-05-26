<?php

namespace App\Filament\Resources\Permisos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;

class PermisosInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del permiso')
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('fecha_creacion')->label('Fecha de Creación')->dateTime(),
                        TextEntry::make('tipoPermiso.nombre')->label('Tipo de Permiso'),
                        TextEntry::make('empleado.oni')->label('ONI'),
                        TextEntry::make('empleado.nombre')->label('Empleado'),

                        TextEntry::make('desde')->label('Desde')->dateTime('d/m/Y H:i'),
                        TextEntry::make('hasta')->label('Hasta')->dateTime('d/m/Y H:i'),
                        TextEntry::make('duracion')->label('Duración'),
                        TextEntry::make('motivo')->label('Motivo'),
                        TextEntry::make('adjunto')
                            ->label('Adjunto')
                            ->formatStateUsing(fn($state) => filled($state) ? 'Sí' : 'No'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),



                Section::make('Información de aprobación')
                    ->schema([
                        TextEntry::make('estadoVB.nombre')->label('Visto Bueno'),
                        TextEntry::make('jefeVb.nombre')->label('Supervisor que dio Visto Bueno'),
                        TextEntry::make('fecha_vb')->label('Fecha que se dio el Visto Bueno')->dateTime('d/m/Y H:i'),
                        TextEntry::make('estadoAprobado.nombre')->label('Aprobación Jefatura de Departamento'),
                        TextEntry::make('fecha_aprobacion')->label('Fecha de Aprobación')->dateTime('d/m/Y H:i'),
                        TextEntry::make('jefeAprobacion.nombre')->label('Jefe que aprobó la solicitud'),
                        TextEntry::make('estadoAprobacionJefeDivision.nombre')->label('Aprobación Jefatura de División'),
                        TextEntry::make('id_oni_jefe_division')
                            ->label('Jefe de División'),
                        TextEntry::make('fecha_aprobacion_jefe_division')
                            ->label('Fecha de Aprobación Jefe División'),


                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
