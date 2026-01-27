<?php

namespace App\Filament\Resources\GestionPermisos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use App\Filament\Exports\PermisoExporter;
use Dom\Text;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;

class GestionPermisosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('fecha_creacion')->label('Fecha de Creación')->date('d/m/Y')->sortable(),
                TextColumn::make('tipoPermiso.nombre')->label('Tipo de Permiso')->sortable()->searchable(),
                TextColumn::make('empleado.oni')->label('ONI')->sortable()->searchable(),
                TextColumn::make('empleado.nombre')->label('Empleado')->sortable()->searchable(),
                TextColumn::make('desde')->label('Desde')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('hasta')->label('Hasta')->dateTime('d/m/y H:i')->sortable(),
                TextColumn::make('duracion')
                    ->label('Duración')
                    ->sortable(),
                TextColumn::make('motivo')->label('Motivo')->limit(50)->sortable(),
                TextColumn::make('estado.nombre')->label('VB')->sortable(),
                TextColumn::make('fecha_aprobacion')->label('Fecha VB')->dateTime('d/m/Y h:m')->sortable(),
                TextColumn::make('estadoUnidad.nombre')->label('Aprobación')->sortable(),
                TextColumn::make('fecha_aprobacion_unidad')->label('Fecha Aprobación')->dateTime('d/m/Y h:m')->sortable(),
                TextColumn::make('adjunto')
                    ->label('Adjuntos')
                    ->icon('heroicon-o-paper-clip')
                     ->formatStateUsing(fn ($state) => filled($state) ? 'Descargar' : '')
                    ->url(fn ($record) => $record?->adjunto
                        ? route('descargar.archivo', $record->adjunto)
                        : null
                    )
                    ->openUrlInNewTab()

                            ])
            ->filters([
                SelectFilter::make('tipo_permiso_id')
                    ->label('Filtrar por Tipo de Permiso')
                    ->relationship('tipoPermiso', 'nombre'),
                SelectFilter::make('estado_id')
                    ->label('Filtrar por VB')
                    ->relationship('estadoVB', 'nombre'),
                SelectFilter::make('estado_unidad_id')
                    ->label('Filtrar por Aprobación')
                    ->relationship('estadoAprobado', 'nombre'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('pdf')
                ->label('Hoja de Permiso')
                ->url(fn ($record) => route('permiso.pdf', ['id' => $record->id]))
                ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(PermisoExporter::class)

                    ->label('Exportar')
                    ->fileName('permisos_export'.date('Y-m-d_H-i-s')),

                BulkActionGroup::make([
                    /*ExportBulkAction::make()
                        ->exporter(PermisoExporter::class)
                        ->label('Exportar Selección')
                        ->fileName('permisos_seleccion_export'.date('Y-m-d_H-i-s')),*/

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
