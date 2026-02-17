<?php

namespace App\Filament\Resources\Horarios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\TimePicker;
class HorarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('horas_jornada')
                    ->numeric(),
                TimePicker::make('hora_entrada')
                    ->required()
                    ->seconds(false)
                    ->native(false)
                    ->displayFormat('H:i')
                    ->format('H:i')
                    ->label('Hora Entrada'),

                TimePicker::make('hora_salida')
                    ->required()
                    ->seconds(false)
                    ->native(false)
                    ->label('Hora Salida')
                    ->displayFormat('H:i')
                    ->format('H:i'),

                ToggleButtons::make('cruza_medianoche')
                    ->label('Cruza Medianoche')
                    ->boolean(),

                TextInput::make('horas_personales')
                    ->required()
                    ->numeric(),
            ]);
    }
}
