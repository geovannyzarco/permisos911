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

    protected static ?string $recordTitleAttribute = 'Permisos';

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
        $user = auth()->user();

        if (! $user->empleado) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        $query = parent::getEloquentQuery()
            ->where('empleado_id', $user->empleado->id);

        // Campo calculado 'duracion' compatible con SQL Server 2008
        $sqlDuracion = "
            CAST(DATEDIFF(DAY, desde, hasta) AS VARCHAR(10)) + ' días ' +
            CAST(DATEPART(HOUR, DATEADD(SECOND, DATEDIFF(SECOND, desde, hasta), 0)) AS VARCHAR(10)) + ' horas ' +
            CAST(DATEPART(MINUTE, DATEADD(SECOND, DATEDIFF(SECOND, desde, hasta), 0)) AS VARCHAR(10)) + ' minutos'
        ";

        return $query->selectRaw("*, ($sqlDuracion) AS duracion");
    }

    /**
     * Se ejecuta antes de insertar el registro en base de datos
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->empleado) {
            $data['empleado_id'] = $user->empleado->id;
        }

        $data['id_estado_aprobacion_grupo'] = 4;
        $data['id_aprobacion_unidad'] = 4;

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
