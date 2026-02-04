<?php

namespace App\Filament\Resources\Empleados\Schemas;

use App\Models\Categoria;
use App\Models\Estado;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Nivel;
use App\Models\Unidad;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Date;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class EmpleadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oni')
                ->required(),
                TextInput::make('nombre')
                ->required(),
                DatePicker::make('fecha_ingreso')
                ->label('Fecha de Ingreso'),
                DatePicker::make('fecha_nacimiento')
                ->label('Fecha de Nacimiento'),
                TextInput::make('codigo_huella')
                ->label('Código de Huella')
                ->numeric(),
                Select::make('estado_civil_id')
                ->label('Estado Civil')
                ->relationship('estadoCivil','nombre'),
                TextInput::make('nombre_conyuge')
                ->label('Nombre del Cónyuge'),
                TextInput::make('numero_hijos')
                ->label('Número de Hijos')
                ->numeric(),

                TextInput::make('email')
                ->label('Correo Electrónico')
                ->email(),
                TextInput::make('telefono')
                ->label('Teléfono de Contacto'),
                Textarea::make('direccion')
                ->label('Dirección de Residencia'),
                Select::make('genero')
                ->label('Género')
                ->required()
                ->options([
                    'M' => 'Masculino',
                    'F' => 'Femenino',
                ]),



                Select::make('grupo_id')
                ->required()
                ->label('Grupo')
                ->options(Grupo::query()->pluck('nombre', 'id')),
                Select::make('categoria_id')
                    ->required()
                    ->label('Categoría')
                    ->options(Categoria::query()->pluck('nombre','id')),

                Select::make('horario_id')
                    ->required()
                    ->label('Horario')
                    ->options(Horario::query()->pluck('nombre','id')),

                Select::make('unidad_id')
                    ->required()
                    ->label('Unidad')
                    ->options(Unidad::query()->pluck('nombre','id')),

                Select::make('nivel_id')
                    ->required()
                    ->label('Nivel')
                    ->options(Nivel::query()->pluck('nivel','id')),

                Select::make('estado_id')
                    ->required()
                    ->label('Estado')
                    ->options(Estado::query()
                        ->where('entidad_id',1)
                        ->pluck('nombre','id')),
                FileUpload::make('Foto')
                    ->label('Foto')
                    ->image(),

                SignaturePad::make('firma')
                    ->label('Firma del empleado')
                    ->backgroundColor('#f2efef')
                    ->dehydrateStateUsing(fn($state) => $state)
                     ->columnSpanFull(),
                    //https://filamentphp.com/plugins/saade-autograph
                    //->dotSize(2.0)
                   // ->lineMinWidth(0.5)
                   // ->lineMaxWidth(2.5)
                   // ->throttle(16)
                   // ->minDistance(5)
                   // ->velocityFilterWeight(0.7)
                   //->backgroundColorOnDark('#f0a'),     // Background color on dark mode (defaults to backgroundColor)
                   // ->exportBackgroundColor('#f00')     // Background color on export (defaults to backgroundColor)
                   // ->penColor('#000')                  // Pen color on light mode
                   // ->penColorOnDark('#fff')            // Pen color on dark mode (defaults to penColor)
                   // ->exportPenColor('#0f0')            // Pen color on export (defaults to penColor)
            ]);
    }
}
