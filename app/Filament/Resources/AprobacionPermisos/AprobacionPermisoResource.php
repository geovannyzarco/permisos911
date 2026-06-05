<?php

namespace App\Filament\Resources\AprobacionPermisos;

use App\Filament\Resources\AprobacionPermisos\Pages\CreateAprobacionPermiso;
use App\Filament\Resources\AprobacionPermisos\Pages\EditAprobacionPermiso;
use App\Filament\Resources\AprobacionPermisos\Pages\ListAprobacionPermisos;
use App\Filament\Resources\AprobacionPermisos\Pages\ViewAprobacionPermiso;
use App\Filament\Resources\AprobacionPermisos\Schemas\AprobacionPermisoForm;
use App\Filament\Resources\AprobacionPermisos\Schemas\AprobacionPermisoInfolist;
use App\Filament\Resources\AprobacionPermisos\Tables\AprobacionPermisosTable;
use App\Models\Empleado;
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

    public static function getPermissionPrefix(): string
    {
        return 'aprobacion_permiso';
    }

    public static function getModelLabel(): string
    {
        return 'Aprobacion de permisos';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Aprobacion de permisos';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:AprobacionPermisoResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:AprobacionPermisoResource')
            && self::canAccessRecord($record);
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:AprobacionPermisoResource')
            && self::canAccessRecord($record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:AprobacionPermisoResource')
            && self::canAccessRecord($record);
    }

    /**
     * Valida si el usuario autenticado tiene permisos para acceder a un registro específico.
     * Los jefes de grupo (nivel 2) solo acceden a permisos de su grupo o grupos delegados.
     * Los jefes de unidad (nivel 3) solo acceden a permisos de su unidad o unidades delegadas.
     */
    public static function canAccessRecord($record): bool
    {
        $emp = auth()->user()->empleado;
        if (!$emp) {
            return false;
        }

        // Nivel 2: Jefe de Grupo. Accede a registros de su grupo principal o delegados.
        if ($emp->nivel_id == 2) {
            return $record->empleado && in_array($record->empleado->grupo_id, $emp->obtenerGruposAsignados());
        }

        // Nivel 3: Jefe de Unidad. Accede a registros de su unidad principal o delegados.
        if ($emp->nivel_id == 3) {
            return $record->empleado && in_array($record->empleado->unidad_id, $emp->obtenerUnidadesAsignadas());
        }

        return true;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        // Personalizar la consulta para mostrar solo los permisos que el usuario puede aprobar (principales + delegados)
        $emp = auth()->user()->empleado;

        if (!$emp) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->when($emp->nivel_id == 2,
                fn ($q)=>$q->whereHas('empleado',
                    fn ($q) => $q->whereIn('grupo_id', $emp->obtenerGruposAsignados())))
            ->when($emp->nivel_id == 3,
                fn ($q)=>$q->whereHas('empleado',
                    fn ($q) => $q->whereIn('unidad_id', $emp->obtenerUnidadesAsignadas())));
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

    /**
     * Hook ejecutado antes de guardar los datos de edición del formulario.
     * Si el empleado logueado es jefe de grupo (nivel 2), se asigna su ID como jefe de visto bueno
     * y la fecha actual en la que realiza la actualización.
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        $empleado = auth()->user()->empleado;

        if ($empleado) {
            // Nivel 2: Jefe de Grupo
            if ($empleado->nivel_id == Empleado::NIVEL_JEFE_GRUPO && isset($data['id_estado_vb'])) {
                $data['id_jefe_vb'] = $empleado->id;
                $data['fecha_vb'] = now();
            }

            // Nivel 3: Jefe de Unidad
            if ($empleado->nivel_id == Empleado::NIVEL_JEFE_UNIDAD && isset($data['id_estado_aprobacion'])) {
                $data['id_jefe_aprobacion'] = $empleado->id;
                $data['fecha_aprobacion'] = now();
            }

            // Nivel 4: Jefe de División
            if ($empleado->nivel_id == 4 && isset($data['id_estado_aprobacion_jefe_division'])) {
                $data['id_oni_jefe_division'] = $empleado->oni;
                $data['fecha_aprobacion_jefe_division'] = now();
            }
        }

        return $data;
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
