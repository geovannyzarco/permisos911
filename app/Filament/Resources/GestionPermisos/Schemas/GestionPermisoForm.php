<?php
namespace App\Filament\Resources\GestionPermisos\Schemas;

use Carbon\Carbon;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class GestionPermisoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                 |-------------------------------------------------
                 | DATOS DEL PERMISO
                 |-------------------------------------------------
                 */

                        DatePicker::make('fecha_creacion')
                            ->label('Fecha de Creación')
                            ->default(Carbon::now())
                            ->readonly(),

                        Select::make('empleado_id')
                            ->label('Empleado')
                            ->relationship('empleado', 'nombre')
                            ->searchable()
                            ->required(),

                        Select::make('tipo_permiso_id')
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


                /*
                 |-------------------------------------------------
                 | COMPENSADOS (SOLO SI ES TIPO 2 Y EN EDICIÓN)
                 |-------------------------------------------------
                 */
                Section::make('Compensados')
                    ->visible(fn ($record) => $record?->tipo_permiso_id == 2)
                    ->schema([
                        Repeater::make('compensados')
                            ->relationship()
                            ->schema([
                                DatePicker::make('fecha')
                                    ->label('Fecha')
                                    ->required(),

                                TextInput::make('horas')
                                    ->label('Horas')
                                    ->numeric()
                                    ->required(),
                            ])

                            ->createItemButtonLabel('Agregar compensado'),
                    ]),
            ]);
    }
}
