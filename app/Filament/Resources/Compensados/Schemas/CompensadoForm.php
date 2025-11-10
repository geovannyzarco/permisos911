<?php

namespace App\Filament\Resources\Compensados\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CompensadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('desde')
                    ->required(),
                DateTimePicker::make('hasta')
                    ->required(),
                Textarea::make('justificacion')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('adjunto')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('permiso_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
