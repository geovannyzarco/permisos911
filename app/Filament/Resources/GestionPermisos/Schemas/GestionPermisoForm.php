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
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use App\Models\Empleado;
use App\Models\TipoPermiso;
use App\Models\Permiso;





class GestionPermisoForm
{
    public static function configure(Schema $schema): Schema
    {
        //Mostrar de las horas personales y permisos del empleado seleccionado
        return $schema
            ->components([
                 //informacion de las horas personales
                        Section::make('Informacion del empleado')
                            ->schema([
                                Placeholder::make('Horas_personales')
                                    ->reactive()
                                    ->content(function ($get) {

                                        $empleadoId = $get('empleado_id');

                                        if (!$empleadoId) {
                                            return 'Seleccione un empleado para ver la información.';
                                        }

                                        $empleado = Empleado::find($empleadoId);

                                        if (!$empleado) {
                                            return 'Empleado no encontrado.';
                                        }

                                        $asignadas = $empleado->horario?->horas_personales ?? 0;

                                        $minutosUsados = $empleado->permisos()
                                            ->whereYear('desde', Carbon::now()->year)
                                            ->where('tipo_permiso_id', 1)
                                            ->where('id_estado_aprobacion', 3) // Solo permisos aprobados
                                            ->whereNotNull('desde')
                                            ->whereNotNull('hasta')
                                            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, `desde`, `hasta`)) as total')
                                            ->value('total') ?? 0;

                                        $usadas = round($minutosUsados / 60, 2);

                                        $disponibles = max($asignadas - $usadas, 0);


                                        return new HtmlString(
                                            "<strong>Horas asignadas:</strong> {$asignadas}<br>
                                            <strong>Horas utilizadas:</strong> {$usadas}<br>
                                            <strong>Horas disponibles:</strong> {$disponibles}"
                                        );
                                    }),
                                ])
                            ->columns(1)
                            ->visible(fn ($get) => filled($get('empleado_id'))),

                        //Resumen de la cantidad permisos del empleado en el año en curso
                        Section::make('Permisos del año en curso')
                            ->schema([
                                Placeholder::make('Permisos')
                                    ->reactive()
                                    ->content(function ($get) {
                                        $empleadoId = $get('empleado_id');

                                        if (!$empleadoId) {
                                            return 'Seleccione un empleado para ver la información.';
                                        }
                                        $anio = Carbon::now()->year;

                                        $empleado = Empleado::find($empleadoId);

                                        if (!$empleado) {
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

                        DatePicker::make('fecha_creacion')
                            ->label('Fecha de Creación')
                            ->default(Carbon::now())
                            ->readonly(),

                        Select::make('empleado_id')
                            ->label('Empleado')
                            ->reactive()
                            ->searchable()
                            ->required()
                            //Para buscar por nombre y oni en el select
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
                        Select::make('id_estado_vb')
                            ->label('Estado de Vo.Bo.')
                            ->relationship(
                                name: 'estadoVB',
                                titleAttribute: 'nombre',
                                modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                            )
                            ->required(),
                        Select::make('id_estado_aprobacion')
                            ->label('Estado de Aprobación')
                            ->relationship(
                                name: 'estadoAprobado',
                                titleAttribute: 'nombre',
                                modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2))
                            ->required(),
                        TextInput::make('comentarios')
                            ->label('Comentarios')
                            ->maxLength(500)
                            ->nullable(),

                        //Subir archivo adjunto usando FileUpload de Filament y rutas
                        FileUpload::make('adjunto')
                            ->label('Adjunto')
                            ->disk('public')
                            ->directory('permisos')
                            //->downloadable()
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->nullable(),
                        Placeholder::make('descarga')
                            ->label('Archivo adjunto')
                            ->icon('heroicon-o-paper-clip')
                            ->visible(fn ($record) => filled($record?->adjunto))
                            ->content(fn ($record) => new HtmlString(
                                '<a href="' .
                                route('descargar.archivo', $record->adjunto) .
                                '" class="text-primary-600 underline" target="_blank">
                                    DESCARGAR ARCHIVO ADJUNTO
                                </a>'
                            )),

            ]);
    }
}
