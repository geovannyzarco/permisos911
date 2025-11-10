<?php

namespace App\Filament\Resources\Permisos\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;

class PermisosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                DatePicker::make('fecha_creacion')
                    ->label('Fecha de Creación')
                    ->default(Carbon::now())
                    ->readonly(),
                Select::Make('tipo_permiso_id')
                    ->label('Tipo de Permiso')
                    ->relationship('tipoPermiso', 'nombre')
                    ->required(),
                DatePicker::make('desde')
                    ->label('Desde')
                    ->required(),
                DatePicker::make('hasta')
                    ->label('Hasta')
                    ->required(),
                TextInput::make('motivo')
                    ->label('Motivo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('comentarios')
                    ->label('Comentarios')
                    ->maxLength(500),
                FileUpload::make('adjunto')
                    ->label('Adjunto')
                    ->maxSize(10240) // 10 MB
                    ->directory('permisos/adjuntos')
                    ->nullable(),

            ]);
    }
}
