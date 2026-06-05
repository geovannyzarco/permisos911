<?php

namespace App\Filament\Resources\DelegarAprobacions\Schemas;

use App\Models\Empleado;
use App\Models\Grupo;
use App\Models\Unidad;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DelegarAprobacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jefe_id')
                    ->label('Jefe / Supervisor')
                    ->options(
                        Empleado::query()
                            ->whereIn('nivel_id', [2, 3, 4]) // Solo jefes/supervisores
                            ->pluck('nombre', 'id')
                    )
                    ->searchable()
                    ->required(),

                Select::make('tipo_delegacion')
                    ->label('Tipo de Delegación')
                    ->options([
                        'grupo' => 'Grupo de Trabajo',
                        'unidad' => 'Unidad / Departamento',
                    ])
                    ->live()
                    ->required(),

                Select::make('entidad_delegada_id')
                    ->label('Entidad Delegada')
                    ->options(function (callable $get) {
                        $tipo = $get('tipo_delegacion');

                        if ($tipo === 'grupo') {
                            return Grupo::pluck('nombre', 'id');
                        }

                        if ($tipo === 'unidad') {
                            return Unidad::pluck('nombre', 'id');
                        }

                        return [];
                    })
                    ->disabled(fn (callable $get) => empty($get('tipo_delegacion')))
                    ->live()
                    ->required(),

                DatePicker::make('fecha_desde')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->nullable(),

                DatePicker::make('fecha_hasta')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->nullable(),

                Toggle::make('activo')
                    ->label('Activa')
                    ->default(true)
                    ->required(),
            ]);
    }
}
