<?php

namespace App\Filament\Resources\AprobacionPermisos;

use App\Filament\Resources\AprobacionPermisos\Pages\CreateAprobacionPermiso;
use App\Filament\Resources\AprobacionPermisos\Pages\EditAprobacionPermiso;
use App\Filament\Resources\AprobacionPermisos\Pages\ListAprobacionPermisos;
use App\Filament\Resources\AprobacionPermisos\Pages\ViewAprobacionPermiso;
use App\Filament\Resources\AprobacionPermisos\Schemas\AprobacionPermisoForm;
use App\Filament\Resources\AprobacionPermisos\Schemas\AprobacionPermisoInfolist;
use App\Filament\Resources\AprobacionPermisos\Tables\AprobacionPermisosTable;
use App\Models\Permiso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AprobacionPermisoResource extends Resource
{
    protected static ?string $model = Permiso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Aprobaciones';

    protected static ?string $navigationLabel = 'Aprobar Permisos';

    protected static ?string $pluralModelLabel = 'Aprobar Permisos';

    protected static ?string $recordTitleAttribute = 'Aprobar Permisos';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $empleado = auth()->user()->empleado;
        $query = parent::getEloquentQuery();
        // JEFE DE GRUPO
        if ($empleado->nivel_id == 2) {
            $query->whereNull('id_estado_vb')
                ->whereHas('empleado', function ($q) use ($empleado) {
                    $q->where('grupo_id', $empleado->grupo_id);
                });
        }
        // JEFE DE UNIDAD
        if ($empleado->nivel_id == 3) {
            $query->whereNotNull('id_estado_vb')
                ->whereNull('id_estado_aprobacion')
                ->whereHas('empleado', function ($q) use ($empleado) {
                    $q->where('unidad_id', $empleado->unidad_id);
                });
        }

        // JEFE DE DIVISION
        if ($empleado->nivel_id == 4) {
            $query->whereNull('id_estado_aprobacion')
                ->whereHas('empleado', function ($q) use ($empleado) {
                    $q->where('unidad_id', $empleado->unidad_id);
                });
        }

        return $query;

    }

    public static function getNavigationBadge(): ?string
    {
        $empleado = auth()->user()->empleado;

        $query = static::getModel()::query();

        // JEFE DE GRUPO
        if ($empleado->nivel_id == 2) {

            $query->whereNull('id_estado_vb')
                ->whereHas('empleado', function ($q) use ($empleado) {
                    $q->where('grupo_id', $empleado->grupo_id);
                });
        }

        // JEFE DE UNIDAD
        elseif ($empleado->nivel_id == 3) {

            $query->whereNotNull('id_estado_vb')
                ->whereNull('id_estado_aprobacion')
                ->whereHas('empleado', function ($q) use ($empleado) {
                    $q->where('unidad_id', $empleado->unidad_id);
                });
        } else {
            return null; // empleados normales no ven badge
        }

        return (string) $query->count();
    }

    public static function form(Schema $schema): Schema
    {
        return AprobacionPermisoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AprobacionPermisoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AprobacionPermisosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAprobacionPermisos::route('/'),
            //  'create' => CreateAprobacionPermiso::route('/create'),
            'view' => ViewAprobacionPermiso::route('/{record}'),
            'edit' => EditAprobacionPermiso::route('/{record}/edit'),
        ];
    }
}
