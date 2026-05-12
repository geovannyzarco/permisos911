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

    public static function canView($record): bool
    {
        return auth()->user()->can('View:AprobacionPermisoResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:AprobacionPermisoResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:AprobacionPermisoResource');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        // Personalizar la consulta para mostrar solo los permisos que el usuario puede aprobar
        $emp = auth()->user()->empleado;
        return parent::getEloquentQuery()
            ->when($emp->nivel_id == 2,
                fn ($q)=>$q->whereHas('empleado',
                    fn ($q) => $q->where('grupo_id', $emp->grupo_id)))
            ->when($emp->nivel_id == 3,
                fn ($q)=>$q->whereHas('empleado',
                    fn ($q) => $q->where('unidad_id', $emp->unidad_id)));


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
