<?php

namespace App\Filament\Resources\GestionPermisos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GestionPermisoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de aprobación')
                    ->schema([
                        TextEntry::make('id_oni_jefe_division')
                            ->label('ONI Jefe División'),
                        TextEntry::make('fecha_aprobacion_jefe_division')
                            ->label('Fecha Aprobación Jefe División'),
                        TextEntry::make('id_estado_aprobacion_jefe_division')
                            ->label('Estado Aprobación Jefe División'),
                        TextEntry::make('tramitado')
                            ->label('Tramitado')
                            ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
