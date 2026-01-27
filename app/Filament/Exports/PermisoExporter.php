<?php

namespace App\Filament\Exports;

use App\Models\Permiso;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;
use Filament\Actions\Exports\Enums\ExportFormat;
use Illuminate\Database\Eloquent\Builder;

class PermisoExporter extends Exporter
{
    protected static ?string $model = Permiso::class;
    public static function modifyQuery(Builder $query): Builder
        {
            return $query->with([
                'empleado',
                'tipoPermiso',
                'estadoVB',
                'estadoAprobado',
            ]);
        }


    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
           ExportColumn::make('fecha_creacion')->label('Fecha creación'),
            ExportColumn::make('empleado.id')->label('ID Empleado'),
            ExportColumn::make('empleado.oni')->label('ONI'),
            ExportColumn::make('empleado.nombre')->label('Empleado'),
            ExportColumn::make('tipoPermiso.nombre')->label('Tipo permiso'),
            ExportColumn::make('desde')->label('Desde'),
            ExportColumn::make('hasta')->label('Hasta'),
            ExportColumn::make('motivo')->label('Motivo'),
            ExportColumn::make('adjunto')->label('Adjunto'),
            ExportColumn::make('comentarios')->label('Comentarios'),
            ExportColumn::make('duracion')->label('Duración'),
            ExportColumn::make('estadoVB.nombre')->label('Estado VB'),
            ExportColumn::make('fecha_vb')->label('Fecha VB'),
            ExportColumn::make('id_jefe_vb')->label('ID Jefe VB'),
            ExportColumn::make('estadoAprobacion.nombre')->label('Estado aprobación'),
            ExportColumn::make('fecha_aprobacion')->label('Fecha aprobación'),
            ExportColumn::make('id_jefe_aprobacion')->label('ID Jefe Aprobación'),
            ExportColumn::make('observaciones')->label('Observaciones'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your permiso export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
