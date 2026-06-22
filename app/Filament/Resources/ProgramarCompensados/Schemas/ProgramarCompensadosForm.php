<?php

namespace App\Filament\Resources\ProgramarCompensados\Schemas;

use App\Models\Empleado;
use App\Models\Permiso;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ProgramarCompensadosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sección superior informativa del empleado y disponibilidad diaria en tiempo real
                Section::make('Información y Disponibilidad')
                    ->schema([
                        Placeholder::make('disponibilidad_telefonistas')
                            ->label('Disponibilidad de Telefonistas')
                            ->reactive()
                            // Se muestra solo si el usuario seleccionó un empleado y las fechas desde/hasta
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
                                // Evita consultas inmensas en caso de fechas erróneas
                                if ($periodo->count() > 31) {
                                    return 'El rango de fechas es muy amplio.';
                                }

                                $html = '<ul class="list-disc list-inside space-y-1">';
                                foreach ($periodo as $fecha) {
                                    // Contamos cuántos empleados distintos con categoría 24 (Telefonista) ya tienen permisos ese día
                                    $empleadosConPermiso = Permiso::query()
                                        ->whereHas('empleado', function ($query) {
                                            $query->where('categoria_id', 24);
                                        })
                                        ->whereDate('desde', '<=', $fecha)
                                        ->whereDate('hasta', '>=', $fecha)
                                        ->when($record, function ($query) use ($record) {
                                            $query->where('id', '!=', $record->id);
                                        })
                                        ->where('id_estado_aprobacion', '!=', 5) // Excluimos los rechazados
                                        ->pluck('empleado_id')
                                        ->unique()
                                        ->values()
                                        ->toArray();

                                    $totalEmpleados = count($empleadosConPermiso);

                                    // Si el empleado actual no está en la lista de los que ya tienen permiso hoy, sumamos 1 cupo
                                    if (!in_array($empleado->id, $empleadosConPermiso)) {
                                        $totalEmpleados += 1;
                                    }

                                    // Comprobamos disponibilidad contra el límite específico de 2 telefonistas por día
                                    $disponible = $totalEmpleados <= 2;
                                    $color = $disponible ? 'text-success-600 font-medium' : 'text-danger-600 font-bold';
                                    $estado = $disponible ? '(🟢 Disponible)' : '(🔴 Lleno)';
                                    $html .= "<li class='{$color}'>{$fecha->format('d/m/Y')}: " . count($empleadosConPermiso) . " de 2 cupos ocupados {$estado}</li>";
                                }
                                $html .= '</ul>';

                                return new HtmlString($html);
                            }),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),

                // Sección para ingresar los datos principales del permiso
                Section::make('Datos del Permiso')
                    ->schema([
                        // Selector de empleado limitado únicamente a Telefonistas (Categoría 24)
                        Select::make('empleado_id')
                            ->label('Empleado (Telefonista)')
                            ->reactive()
                            ->searchable()
                            ->required()
                            ->getSearchResultsUsing(function (string $search) {
                                return Empleado::query()
                                    ->where('categoria_id', 24) // Restringe la búsqueda a telefonistas
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

                        // Campo estático de solo lectura que indica que el permiso es de tipo compensatorio
                        Placeholder::make('tipo_permiso_display')
                            ->label('Tipo de Permiso')
                            ->content('POR TIEMPO COMPENSATORIO'),

                        // Campo oculto para registrar automáticamente el tipo_permiso_id = 2 en la base de datos
                        Hidden::make('tipo_permiso_id')
                            ->default(2),

                        // Fecha/Hora de inicio
                        DateTimePicker::make('desde')
                            ->label('Desde')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->withoutSeconds()
                            ->required()
                            ->live(),

                        // Fecha/Hora de fin (con todas las validaciones de negocio correspondientes)
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
                                        // 1. Validar rango coherente (desde < hasta)
                                        if (! $service->validarRangoFechas($desde, $value)) {
                                            $fail('La fecha "hasta" debe ser mayor que "desde".');
                                            return;
                                        }

                                        // 2. Validar que no se registren horas de inicio y fin en 00:00
                                        if (! $service->validarHorasNoCero($desde, $value)) {
                                            $fail('No se permite registrar permisos con la hora de inicio (desde) y hora de fin (hasta) en 00:00.');
                                            return;
                                        }

                                        // 3. Validar traslape/choque de horarios para el mismo empleado (zona horaria compatible con offset)
                                        $service->validarNoTraslapeHoras($empleado, $desde, $value, $record);

                                        // 4. Validar límite estricto de cupos diarios específicos de telefonista (máximo 2 por día)
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

                // Sección para ingresar los periodos de tiempo acumulado que se van a compensar
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
                                TextInput::make('justificacion')
                                    ->label('Justificación')
                                    ->required()
                                    ->maxLength(65535),
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

                                    // Calcula la duración en minutos del permiso solicitado
                                    $minutosPrincipales = Carbon::parse($desdePrincipal)->diffInMinutes(Carbon::parse($hastaPrincipal));

                                    // Suma de la duración de cada periodo compensado ingresado en la lista
                                    $minutosCompensados = 0;
                                    foreach ($value as $item) {
                                        if (isset($item['desde']) && isset($item['hasta'])) {
                                            $minutosCompensados += Carbon::parse($item['desde'])->diffInMinutes(Carbon::parse($item['hasta']));
                                        }
                                    }

                                    // Exige coincidencia exacta entre la duración solicitada y el tiempo compensado justificado
                                    if ($minutosPrincipales !== $minutosCompensados) {
                                        $horasP = round($minutosPrincipales / 60, 2);
                                        $horasC = round($minutosCompensados / 60, 2);
                                        $fail("La suma de horas de los periodos compensados ({$horasC} hrs) debe ser exactamente igual a las horas solicitadas en el permiso ({$horasP} hrs).");
                                    }

                                    // Valida antigüedad de periodos compensados (no mayor a 6 meses)
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
