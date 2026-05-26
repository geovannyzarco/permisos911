<?php

namespace App\Filament\Resources\Permisos\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;

class PermisosTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->searchable(),
                TextColumn::make('fecha_creacion')->label('Fecha de Creación')->date('d/m/Y')->sortable(),
                TextColumn::make('tipoPermiso.nombre')->label('Tipo de Permiso')->sortable()->searchable(),
                TextColumn::make('empleado.oni')->label('ONI')->sortable()->searchable(),
                TextColumn::make('empleado.nombre')->label('Empleado')->sortable()->searchable(),
                TextColumn::make('desde')->label('Desde')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('hasta')->label('Hasta')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('duracion')->label('Duración'),
                TextColumn::make('motivo')->label('Motivo')->limit(50)->searchable(),
                TextColumn::make('adjunto')
                    ->label('Adjunto')
                    ->icon('heroicon-o-paper-clip')
                    ->formatStateUsing(fn($state) => filled($state) ? 'Descargar' : '')
                    ->url(
                        fn($record) => $record?->adjunto
                            ? route('descargar.archivo', $record->adjunto)
                            : null
                    )
                    ->openUrlInNewTab(),
                TextColumn::make('estadoVB.nombre')
                    ->label('VB')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->id_estado_vb == 3 ? 'success' : 'gray'),
                TextColumn::make('jefeVb.nombre')->label('JEFE VB')->sortable(),
                TextColumn::make('fecha_vb')->label('FECHA DE VB')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('estadoAprobado.nombre')
                    ->label('APROBACION JEFATURA')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->id_estado_aprobacion == 3 ? 'success' : 'gray'),

                TextColumn::make('fecha_aprobacion')->label('FECHA APROBACION JEFATURA')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('jefeAprobacion.nombre')->label('JEFE APROBACIÓN')->sortable(),
                TextColumn::make('estadoAprobacionJefeDivision.nombre')
                    ->label('APROBACION JEFE DIVISION')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->id_estado_aprobacion_jefe_division == 3 ? 'success' : 'gray'),
                TextColumn::make('fecha_aprobacion_jefe_division')->label('FECHA APROBACION JEFE DIVISION')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('id_oni_jefe_division')->label('JEFE DIVISION')->sortable(),
                TextColumn::make('comentarios')->label('COMENTARIOS')->limit(50)->sortable(),




            ])
            ->filters([

                // Filtro por rango de fechas en el campo desde
                Filter::make('desde')
                    ->label('Fecha Desde')
                    ->form([
                        DatePicker::make('desde_inicio')->label('Desde (Inicio)'),
                        DatePicker::make('desde_fin')->label('Desde (Fin)'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['desde_inicio']) {
                            $query->whereDate('desde', '>=', $data['desde_inicio']);
                        }
                        if ($data['desde_fin']) {
                            $query->whereDate('desde', '<=', $data['desde_fin']);
                        }
                    }),

                SelectFilter::make('id_estado_vb')
                    ->label('Visto Bueno')
                    ->relationship(
                        name: 'estadoVB',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('id_estado_aprobacion')
                    ->label('Aprobación')
                    ->relationship(
                        name: 'estadoAprobado',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                    ),

                SelectFilter::make('id_estado_aprobacion_jefe_division')
                    ->label('Estado Aprobación Jefe División')
                    ->relationship(
                        name: 'estadoAprobacionJefeDivision',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn($query) => $query->where('entidad_id', 2)
                    ),


            ])
            ->recordActions([
                ViewAction::make(),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
