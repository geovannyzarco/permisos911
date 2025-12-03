<?php

namespace App\Filament\Resources\Compensados\Schemas;

use Dom\Text;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;

class CompensadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('desde')
                    ->label('Inicio Actividad')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required(),
                DateTimePicker::make('hasta')
                    ->label('Fin Actividad')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required(),
                Textarea::make('justificacion')
                    ->label('Justificación')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('adjunto')
                    ->label('Adjunto')
                    ->columnSpanFull(),
            ]);
    }
}
