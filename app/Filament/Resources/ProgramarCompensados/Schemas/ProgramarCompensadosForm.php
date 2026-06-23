<?php

namespace App\Filament\Resources\ProgramarCompensados\Schemas;

use App\Models\Empleado;
use App\Models\Permiso;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ProgramarCompensadosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sección de Información y Disponibilidad en tiempo real
                Section::make('Información y Disponibilidad')
                    ->schema([
                        // Muestra horas personales del empleado seleccionado
                        Placeholder::make('Horas_personales')
                            ->reactive()
                            ->content(function ($get, ?Permiso $record) {
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

                        // Resumen de permisos aprobados del año en curso
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
                                    ->where('id_estado_aprobacion', 3) // Solo permisos Aprobados
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

                        // Resumen interactivo de disponibilidad diaria (cupos ocupados de Telefonistas)
                        Placeholder::make('disponibilidad_telefonistas')
                            ->label('Disponibilidad de Telefonistas')
                            ->reactive()
                            ->visible(fn($get) => filled($get('desde')) && filled($get('hasta')) && filled($get('empleado_id')))
                            ->content(function ($get, ?Permiso $record) {
                                $desde = $get('desde');
                                $hasta = $get('hasta');
                                $empleadoId = $get('empleado_id');

                                if (! $empleadoId || ! $desde || ! $hasta) {
                                    return 'Seleccione empleado y rango de fechas.';
                                }

                                $empleado = Empleado::find($empleadoId);
                                if (! $empleado || $empleado->categoria_id != 24) {
                                    return 'El empleado seleccionado no es telefonista.';
                                }

                                $fechaDesde = Carbon::parse($desde)->startOfDay();
                                $fechaHasta = Carbon::parse($hasta)->startOfDay();

                                if ($fechaHasta->lessThan($fechaDesde)) {
                                    return 'Rango de fechas inválido.';
                                }

                                $periodo = \Carbon\CarbonPeriod::create($fechaDesde, $fechaHasta);
                                if ($periodo->count() > 31) {
                                    return 'El rango de fechas es muy amplio.';
                                }

                                $grupo = $empleado->grupo;
                                $limite = $grupo ? $grupo->permisos_diarios : 2;
                                $grupoNombre = $grupo ? $grupo->nombre : 'Sin Grupo';

                                $html = '<ul class="list-disc list-inside space-y-1">';
                                foreach ($periodo as $fecha) {
                                    $empleadosConPermiso = Permiso::query()
                                        ->whereHas('empleado', function ($query) use ($grupo) {
                                            $query->where('categoria_id', 24)
                                                  ->when($grupo, function ($q) use ($grupo) {
                                                      $q->where('grupo_id', $grupo->id);
                                                  });
                                        })
                                        ->whereDate('desde', '<=', $fecha)
                                        ->whereDate('hasta', '>=', $fecha)
                                        ->when($record, function ($query) use ($record) {
                                            $query->where('id', '!=', $record->id);
                                        })
                                        ->where('id_estado_aprobacion', '!=', 5) // Excluir Rechazados
                                        ->pluck('empleado_id')
                                        ->unique()
                                        ->values()
                                        ->toArray();

                                    $totalEmpleados = count($empleadosConPermiso);
                                    if (!in_array($empleado->id, $empleadosConPermiso)) {
                                        $totalEmpleados += 1;
                                    }

                                    $disponible = $totalEmpleados <= $limite;
                                    $color = $disponible ? 'text-success-600 font-medium' : 'text-danger-600 font-bold';
                                    $estado = $disponible ? '(🟢 Disponible)' : '(🔴 Lleno)';
                                    $html .= "<li class='{$color}'>{$fecha->format('d/m/Y')}: " . count($empleadosConPermiso) . " de {$limite} cupos ocupados (Grupo: {$grupoNombre}) {$estado}</li>";
                                }
                                $html .= '</ul>';

                                return new HtmlString($html);
                            }),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn($get) => filled($get('empleado_id'))),

                // Sección para ingresar los datos principales del permiso
                Section::make('Datos del Permiso')
                    ->schema([
                        Image::make(
                            url: fn($get) => route('foto.empleado', [
                                'filename' => optional(Empleado::find($get('empleado_id')))->foto ?? 'dummy.jpg',
                            ]),
                            alt: 'Foto del empleado',
                        )
                            ->visible(fn($get) => filled(optional(Empleado::find($get('empleado_id')))->foto))
                            ->columnSpanFull(),

                        DatePicker::make('fecha_creacion')
                            ->label('Fecha de Creación')
                            ->default(Carbon::now())
                            ->readonly(),

                        // Buscador limitado exclusivamente a empleados Telefonistas (Categoría ID = 24)
                        Select::make('empleado_id')
                            ->label('Empleado (Telefonista)')
                            ->reactive()
                            ->searchable()
                            ->required()
                            ->getSearchResultsUsing(function (string $search) {
                                return Empleado::query()
                                    ->where('categoria_id', 24)
                                    ->where(function ($q) use ($search) {
                                        $q->where('nombre', 'like', "%{$search}%")
                                            ->orWhere('oni', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($e) => [
                                        $e->id => "{$e->oni} - {$e->nombre}",
                                    ]);
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $empleado = Empleado::find($value);
                                return $empleado ? "{$empleado->oni} - {$empleado->nombre}" : '';
                            }),

                        Placeholder::make('tipo_permiso_display')
                            ->label('Tipo de Permiso')
                            ->content('POR TIEMPO COMPENSATORIO'),

                        Hidden::make('tipo_permiso_id')
                            ->default(2),

                        DateTimePicker::make('desde')
                            ->label('Desde')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->withoutSeconds()
                            ->required()
                            ->live(),

                        DateTimePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->withoutSeconds()
                            ->required()
                            ->live()
                            ->rules([
                                fn($get, ?Permiso $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                    $desde = $get('desde');
                                    $empleadoId = $get('empleado_id');

                                    if (! $desde || ! $value || ! $empleadoId) {
                                        return;
                                    }

                                    $empleado = Empleado::find($empleadoId);
                                    if (! $empleado) {
                                        return;
                                    }

                                    $service = app(\App\Services\PermisoService::class);

                                    try {
                                        // 1. Validación de no-destiempo: El permiso no puede iniciar en el pasado al crearse
                                        if (! $record && Carbon::parse($desde)->isBefore(now()->startOfMinute())) {
                                            $fail('No se permite programar permisos a destiempo (fechas o horas de inicio en el pasado).');
                                            return;
                                        }

                                        // 2. Validar rango coherente (desde < hasta)
                                        if (! $service->validarRangoFechas($desde, $value)) {
                                            $fail('La fecha "hasta" debe ser mayor que "desde".');
                                            return;
                                        }

                                        // 3. Validar que no se registren horas de inicio y fin en 00:00
                                        if (! $service->validarHorasNoCero($desde, $value)) {
                                            $fail('No se permite registrar permisos con la hora de inicio (desde) y hora de fin (hasta) en 00:00.');
                                            return;
                                        }

                                        // 4. Validar traslape/choque de horarios para el mismo empleado
                                        $service->validarNoTraslapeHoras($empleado, $desde, $value, $record);

                                        // 5. Validar límite estricto de cupos diarios específicos de telefonista (máximo 2 por día)
                                        $service->validarLimiteTelefonistas($empleado, $desde, $value, $record);
                                    } catch (\DomainException $e) {
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
                    ])
                    ->columns(2)
                    ->columnSpanFull(),



                // Listado para ingresar los periodos de tiempo compensado ya realizados
                Section::make('Periodos Compensados a Utilizar')
                    ->schema([
                        Repeater::make('compensados')
                            ->relationship()
                            ->label('Periodos')
                            ->schema([
                                DateTimePicker::make('desde')
                                    ->label('Desde')
                                    ->displayFormat('d/m/Y H:i')
                                    ->format('Y-m-d H:i')
                                    ->withoutSeconds()
                                    ->native(false)
                                    ->required(),
                                DateTimePicker::make('hasta')
                                    ->label('Hasta')
                                    ->displayFormat('d/m/Y H:i')
                                    ->format('Y-m-d H:i')
                                    ->withoutSeconds()
                                    ->native(false)
                                    ->required(),
                                Textarea::make('justificacion')
                                    ->label('Justificación')
                                    ->required()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                FileUpload::make('adjunto')
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
                            ->rules([
                                fn($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $desdePrincipal = $get('desde');
                                    $hastaPrincipal = $get('hasta');

                                    if (! $desdePrincipal || ! $hastaPrincipal || ! is_array($value) || empty($value)) {
                                        return;
                                    }

                                    $minutosPrincipales = Carbon::parse($desdePrincipal)->diffInMinutes(Carbon::parse($hastaPrincipal));
                                    $minutosCompensados = 0;

                                    $desdePrin = Carbon::parse($desdePrincipal);
                                    $hastaPrin = Carbon::parse($hastaPrincipal);

                                    foreach ($value as $item) {
                                        if (isset($item['desde']) && isset($item['hasta'])) {
                                            $desdeComp = Carbon::parse($item['desde']);
                                            $hastaComp = Carbon::parse($item['hasta']);

                                            // 1. Validar que las fechas del compensado no se traslapen ni coincidan con el permiso principal
                                            if (!($desdeComp->greaterThanOrEqualTo($hastaPrin) || $hastaComp->lessThanOrEqualTo($desdePrin))) {
                                                $fail("El periodo compensado ({$desdeComp->format('d/m/Y H:i')} - {$hastaComp->format('d/m/Y H:i')}) no puede coincidir ni traslaparse con el horario del permiso solicitado.");
                                                return;
                                            }

                                            // 2. Validar que la fecha del compensado no sea futura (debe ser un trabajo ya realizado)
                                            if ($hastaComp->isFuture()) {
                                                $fail("El periodo compensado ({$desdeComp->format('d/m/Y H:i')} - {$hastaComp->format('d/m/Y H:i')}) no puede estar en el futuro. Debe ser un trabajo ya realizado.");
                                                return;
                                            }

                                            // 3. Validar que el periodo compensado haya finalizado antes de la hora de inicio del permiso
                                            if ($hastaComp->isAfter($desdePrin)) {
                                                $fail("El periodo compensado ({$desdeComp->format('d/m/Y H:i')} - {$hastaComp->format('d/m/Y H:i')}) no puede ser posterior a la fecha de inicio del permiso solicitado.");
                                                return;
                                            }

                                            $minutosCompensados += $desdeComp->diffInMinutes($hastaComp);
                                        }
                                    }

                                    // 4. Validar que la suma total coincida exactamente
                                    if ($minutosPrincipales !== $minutosCompensados) {
                                        $horasP = round($minutosPrincipales / 60, 2);
                                        $horasC = round($minutosCompensados / 60, 2);
                                        $fail("La suma de horas de los periodos compensados ({$horasC} hrs) debe ser exactamente igual a las horas solicitadas en el permiso ({$horasP} hrs).");
                                    }

                                    // 5. Validar antigüedad máxima de 6 meses
                                    $service = app(\App\Services\PermisoService::class);
                                    try {
                                        $service->validarAntiguedadCompensados($value);
                                    } catch (\DomainException $e) {
                                        $fail($e->getMessage());
                                    }
                                },
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
