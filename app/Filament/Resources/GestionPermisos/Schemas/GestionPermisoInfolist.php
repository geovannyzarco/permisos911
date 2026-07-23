<?php

namespace App\Filament\Resources\GestionPermisos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\ImageEntry;
use App\Models\Empleado;
use App\Models\Permiso;
use App\Models\TipoPermiso;
use App\Models\Compensado;
use Filament\Infolists\Components\RepeatableEntry;


class GestionPermisoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permiso Tramitado')
                    ->schema([
                        TextEntry::make('tramitado')
                            ->label('¿Este permiso ya fue tramitado?')
                            ->helperText('Marque esto si el permiso ya fue ingresado al sistema SAAP')
                            ->formatStateUsing(fn($state) => $state ? 'Sí' : 'No'),
                    ])->columns(1)
                    ->columnSpanFull(),

                Section::make('Informacion del empleado')
                    ->schema([
                        ImageEntry::make('empleado.foto')
                            ->label('Foto'),
                        TextEntry::make('empleado.oni')
                            ->label('ONI'),
                        TextEntry::make('empleado.nombre')
                            ->label('Nombre'),
                        TextEntry::make('empleado.unidad.nombre')
                            ->label('Unidad'),
                        TextEntry::make('empleado.grupo.nombre')
                            ->label('Grupo'),
                        TextEntry::make('empleado.horario.nombre')
                            ->label('Horario'),

                    ])->columns(3)
                    ->columnSpanFull(),

                Section::make('Detalles del permiso')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID del permiso'),
                        TextEntry::make('fecha_creacion')
                            ->label('Fecha de Creación'),
                        TextEntry::make('tipoPermiso.nombre')
                            ->label('Tipo de Permiso'),
                        TextEntry::make('desde')
                            ->label('Desde'),
                        TextEntry::make('hasta')
                            ->label('Hasta'),
                        TextEntry::make('duracion')
                            ->label('Duración'),
                        TextEntry::make('motivo')
                            ->label('Motivo'),
                        TextEntry::make('adjunto')
                            ->label('Anexo')
                            ->color('primary')
                            ->formatStateUsing(function ($state) {
                                if ($state) {
                                    return '<a href="' . asset('storage/' . $state) . '" target="_blank">Ver Anexo</a>';
                                }

                                return 'No hay adjunto';
                            })
                            ->html(),

                    ])->columns(3)
                    ->columnSpanFull(),

                Section::make('Detalle del tiempo compensado')
                    ->schema([
                        RepeatableEntry::make('compensados')
                            ->label('')
                            ->schema([
                                TextEntry::make('desde')
                                    ->label('Desde')
                                    ->dateTime('d/m/Y H:i'),
                                TextEntry::make('hasta')
                                    ->label('Hasta')
                                    ->dateTime('d/m/Y H:i'),
                                TextEntry::make('justificacion')
                                    ->label('Justificación')
                                    ->columnSpanFull(),
                                TextEntry::make('adjunto')
                                    ->label('Anexo')
                                    ->color('primary')
                                    ->formatStateUsing(function ($state) {
                                        if ($state) {
                                            return '<a href="' . asset('storage/' . $state) . '" target="_blank">Ver Anexo</a>';
                                        }
                                        return 'No hay adjunto';
                                    })
                                    ->html()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->grid(2),
                    ])
                    ->columnSpanFull()
                    ->visible(fn($record): bool => ($record?->tipo_permiso_id ?? null) == 2),

                Section::make('Aprobaciones')
                    ->schema([
                        TextEntry::make('estadoVB.nombre')
                            ->label('Visto Bueno'),
                        TextEntry::make('jefeVB.nombre')
                            ->label('Supervisor que dio Visto Bueno'),
                        TextEntry::make('estadoAprobado.nombre')
                            ->label('Aprobación de Jefatura'),
                        TextEntry::make('jefeAprobacion.nombre')
                            ->label('Jefe que aprobó'),
                        TextEntry::make('id_oni_jefe_division')
                            ->label('ONI Jefe División'),
                        TextEntry::make('fecha_aprobacion_jefe_division')
                            ->label('Fecha Aprobación Jefe División'),
                        TextEntry::make('estadoAprobacionJefeDivision.nombre')
                            ->label('Estado Aprobación Jefe División'),
                        TextEntry::make('comentarios')
                            ->label('Comentarios'),

                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
