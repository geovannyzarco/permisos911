<?php

namespace App\Filament\Resources\Empleados\Schemas;

use App\Models\Categoria;
use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Estado;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Municipio;
use App\Models\Nivel;
use App\Models\Unidad;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class EmpleadoForm
{
    public static function configure(Schema $schema): Schema
    {
        $isEmpleado = auth()->user()?->hasRole('Empleados') ?? false;

        return $schema
            ->components([

                // informacion Personal
                Section::make('Información Personal')
                    ->schema([
                        FileUpload::make('foto')
                            ->image()
                            ->disabled($isEmpleado),

                        TextInput::make('nombre')
                            ->required(),

                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento'),

                        Select::make('genero')
                            ->label('Género')
                            ->required()
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Femenino',
                            ]),

                        TextInput::make('oni')
                            ->required()
                            ->disabled($isEmpleado),

                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email(),

                        TextInput::make('telefono')
                            ->label('Teléfono de Contacto'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Ubicacion
                Section::make('Ubicación')
                    ->schema([
                        Select::make('departamento_id')
                            ->label('Departamento')
                            ->options(Departamento::pluck('nombre', 'id'))
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('municipio_id', null))
                            ->required(),

                        Select::make('municipio_id')
                            ->label('Municipio')
                            ->options(fn (Get $get) => Municipio::query()
                                ->where('departamento_id', $get('departamento_id'))
                                ->pluck('nombre', 'id')
                            )
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('distrito_id', null))
                            ->required(),

                        Select::make('distrito_id')
                            ->label('Distrito')
                            ->options(fn (Get $get) => Distrito::query()
                                ->where('municipio_id', $get('municipio_id'))
                                ->pluck('nombre', 'id')
                            )
                            ->required(),

                        TextInput::make('direccion')
                            ->label('Dirección de Residencia'),
                    ])->columns(2)
                    ->columnSpanFull(),

                // Información laboral
                Section::make('Información Laboral')
                    ->schema([

                        DatePicker::make('fecha_ingreso')
                            ->label('Fecha de Ingreso'),

                        Select::make('grupo_id')
                            ->required()
                            ->label('Grupo')
                            ->options(Grupo::query()->pluck('nombre', 'id'))
                            ->disabled($isEmpleado),

                        Select::make('categoria_id')
                            ->required()
                            ->label('Categoría')
                            ->options(Categoria::query()->pluck('nombre', 'id')),
                        // ->disabled($isEmpleado),

                        Select::make('horario_id')
                            ->required()
                            ->label('Horario')
                            ->options(Horario::query()->pluck('nombre', 'id'))
                            ->disabled($isEmpleado),

                        Select::make('unidad_id')
                            ->required()
                            ->label('Unidad')
                            ->options(Unidad::query()->pluck('nombre', 'id'))
                            ->disabled($isEmpleado),

                        Select::make('nivel_id')
                            ->required()
                            ->label('Nivel')
                            ->options(Nivel::query()->pluck('nivel', 'id'))
                            ->disabled($isEmpleado),

                        Select::make('estado_id')
                            ->required()
                            ->label('Estado')
                            ->options(
                                Estado::query()
                                    ->where('entidad_id', 1)
                                    ->pluck('nombre', 'id')
                            )
                            ->disabled($isEmpleado),

                        TextInput::make('codigo_huella')
                            ->label('Código de Huella')
                            ->numeric()
                            ->disabled($isEmpleado),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Licencias y permisos
                Section::make('Licencias y permisos')
                    ->schema([

                        Select::make('permiso_portacion_arma')
                            ->label('Permiso de portación de arma')
                            ->options([
                                1 => 'Sí',
                                0 => 'No',
                            ])
                            ->required()
                            ->default(0),

                        TextInput::make('numero_permiso_arma')
                            ->label('Número permiso arma'),

                        Select::make('licencia_conducir')
                            ->label('Licencia de conducir')
                            ->options([
                                1 => 'Sí',
                                0 => 'No',
                            ])
                            ->required()
                            ->default(0),

                        Select::make('tipo_licencia')
                            ->label('Tipo de licencia')
                            ->options([
                                'liviana' => 'Liviana',
                                'pesada' => 'Pesada',
                                'particular' => 'Particular',
                            ]),

                        TextInput::make('numero_licencia')
                            ->label('Número de licencia'),

                        Select::make('licencia_moto')
                            ->label('Licencia para motocicleta')
                            ->options([
                                1 => 'Sí',
                                0 => 'No',
                            ])
                            ->required()
                            ->default(0),

                        TextInput::make('numero_licencia_moto')
                            ->label('Número licencia moto'),

                        Select::make('permiso_estudio')
                            ->label('Permiso para estudiar')
                            ->options([
                                1 => 'Sí',
                                0 => 'No',
                            ])
                            ->required()
                            ->default(0),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Información familiar
                Section::make('Información Familiar')
                    ->schema([
                        Select::make('estado_civil_id')
                            ->label('Estado Civil')
                            ->relationship('estadoCivil', 'nombre'),

                        TextInput::make('nombre_conyuge')
                            ->label('Nombre del Cónyuge'),

                        TextInput::make('numero_hijos')
                            ->label('Número de Hijos')
                            ->numeric(),
                        Repeater::make('hijos')
                            ->relationship()
                            ->schema([
                                TextInput::make('nombre'),
                                DatePicker::make('fecha_nacimiento'),
                            ])->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Firma del empleado
                Section::make('Firma del Empleado')
                    ->schema([
                        SignaturePad::make('firma')

                            ->backgroundColor('#f2efef')
                            ->dehydrateStateUsing(fn ($state) => $state)
                            ->columnSpanFull()
                            ->dotSize(2.0)
                            ->lineMinWidth(0.5)
                            ->lineMaxWidth(2.5)
                            ->throttle(16)
                            ->minDistance(5)
                            ->velocityFilterWeight(0.7)
                            ->exportBackgroundColor('#fff'),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
