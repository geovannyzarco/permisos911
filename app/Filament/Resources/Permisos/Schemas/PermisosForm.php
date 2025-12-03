<?php

namespace App\Filament\Resources\Permisos\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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
                DateTimePicker::make('desde')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required(),
                DateTimePicker::make('hasta')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required(),
                TextInput::make('motivo')
                    ->label('Motivo')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('adjunto')
                    ->label('Adjunto')
                    ->preserveFilenames()
                     ->downloadable()
                    ->maxSize(10240)
                    ->disk('public')
                    ->directory('permisos/adjuntos')
                    ->nullable(),

            ]);
    }
}
