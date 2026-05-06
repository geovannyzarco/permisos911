<?php

namespace App\Filament\Resources\GestionPermisos\Schemas;

use App\Models\Empleado;
use App\Models\Permiso;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class GestionPermisoForm
{
    public static function configure(Schema $schema): Schema
    {
        // Mostrar de las horas personales y permisos del empleado seleccionado
        return $schema
            ->components([
                // informacion de las horas personales
                Section::make('Informacion del empleado')
                    ->schema([
                        Placeholder::make('Horas_personales')
                            ->reactive()
                            ->content(function ($get, ?\App\Models\Permiso $record) {

                                $empleadoId = $get('empleado_id');

                                if (! $empleadoId) {
                                    return 'Seleccione un empleado para ver la información.';
                                }

                                $empleado = Empleado::find($empleadoId);

                                if (! $empleado) {
                                    return 'Empleado no encontrado.';
                                }

                                $permisoService = app(\App\Services\PermisoService::class);
                                $resumen = $permisoService->obtenerResumenHorasPersonales($empleado, $record);

                                $asignadas = $resumen['asignadas'];
                                $usadas = $resumen['usadas'];
                                $disponibles = $resumen['disponibles'];

                                return new HtmlString(
                                    "<strong>Horas asignadas:</strong> {$asignadas}<br>
                                            <strong>Horas utilizadas:</strong> {$usadas}<br>
                                            <strong>Horas disponibles:</strong> {$disponibles}"
                                );
                            }),
                    ])
                    ->columns(1)
                    ->visible(fn ($get) => filled($get('empleado_id'))),

                // Resumen de la cantidad permisos del empleado en el año en curso
                Section::make('Permisos del año en curso')
                    ->schema([
                        Placeholder::make('Permisos')
                            ->reactive()
                            ->content(function ($get) {
                                $empleadoId = $get('empleado_id');

                                if (! $empleadoId) {
                                    return 'Seleccione un empleado para ver la información.';
                                }
                                $anio = Carbon::now()->year;

                                $empleado = Empleado::find($empleadoId);

                                if (! $empleado) {
                                    return 'Empleado no encontrado.';
                                }

                                $resumen = Permiso::query()
                                    ->selectRaw('tipo_permiso_id, COUNT(*) as total')
                                    ->where('empleado_id', $empleadoId)
                                    ->where('id_estado_aprobacion', 3) // Solo permisos aprobados
                                    ->whereYear('desde', $anio)
                                    ->groupBy('tipo_permiso_id')
                                    ->with('tipoPermiso:id,nombre')
                                    ->get();

                                if ($resumen->isEmpty()) {
                                    return 'No hay permisos registrados para este empleado en el año en curso.';
                                }

                                $html = "<strong>Año {$anio}</strong><br><br>";
                                foreach ($resumen as $fila) {
                                    $tipo = $fila->tipoPermiso->nombre ?? 'Sin tipo';
                                    $html .= "• {$tipo}: {$fila->total}<br>";
                                }

                                return new HtmlString($html);
                            }),
                    ])
                    ->columns(1)
                    ->visible(fn ($get) => filled($get('empleado_id'))),

                Image::make(
                    url: fn ($get) => route('foto.empleado', [
                        'filename' => optional(
                            Empleado::find($get('empleado_id'))
                        )->foto ?? 'dummy.jpg', // nunca null
                    ]),
                    alt: 'Foto del empleado',
                )
                    ->visible(function ($get) {
                        $empleado = Empleado::find($get('empleado_id'));

                        return filled($empleado?->foto);
                    }),

                DatePicker::make('fecha_creacion')
                    ->label('Fecha de Creación')
                    ->default(Carbon::now())
                    ->readonly(),

                Select::make('empleado_id')
                    ->label('Empleado')
                    ->reactive()
                    ->searchable()
                    ->required()
                    // Para buscar por nombre y oni en el select
                    ->getSearchResultsUsing(function (string $search) {
                        return Empleado::query()
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('oni', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($e) => [
                                $e->id => "{$e->oni} - {$e->nombre}",
                            ]);
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $empleado = Empleado::find($value);

                        return $empleado
                            ? "{$empleado->oni} - {$empleado->nombre} "
                            : '';
                    }),

                Select::make('tipo_permiso_id')
                    ->label('Tipo de Permiso')
                    ->relationship('tipoPermiso', 'nombre')
                    ->required()
                    ->live(),

                // MODIFICACIÓN: Nuevo interruptor para que el administrador pueda registrar permisos pasados saltándose las restricciones.
                \Filament\Forms\Components\Toggle::make('ignorar_validaciones')
                    ->label('Es un permiso retroactivo / Ignorar validaciones (Límite diario y Horas)')
                    ->helperText('Activa esto si se van a ingresar un permiso con fecha anterior a hoy y el sistema no lo bloquee por límites de grupo o saldos.')
                    ->dehydrated(false) // Esto evita que Filament intente guardar este campo en la tabla de base de datos
                    ->live()
                    ->columnSpanFull(),

                DateTimePicker::make('desde')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required()
                    ->live(), // MODIFICACIÓN: Reactividad para escuchar cambios de fecha en tiempo real

                DateTimePicker::make('hasta')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->format('Y-m-d H:i')
                    ->withoutSeconds()
                    ->required()
                    ->live() // MODIFICACIÓN: Reactividad para escuchar cambios de fecha en tiempo real
                    ->rules([
                        fn ($get, ?\App\Models\Permiso $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                            $desde = $get('desde');
                            $empleadoId = $get('empleado_id');
                            $tipoPermisoId = $get('tipo_permiso_id');

                            $ignorarValidaciones = $get('ignorar_validaciones');

                            if (! $desde || ! $value || ! $empleadoId) {
                                return;
                            }

                            $empleado = \App\Models\Empleado::find($empleadoId);
                            if (! $empleado) {
                                return;
                            }

                            $service = app(\App\Services\PermisoService::class);

                            try {
                                if (! $service->validarRangoFechas($desde, $value)) {
                                    $fail('La fecha "hasta" debe ser mayor o igual que "desde".');

                                    return;
                                }

                                // MODIFICACIÓN: Si el administrador activó el interruptor, nos detenemos aquí.
                                // De esta manera no se validan los cupos diarios ni las horas personales.
                                if ($ignorarValidaciones) {
                                    return;
                                }

                                $service->validarLimitePermisosDiarios($empleado, $desde, $value, $record);

                                if ($tipoPermisoId == 1) {
                                    $horasSolicitadas = \Carbon\Carbon::parse($desde)->diffInMinutes(\Carbon\Carbon::parse($value)) / 60;
                                    if (! $service->puedeGuardarPermisoPersonal($empleado, $horasSolicitadas, $record)) {
                                        $fail('El tiempo solicitado excede el saldo de horas personales disponibles según tu horario.');
                                    }
                                }

                            } catch (\DomainException $e) {
                                $fail($e->getMessage());
                            }
                        },
                    ]),

                // MODIFICACIÓN: Nuevo componente Placeholder para mostrar la disponibilidad diaria del grupo
                \Filament\Forms\Components\Placeholder::make('disponibilidad_grupo')
                    ->label('Disponibilidad del Grupo')
                    // Solo se muestra si ya se escogieron empleado, desde y hasta
                    ->visible(fn ($get) => filled($get('desde')) && filled($get('hasta')) && filled($get('empleado_id')))
                    ->content(function ($get, ?\App\Models\Permiso $record) {
                        $desde = $get('desde');
                        $hasta = $get('hasta');
                        $empleadoId = $get('empleado_id');

                        // Si falta algún dato vital, mostramos un mensaje de guía
                        if (! $empleadoId || ! $desde || ! $hasta) {
                            return 'Seleccione empleado y fechas para consultar disponibilidad.';
                        }

                        // Obtenemos el empleado a partir del ID seleccionado en el formulario
                        $empleado = \App\Models\Empleado::find($empleadoId);
                        if (! $empleado) {
                            return 'Empleado no válido.';
                        }

                        // Llamamos a nuestro servicio central para calcular ocupaciones diarias
                        $service = app(\App\Services\PermisoService::class);
                        $disponibilidad = $service->obtenerDisponibilidadDiaria($empleado, $desde, $hasta, $record);

                        // Protegemos contra rangos de fechas inmensos
                        if (isset($disponibilidad['error'])) {
                            return new \Illuminate\Support\HtmlString("<span class='text-danger-600 font-bold'>{$disponibilidad['error']}</span>");
                        }

                        // Si no hay límites configurados en base de datos
                        if (empty($disponibilidad)) {
                            return 'El grupo del empleado no tiene configurado un límite diario.';
                        }

                        // Renderizamos HTML amigable al usuario indicando el límite por día y su estado
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
                Select::make('id_estado_vb')
                    ->label('Estado de Vo.Bo.')
                    ->relationship(
                        name: 'estadoVB',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                    )
                    ->required(),
                Select::make('id_jefe_vb')
                    ->label('Jefe Vo.Bo.')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getSearchResultsUsing(function (string $search) {
                        return Empleado::query()
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('oni', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($e) => [
                                $e->id => "{$e->oni} - {$e->nombre}",
                            ]);
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $empleado = Empleado::find($value);

                        return $empleado
                            ? "{$empleado->oni} - {$empleado->nombre}"
                            : '';
                    }),

                Select::make('id_estado_aprobacion')
                    ->label('Estado de Aprobación')
                    ->relationship(
                        name: 'estadoAprobado',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2))
                    ->required(),
                Select::make('id_jefe_aprobacion')
                    ->label('Jefe Aprobador')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getSearchResultsUsing(function (string $search) {
                        return Empleado::query()
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('oni', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($e) => [
                                $e->id => "{$e->oni} - {$e->nombre}",
                            ]);
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $empleado = Empleado::find($value);

                        return $empleado
                            ? "{$empleado->oni} - {$empleado->nombre}"
                            : '';
                    }),
                TextInput::make('comentarios')
                    ->label('Comentarios')
                    ->maxLength(500)
                    ->nullable(),

                // Subir archivo adjunto usando FileUpload de Filament y rutas
                FileUpload::make('adjunto')
                    ->label('Adjunto')
                    ->disk('public')
                    ->directory('permisos')
                    // ->downloadable()
                    ->preserveFilenames()
                    ->maxSize(10240)
                    ->nullable(),
                Placeholder::make('descarga')
                    ->label('Archivo adjunto')
                    ->icon('heroicon-o-paper-clip')
                    ->visible(fn ($record) => filled($record?->adjunto))
                    ->content(fn ($record) => new HtmlString(
                        '<a href="'.
                        route('descargar.archivo', $record->adjunto).
                        '" class="text-primary-600 underline" target="_blank">
                                    DESCARGAR ARCHIVO ADJUNTO
                                </a>'
                    )),

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

                            $ignorarValidaciones = $get('ignorar_validaciones');

                            // 2. Comprobamos si es necesario validar:
                            // MODIFICACIÓN: Ahora también verificamos si el administrador pidió ignorar las validaciones para saltarnos este paso
                            if ($ignorarValidaciones || $get('tipo_permiso_id') != 2 || ! $desdePrincipal || ! $hastaPrincipal || ! is_array($value) || empty($value)) {
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
