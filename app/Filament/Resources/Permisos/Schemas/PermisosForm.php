<?php

namespace App\Filament\Resources\Permisos\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                Select::make('tipo_permiso_id')
                    ->label('Tipo de Permiso')
                    ->relationship('tipoPermiso', 'nombre')

                    ->required()
                    ->live(),
                DateTimePicker::make('desde')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required()
                    ->live(), // MODIFICACIÓN: Hace que la vista reaccione en tiempo real al cambiar la fecha
                DateTimePicker::make('hasta')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required()
                    ->live() // MODIFICACIÓN: Hace que la vista reaccione en tiempo real al cambiar la fecha
                    ->rules([
                        fn ($get, ?\App\Models\Permiso $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            // Obtenemos la fecha "desde" seleccionada en el formulario
                            $desde = $get('desde');

                            // Obtenemos el empleado asociado al usuario que está logueado actualmente
                            $empleado = auth()->user()->empleado;

                            // Obtenemos el ID del tipo de permiso que fue seleccionado
                            $tipoPermisoId = $get('tipo_permiso_id');

                            // Si falta algún dato importante (no se han llenado todos los campos), no validamos aún
                            if (! $desde || ! $value || ! $empleado) {
                                return;
                            }

                            // Instanciamos el servicio de permisos que contiene toda la lógica de negocio
                            $service = app(\App\Services\PermisoService::class);

                            try {
                                // 1. Validamos que la fecha "hasta" no sea menor a la fecha "desde"
                                if (! $service->validarRangoFechas($desde, $value)) {
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
                                    if (! $service->puedeGuardarPermisoPersonal($empleado, $horasSolicitadas, $record)) {
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
                // MODIFICACIÓN: Nuevo componente Placeholder para mostrar la disponibilidad diaria del grupo
                \Filament\Forms\Components\Placeholder::make('disponibilidad_grupo')
                    ->label('Disponibilidad del Grupo')
                    // Solo es visible si el usuario ya escogió ambas fechas (desde y hasta)
                    ->visible(fn ($get) => filled($get('desde')) && filled($get('hasta')))
                    ->content(function ($get, ?\App\Models\Permiso $record) {
                        $desde = $get('desde');
                        $hasta = $get('hasta');

                        $empleado = auth()->user()->empleado;

                        // Si falta algún dato vital, mostramos un mensaje de guía
                        if (! $empleado || ! $desde || ! $hasta) {
                            return 'Seleccione fechas para consultar disponibilidad.';
                        }

                        // Llamamos a nuestro servicio central para que calcule cuántos permisos hay cada día
                        $service = app(\App\Services\PermisoService::class);
                        $disponibilidad = $service->obtenerDisponibilidadDiaria($empleado, $desde, $hasta, $record);

                        // Si el rango es demasiado largo (ej. más de 31 días), mostramos error
                        if (isset($disponibilidad['error'])) {
                            return new \Illuminate\Support\HtmlString("<span class='text-danger-600 font-bold'>{$disponibilidad['error']}</span>");
                        }

                        // Si el grupo del empleado no tiene límite diario en base de datos, lo indicamos
                        if (empty($disponibilidad)) {
                            return 'Tu grupo no tiene configurado un límite diario.';
                        }

                        // Renderizamos los resultados en formato de lista (puntos viñeta) con Tailwind CSS
                        $html = '<ul class="list-disc list-inside space-y-1">';
                        foreach ($disponibilidad as $dia) {
                            $color = $dia['disponible'] ? 'text-success-600 font-medium' : 'text-danger-600 font-bold';
                            $estado = $dia['disponible'] ? '(🟢 Disponible)' : '(🔴 Lleno)';
                            $html .= "<li class='{$color}'>{$dia['fecha']}: {$dia['ocupados']} de {$dia['limite']} permisos ocupados {$estado}</li>";
                        }
                        $html .= '</ul>';

                        return new \Illuminate\Support\HtmlString($html);
                    }),
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

                \Filament\Forms\Components\Repeater::make('compensados')
                    ->relationship()
                    ->label('Periodos Compensados a Utilizar')
                    ->schema([
                        \Filament\Forms\Components\DateTimePicker::make('desde')
                            ->label('Desde')
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->withoutSeconds()
                            ->native(false)
                            ->required(),
                        \Filament\Forms\Components\DateTimePicker::make('hasta')
                            ->label('Hasta')
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->withoutSeconds()
                            ->native(false)
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('justificacion')
                            ->label('Justificación')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\FileUpload::make('adjunto')
                            ->label('Adjunto')
                            ->preserveFilenames()
                            ->downloadable()
                            ->maxSize(10240)
                            ->disk('public')
                            ->directory('permisos/compensados_adjuntos')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn ($get) => $get('tipo_permiso_id') == 2)
                    ->rules([
                        fn ($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                            // 1. Obtenemos las fechas generales del permiso solicitadas en la parte de arriba del formulario
                            $desdePrincipal = $get('desde');
                            $hastaPrincipal = $get('hasta');

                            // 2. Comprobamos si es necesario validar:
                            // Si no es tipo "Compensado" (ID 2), o si no han llenado el desde/hasta, o si no hay items en el repeater,
                            // entonces simplemente salimos y no hacemos validaciones numéricas.
                            if ($get('tipo_permiso_id') != 2 || ! $desdePrincipal || ! $hastaPrincipal || ! is_array($value) || empty($value)) {
                                return;
                            }

                            // 3. Calculamos la duración total en MINUTOS del permiso principal.
                            // Usamos diffInMinutes de Carbon para ser exactos.
                            $minutosPrincipales = \Carbon\Carbon::parse($desdePrincipal)->diffInMinutes(\Carbon\Carbon::parse($hastaPrincipal));

                            // 4. Vamos a recorrer todos los items que el usuario agregó a la lista (Repeater)
                            $minutosCompensados = 0;
                            foreach ($value as $item) {
                                // Validamos que el item de la lista tenga llenas sus propias fechas desde/hasta
                                if (isset($item['desde']) && isset($item['hasta'])) {
                                    // Sumamos la duración de este item individual al total acumulado
                                    $minutosCompensados += \Carbon\Carbon::parse($item['desde'])->diffInMinutes(\Carbon\Carbon::parse($item['hasta']));
                                }
                            }

                            // 5. Comparamos ambos resultados: los minutos solicitados vs los minutos respaldados en la tabla
                            if ($minutosPrincipales !== $minutosCompensados) {
                                // Si no coinciden, convertimos los minutos a horas para mostrarle un mensaje amigable al usuario
                                $horasP = round($minutosPrincipales / 60, 2);
                                $horasC = round($minutosCompensados / 60, 2);
                                // Arrojamos el error (esto evita que el formulario se guarde)
                                $fail("La suma de horas de los periodos compensados ({$horasC} hrs) debe ser exactamente igual a las horas solicitadas en el permiso ({$horasP} hrs).");
                            }
                        },
                    ]),

            ]);
    }
}
