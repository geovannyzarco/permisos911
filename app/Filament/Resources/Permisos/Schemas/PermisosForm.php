<?php

namespace App\Filament\Resources\Permisos\Schemas;

use App\Models\Empleado;
use App\Models\Permiso;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PermisosForm
{
    public static function configure(Schema $schema): Schema
    {
        // Closure para verificar si algún estado de aprobación ya no es "Pendiente" (4)
        $deshabilitarSiNoPendiente = fn(?Permiso $record) => $record !== null && (
            $record->id_estado_vb != 4 ||
            $record->id_estado_aprobacion != 4 ||
            $record->id_estado_aprobacion_jefe_division != 4
        );

        return $schema
            ->components([

                // INICIO MODIFICACIÓN: Paneles informativos y alertas de bloqueo/conflictos de cupos del empleado logueado

                // 1. Sección para mostrar la información del empleado autenticado (resumen de horas personales)
                Section::make('Información del empleado')
                    ->schema([
                        Placeholder::make('Horas_personales')
                            ->reactive()
                            ->content(function (?Permiso $record) {
                                // Obtenemos el empleado asociado al usuario que está logueado actualmente
                                $empleado = auth()->user()->empleado;

                                if (! $empleado) {
                                    return 'No se encontró un empleado asociado a tu usuario.';
                                }

                                // Instanciamos el servicio y obtenemos el resumen de sus horas personales
                                $permisoService = app(\App\Services\PermisoService::class);
                                $resumen = $permisoService->obtenerResumenHorasPersonales($empleado, $record);

                                $asignadas = $resumen['asignadas'];
                                $usadas = $resumen['usadas'];
                                $disponibles = $resumen['disponibles'];

                                // Retornamos el resumen en HTML amigable
                                return new HtmlString(
                                    "<strong>Horas asignadas:</strong> {$asignadas}<br>
                                     <strong>Horas utilizadas:</strong> {$usadas}<br>
                                     <strong>Horas disponibles:</strong> {$disponibles}"
                                );
                            }),
                    ])
                    ->columns(1)
                    // Visible solo si el usuario autenticado tiene un empleado asignado
                    ->visible(fn() => filled(auth()->user()->empleado)),

                // 2. Resumen de la cantidad de permisos aprobados del empleado en el año en curso
                Section::make('Permisos del año en curso')
                    ->schema([
                        Placeholder::make('Permisos')
                            ->reactive()
                            ->content(function () {
                                // Obtenemos el empleado asociado al usuario logueado
                                $empleado = auth()->user()->empleado;

                                if (! $empleado) {
                                    return 'No se encontró un empleado asociado a tu usuario.';
                                }
                                $anio = Carbon::now()->year;

                                // Consultamos la base de datos agrupando permisos aprobados del empleado en el año actual por tipo
                                $resumen = Permiso::query()
                                    ->selectRaw('tipo_permiso_id, COUNT(*) as total')
                                    ->where('empleado_id', $empleado->id)
                                    ->where('id_estado_aprobacion_jefe_division', 3) // ID 3 representa permisos con estado "Aprobado"
                                    ->whereYear('desde', $anio)
                                    ->groupBy('tipo_permiso_id')
                                    ->with('tipoPermiso:id,nombre')
                                    ->get();

                                if ($resumen->isEmpty()) {
                                    return 'No tienes permisos aprobados registrados en el año en curso.';
                                }

                                // Construimos la lista para mostrarla en el panel
                                $html = "<strong>Año {$anio}</strong><br><br>";
                                foreach ($resumen as $fila) {
                                    $tipo = $fila->tipoPermiso->nombre ?? 'Sin tipo';
                                    $html .= "• {$tipo}: {$fila->total}<br>";
                                }

                                return new HtmlString($html);
                            }),
                    ])
                    ->columns(1)
                    // Visible solo si el usuario autenticado tiene un empleado asignado
                    ->visible(fn() => filled(auth()->user()->empleado)),

                // 3. Sección de alertas de cupos bloqueados y permisos de otros miembros del grupo que interfieren en el rango
                Section::make('Permisos en Conflicto (Bloqueos de Cupo)')
                    ->schema([
                        Placeholder::make('permisos_conflictivos')
                            ->reactive()
                            ->content(function ($get, ?Permiso $record) {
                                $desde = $get('desde');
                                $hasta = $get('hasta');

                                // Requerimos que las fechas estén seleccionadas antes de hacer la validación
                                if (! $desde || ! $hasta) {
                                    return 'Seleccione fechas para evaluar posibles conflictos.';
                                }

                                // Obtenemos el empleado del usuario logueado y validamos que pertenezca a un grupo con límite
                                $empleado = auth()->user()->empleado;
                                if (! $empleado || ! $empleado->grupo || $empleado->grupo->id == 12 || empty($empleado->grupo->permisos_diarios)) {
                                    return 'No perteneces a un grupo con límite diario.';
                                }

                                $grupo = $empleado->grupo;
                                $limite = $grupo->permisos_diarios;

                                $fechaDesde = Carbon::parse($desde)->startOfDay();
                                $fechaHasta = Carbon::parse($hasta)->startOfDay();

                                if ($fechaHasta->lessThan($fechaDesde)) {
                                    return 'Rango de fechas inválido.';
                                }

                                $periodo = \Carbon\CarbonPeriod::create($fechaDesde, $fechaHasta);

                                if ($periodo->count() > 31) {
                                    return 'El rango de fechas es demasiado amplio para evaluar.';
                                }

                                $html = '';
                                $hayBloqueo = false;

                                // Recorremos cada día en el rango de fechas solicitado
                                foreach ($periodo as $fecha) {
                                    // Obtenemos los permisos ya creados por miembros de su mismo grupo para esta fecha
                                    $permisosEnEsteDia = Permiso::query()
                                        ->whereHas('empleado', function ($query) use ($grupo) {
                                            $query->where('grupo_id', $grupo->id);
                                        })
                                        ->whereDate('desde', '<=', $fecha)
                                        ->whereDate('hasta', '>=', $fecha)
                                        ->when($record, function ($query) use ($record) {
                                            $query->where('id', '!=', $record->id);
                                        })
                                        ->with('empleado')
                                        ->get();

                                    // MODIFICACIÓN: Evaluamos los bloqueos basándonos en empleados distintos
                                    $empleadosConPermiso = $permisosEnEsteDia->pluck('empleado_id')->unique()->toArray();
                                    $totalEmpleados = count($empleadosConPermiso);
                                    if (!in_array($empleado->id, $empleadosConPermiso)) {
                                        $totalEmpleados += 1;
                                    }

                                    // Si la cantidad de empleados distintos excede el límite permitido para el grupo
                                    if ($totalEmpleados > $limite) {
                                        $hayBloqueo = true;
                                        $html .= "<div style='margin-bottom: 12px;'>";
                                        $html .= "<strong style='color: #e53e3e;'>⚠️ El día {$fecha->format('d/m/Y')} está bloqueado (Límite: {$limite} empleados, Ocupados: " . count($empleadosConPermiso) . " empleados distintos):</strong>";
                                        $html .= "<ul style='list-style-type: disc; margin-left: 20px; color: #4a5568;'>";
                                        foreach ($permisosEnEsteDia as $p) {
                                            $desdeStr = Carbon::parse($p->desde)->format('d/m/Y H:i');
                                            $hastaStr = Carbon::parse($p->hasta)->format('d/m/Y H:i');
                                            $html .= "<li>Permiso #{$p->id}: <strong>{$p->empleado->nombre}</strong> (ONI: {$p->empleado->oni}) - Desde: {$desdeStr} Hasta: {$hastaStr}</li>";
                                        }
                                        $html .= "</ul>";
                                        $html .= "</div>";
                                    }
                                }

                                // Si no hay ningún bloqueo en el rango seleccionado, mostramos un mensaje verde
                                if (! $hayBloqueo) {
                                    return new HtmlString("<span style='color: #38a169; font-weight: bold;'>🟢 No hay bloqueos. Hay cupo disponible para el rango seleccionado.</span>");
                                }

                                return new HtmlString($html);
                            })
                    ])
                    ->columns(1)
                    // Visible si el usuario tiene empleado logueado y ha especificado las fechas
                    ->visible(fn($get) => filled(auth()->user()->empleado) && filled($get('desde')) && filled($get('hasta'))),

                // FIN MODIFICACIÓN

                DatePicker::make('fecha_creacion')
                    ->label('Fecha de Creación')
                    ->default(Carbon::now())
                    ->readonly(),
                Select::make('tipo_permiso_id')
                    ->label('Tipo de Permiso')
                    // MODIFICACIÓN: Filtrar los tipos de permiso según la categoría del empleado
                    ->relationship(
                        name: 'tipoPermiso',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: function ($query) {
                            // Obtenemos el empleado asociado al usuario logueado actualmente
                            $empleado = auth()->user()?->empleado;

                            // Si el empleado pertenece a la categoría ID = 24 (Telefonista),
                            // se excluye la opción de "tiempo compensatorio / compensado" (ID = 2
                            if ($empleado && $empleado->categoria_id == 24) {
                                $query->where('id', '!=', 2);
                            }
                        }
                    )
                    ->required()
                    ->live()
                    ->disabled($deshabilitarSiNoPendiente),
                DateTimePicker::make('desde')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required()
                    ->live() // MODIFICACIÓN: Hace que la vista reaccione en tiempo real al cambiar la fecha
                    ->disabled($deshabilitarSiNoPendiente),
                DateTimePicker::make('hasta')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required()
                    ->live() // MODIFICACIÓN: Hace que la vista reaccione en tiempo real al cambiar la fecha
                    ->disabled($deshabilitarSiNoPendiente)
                    ->rules([
                        fn($get, ?\App\Models\Permiso $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
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
                                // 1. Validamos que la fecha "hasta" no sea menor o igual a la fecha "desde"
                                if (! $service->validarRangoFechas($desde, $value)) {
                                    $fail('La fecha "hasta" debe ser mayor que "desde".');

                                    return; // Detenemos la ejecución aquí si el rango no tiene sentido
                                }

                                // 1.2. Validamos que la hora de inicio y de fin no sean ambas 00:00
                                if (! $service->validarHorasNoCero($desde, $value)) {
                                    $fail('No se permite registrar permisos con la hora de inicio (desde) y hora de fin (hasta) en 00:00.');

                                    return; // Detenemos la ejecución aquí
                                }

                                // 2. Validamos que el permiso no supere el límite de permisos diarios para el grupo
                                // Si se pasa del límite, la función lanzará una excepción que atraparemos más abajo
                                $service->validarLimitePermisosDiarios($empleado, $desde, $value, $record);

                                // MODIFICACIÓN: Validamos que no se traslapen los horarios del empleado (se evalúa siempre)
                                $service->validarNoTraslapeHoras($empleado, $desde, $value, $record);

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
                    ->visible(fn($get) => filled($get('desde')) && filled($get('hasta')))
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
                            $html .= "<li class='{$color}'>{$dia['fecha']}: {$dia['ocupados']} de {$dia['limite']} cupos ocupados (empleados distintos) {$estado}</li>";
                        }
                        $html .= '</ul>';

                        return new \Illuminate\Support\HtmlString($html);
                    }),
                TextInput::make('motivo')
                    ->label('Motivo')
                    ->required()
                    ->maxLength(255)
                    ->disabled($deshabilitarSiNoPendiente),
                FileUpload::make('adjunto')
                    ->label('Adjunto')
                    ->preserveFilenames()
                    ->downloadable()
                    ->maxSize(10240)
                    ->disk('public')
                    ->directory('permisos/adjuntos')
                    ->nullable()
                    ->disabled($deshabilitarSiNoPendiente),

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
                    ->visible(fn($get, ?\App\Models\Permiso $record) => $get('tipo_permiso_id') == 2 || $record?->tipo_permiso_id == 2)
                    ->minItems(fn($get, ?\App\Models\Permiso $record) => ($get('tipo_permiso_id') == 2 || $record?->tipo_permiso_id == 2) ? 1 : 0)
                    ->disabled($deshabilitarSiNoPendiente)
                    ->rules([
                        fn($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
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

                            // INICIO CAMBIO: Validar antigüedad de los periodos compensados (máximo 6 meses)
                            $service = app(\App\Services\PermisoService::class);
                            try {
                                $service->validarAntiguedadCompensados($value, $desdePrincipal);
                            } catch (\DomainException $e) {
                                $fail($e->getMessage());
                            }
                            // FIN CAMBIO
                        },
                    ]),

            ]);
    }
}
