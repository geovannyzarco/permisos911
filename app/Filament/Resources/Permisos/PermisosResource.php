<?php

namespace App\Filament\Resources\Permisos;

use App\Filament\Resources\Permisos\Pages\CreatePermisos;
use App\Filament\Resources\Permisos\Pages\EditPermisos;
use App\Filament\Resources\Permisos\Pages\ListPermisos;
use App\Filament\Resources\Permisos\Pages\UserDashboard;
use App\Filament\Resources\Permisos\Pages\ViewPermisos;
use App\Filament\Resources\Permisos\RelationManagers\CompensadosRelationManager;
use App\Filament\Resources\Permisos\Schemas\PermisosForm;
use App\Filament\Resources\Permisos\Schemas\PermisosInfolist;
use App\Filament\Resources\Permisos\Tables\PermisosTable;
use App\Models\Permiso;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;


class PermisosResource extends Resource
{

    protected static ?string $model = Permiso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Solicitudes';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Mis Permisos';

    protected static ?string $recordTitleAttribute = 'Mis Permisos';

        public static function getModelLabel(): string
    {
        return 'Permisos de empleado';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Permisos de empleado';
    }

    public static function form(Schema $schema): Schema
    {
        return PermisosForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermisosInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermisosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CompensadosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermisos::route('/'),
            'create' => CreatePermisos::route('/create'),
            'view' => ViewPermisos::route('/{record}'),
            'edit' => EditPermisos::route('/{record}/edit'),

        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Obtenemos el usuario autenticado actualmente
        $user = auth()->user();

        // Si el usuario no tiene un empleado asociado (perfil incompleto o administrador sin empleado),
        // no debe ver ningún registro. Retornamos una consulta que siempre es falsa (1 = 0).
        if (! $user->empleado) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        // Retornamos la consulta original pero la filtramos para que solo muestre
        // los permisos que pertenezcan al ID del empleado asociado al usuario actual.
        return parent::getEloquentQuery()
            ->where('empleado_id', $user->empleado->id);
    }

    /**
     * Se ejecuta antes de insertar el registro en base de datos
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Obtenemos el usuario autenticado
        $user = auth()->user();

        // Si el usuario tiene un empleado asociado, forzamos que el permiso
        // quede registrado a nombre de ese empleado (evita que un usuario cree permisos para otros)
        if ($user && $user->empleado) {
            $data['empleado_id'] = $user->empleado->id;
        }

        // Asignamos el estado 4 (Pendiente) por defecto al crear un permiso nuevo.
        // Esto se aplica tanto para el estado de visto bueno del jefe como para la aprobación de RRHH.
        $data['id_estado_vb'] = 4;
        $data['id_estado_aprobacion'] = 4;

        // Retornamos los datos modificados para que se guarden en la base de datos
        return $data;
    }

    /**
     * Evitar que usuario cambie empleado en edición
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['empleado_id']); // impedir cambios al editar
        return $data;
    }


}
