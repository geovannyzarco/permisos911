<?php

namespace App\Filament\Resources\GestionPermisos;

use App\Filament\Resources\GestionPermisos\Pages\CreateGestionPermiso;
use App\Filament\Resources\GestionPermisos\Pages\EditGestionPermiso;
use App\Filament\Resources\GestionPermisos\Pages\ListGestionPermisos;
use App\Filament\Resources\GestionPermisos\Pages\ViewGestionPermiso;
use App\Filament\Resources\GestionPermisos\RelationManagers\CompensadosRelationManager;
use App\Filament\Resources\GestionPermisos\Schemas\GestionPermisoForm;
use App\Filament\Resources\GestionPermisos\Schemas\GestionPermisoInfolist;
use App\Filament\Resources\GestionPermisos\Tables\GestionPermisosTable;
use App\Models\Permiso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class GestionPermisoResource extends Resource
{
    protected static ?string $model = Permiso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'Permisos';
    protected static ?string $navigationLabel = 'Gestión de Permisos';
    protected static ?string $pluralModelLabel = 'Gestión de Permisos';
    protected static string | UnitEnum | null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 3;



    public static function form(Schema $schema): Schema
    {
        return GestionPermisoForm::configure($schema);

    }

    public static function infolist(Schema $schema): Schema
    {
        return GestionPermisoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GestionPermisosTable::configure($table);
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
            'index' => ListGestionPermisos::route('/'),
            'create' => CreateGestionPermiso::route('/create'),
            'view' => ViewGestionPermiso::route('/{record}'),
            'edit' => EditGestionPermiso::route('/{record}/edit'),
        ];
    }



}
