<?php

namespace App\Filament\Resources\GestionPermisos\Tables;

use App\Models\Empleado;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class GestionPermisosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100, 'all'])

            /* =======================
             * COLUMNS
             * ======================= */
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('fecha_creacion')
                    ->label('Fecha de Creación')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tipoPermiso.nombre')
                    ->label('Tipo de Permiso')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('empleado.oni')
                    ->label('ONI')
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('empleado.foto')
                    ->circular()
                    ->label('Foto'),

                TextColumn::make('empleado.nombre')
                    ->label('Empleado')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('desde')
                    ->label('Desde')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('hasta')
                    ->label('Hasta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('duracion')
                    ->label('Duración')
                    ->sortable(),

                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->limit(50)
                    ->sortable(),

                TextColumn::make('estadoVB.nombre')
                    ->label('VB')
                    ->sortable(),

                TextColumn::make('fecha_vb')
                    ->label('Fecha VB')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('jefeVB.oni')
                    ->label('Jefe VB')
                    ->sortable(),

                TextColumn::make('estadoAprobado.nombre')
                    ->label('Aprobación')
                    ->sortable(),

                TextColumn::make('fecha_aprobacion')
                    ->label('Fecha Aprobación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('jefeAprobacion.oni')
                    ->label('Jefe Aprobación')
                    ->sortable(),
                TextColumn::make('adjunto')
                    ->label('Adjunto')
                    ->icon('heroicon-o-paper-clip')
                    ->formatStateUsing(fn ($state) => filled($state) ? 'Descargar' : '')
                    ->url(fn ($record) => $record?->adjunto
                        ? route('descargar.archivo', $record->adjunto)
                        : null
                    )
                    ->openUrlInNewTab(),
            ])

            /* =======================
             * FILTERS
             * ======================= */
            ->filters([
                SelectFilter::make('tipo_permiso_id')
                    ->label('Tipo de Permiso')
                    ->relationship('tipoPermiso', 'nombre'),

                SelectFilter::make('estado_vb')
                    ->label('VB')
                    ->relationship('estadoVB', 'nombre'),

                SelectFilter::make('estado_aprobacion')
                    ->label('Aprobación')
                    ->relationship('estadoAprobado', 'nombre'),
                    
            ])

            /* =======================
             * ROW ACTIONS
             * ======================= */
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('pdf')
                    ->label('Hoja de Permiso')
                    ->url(fn ($record) => route('permiso.pdf', ['id' => $record->id]))
                    ->openUrlInNewTab(),
            ])

            /* =======================
             * BULK ACTIONS
             * ======================= */
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),

                    /* ----- Cambiar Vo.Bo. ----- */
                    BulkAction::make('cambiar_estado_vb')
                        ->label('Cambiar Vo.Bo.')
                        ->icon('heroicon-o-check-circle')
                        ->form([
                            Select::make('estado_vb')
                                ->label('Estado Vo.Bo.')
                                ->relationship(
                                    name: 'estadoVB',
                                    titleAttribute: 'nombre',
                                    modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                                )
                                ->required(),

                            Select::make('jefe_vb')
                                ->label('Jefe Vo.Bo.')
                                ->relationship('jefeVB', 'nombre') // ajusta el nombre del campo visible
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
                                        ? "{$empleado->oni} - {$empleado->nombre} "
                                        : '';
                                }),
                        ])

                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'id_estado_vb' => $data['estado_vb'],
                                    'fecha_vb' => now(),
                                    'id_jefe_vb' => $data['jefe_vb'],
                                ]);
                            }
                        }),

                    /* ----- Cambiar Aprobación ----- */
                    BulkAction::make('cambiar_estado_aprobacion')
                        ->label('Cambiar Aprobación')
                        ->icon('heroicon-o-shield-check')
                        ->form([
                            Select::make('estado_aprobacion')
                                ->label('Estado de Aprobación')
                                ->relationship(
                                    name: 'estadoAprobado',
                                    titleAttribute: 'nombre',
                                    modifyQueryUsing: fn ($query) => $query->where('entidad_id', 2)
                                )
                                ->required(),
                            Select::make('jefe_aprobacion')
                                ->label('Jefe Aprobador')
                                ->relationship('jefeAprobacion', 'nombre')
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
                                        ? "{$empleado->oni} - {$empleado->nombre} "
                                        : '';
                                }),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'id_estado_aprobacion' => $data['estado_aprobacion'],
                                    'fecha_aprobacion' => now(),
                                    'id_jefe_aprobacion' => $data['jefe_aprobacion'],
                                ]);
                            }
                        }),
                ]),
            ]);
    }
}
