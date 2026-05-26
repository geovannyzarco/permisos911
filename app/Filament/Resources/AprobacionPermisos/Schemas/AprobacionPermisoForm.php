<?php

namespace App\Filament\Resources\AprobacionPermisos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Models\Estado;


class AprobacionPermisoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informacion del empleado')
                    ->schema([
                        Placeholder::make('foto')
                            ->label('Foto')
                            ->content(function ($record) {

                                $filename = optional($record->empleado)->foto ?? 'dummy.jpg';

                                $url = route('foto.empleado', [
                                    'filename' => $filename
                                ]);

                                return new HtmlString("
                                <img src='{$url}'
                                    alt='Foto del empleado'
                                    style='width:100px;height:100px;border-radius:50%;object-fit:cover;' />
                            ");
                            }),

                        Placeholder::make('nombre')
                            ->label('Nombre')
                            ->content(fn($record) => optional($record->empleado)->nombre ?? 'N/A'),
                        Placeholder::make('empleado.oni')
                            ->label('ONI'),
                        Placeholder::make('unidad')
                            ->label('Unidad')
                            ->content(fn($record) => optional($record->empleado->unidad)->nombre ?? 'N/A'),
                        Placeholder::make('grupo')
                            ->label('Grupo')
                            ->content(fn($record) => optional($record->empleado->grupo)->nombre ?? 'N/A'),
                        Placeholder::make('horario')
                            ->label('Horario')
                            ->content(fn($record) => optional($record->empleado->horario)->nombre ?? 'N/A'),
                    ])->columns(3)
                    ->columnSpanFull(),

                Section::make('Información del Permiso')
                    ->schema([


                        Placeholder::make('tipoPermiso.nombre')
                            ->label('Tipo de Permiso'),
                        Placeholder::make('desde')
                            ->label('Desde'),
                        Placeholder::make('hasta')
                            ->label('Hasta'),
                        Placeholder::make('duracion')
                            ->label('Duración'),
                        Placeholder::make('motivo')
                            ->label('Motivo'),
                        Placeholder::make('Adjunto')
                            ->label('Anexo')
                            ->color('primary')
                            ->content(function ($record) {
                                if ($record->adjunto) {
                                    $url = asset('storage/' . $record->adjunto);
                                    return new HtmlString("<a href='{$url}' target='_blank'>Ver Anexo</a>");
                                }

                                return 'No hay adjunto';
                            }),

                    ])->columns(3)
                    ->columnSpanFull(),

                Section::make('Aprobaciones')
                    ->schema([

                        Select::make('id_estado_vb')
                            ->label('VISTO BUENO')
                            ->options(
                                Estado::where('entidad_id', 2)
                                    ->whereIn('id', [3, 5])
                                    ->pluck('nombre', 'id')
                            )
                            ->visible(fn() => auth()->user()->empleado?->nivel_id == 2)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $record) {
                                if ($state) {
                                    $record->update([
                                        'id_jefe_vb' => auth()->user()->empleado->id,
                                        'fecha_vb' => now(),
                                    ]);
                                }
                            }),

                        Select::make('id_estado_aprobacion')
                            ->label('APROBACIÓN JEFATURA')
                            ->options(
                                Estado::where('entidad_id', 2)
                                    ->whereIn('id', [3, 5])
                                    ->pluck('nombre', 'id')
                            )
                            ->visible(fn() => auth()->user()->empleado?->nivel_id == 3)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $record) {
                                if ($state) {
                                    $record->update([
                                        'id_jefe_aprobacion' => auth()->user()->empleado->id,
                                        'fecha_aprobacion' => now(),
                                    ]);
                                }
                            }),

                        Select::make('id_estado_aprobacion_jefe_division')
                            ->label('APROBACIÓN JEFE DIVISIÓN')
                            ->options(
                                Estado::where('entidad_id', 2)
                                    ->whereIn('id', [3, 5])
                                    ->pluck('nombre', 'id')
                            )
                            ->visible(fn() => auth()->user()->empleado?->nivel_id == 4)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $record) {
                                if ($state) {
                                    $record->update([
                                        'id_oni_jefe_division' => auth()->user()->empleado->oni,
                                        'fecha_aprobacion_jefe_division' => now(),
                                    ]);
                                }
                            }),

                        Textarea::make('comentarios')
                            ->label('COMENTARIOS')
                            ->visible(fn() => in_array(auth()->user()->empleado?->nivel_id, [2, 3])),

                    ])
                    ->columns(2)
                    ->columnSpanFull()
            ]);
    }
}
