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
                    ->required()
                    ->rules([
                        fn (\Filament\Forms\Get $get, ?\App\Models\Permiso $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            // Obtenemos la fecha "desde" seleccionada en el formulario
                            $desde = $get('desde');
                            
                            // Obtenemos el empleado asociado al usuario que está logueado actualmente
                            $empleado = auth()->user()->empleado;
                            
                            // Obtenemos el ID del tipo de permiso que fue seleccionado
                            $tipoPermisoId = $get('tipo_permiso_id');

                            // Si falta algún dato importante (no se han llenado todos los campos), no validamos aún
                            if (!$desde || !$value || !$empleado) {
                                return;
                            }

                            // Instanciamos el servicio de permisos que contiene toda la lógica de negocio
                            $service = app(\App\Services\PermisoService::class);

                            try {
                                // 1. Validamos que la fecha "hasta" no sea menor a la fecha "desde"
                                if (!$service->validarRangoFechas($desde, $value)) {
                                    $fail('La fecha "hasta" debe ser mayor o igual que "desde".');
                                    return; // Detenemos la ejecución aquí si el rango no tiene sentido
                                }

                                // 2. Validamos que el permiso no supere el límite de permisos diarios para el grupo
                                // Si se pasa del límite, la función lanzará una excepción que atraparemos más abajo
                                $service->validarLimitePermisosDiarios($empleado, $desde, $value, $record);

                                // 3. Si el permiso es de tipo Personal (ID 1), procedemos a validar su saldo de horas
                                if ($tipoPermisoId == 1) {
                                    // Calculamos cuántas horas está solicitando en total usando Carbon
                                    $horasSolicitadas = \Carbon\Carbon::parse($desde)->diffInMinutes(\Carbon\Carbon::parse($value)) / 60;
                                    
                                    // Verificamos si la suma de lo solicitado + lo usado es menor a lo que tiene en su horario
                                    if (!$service->puedeGuardarPermisoPersonal($empleado, $horasSolicitadas, $record)) {
                                        $fail('El tiempo solicitado excede el saldo de horas personales disponibles según tu horario.');
                                    }
                                }
                            // Capturamos cualquier excepción de dominio (por ejemplo, el mensaje de límite diario excedido)
                            } catch (\DomainException $e) {
                                // Y lo mostramos como un error en pantalla bajo el campo "hasta"
                                $fail($e->getMessage());
                            }
                        },
                    ]),
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
