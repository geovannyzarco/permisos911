<?php

namespace App\Filament\Resources\Permisos;

use App\Filament\Resources\Permisos\Pages\CreatePermisos;
use App\Filament\Resources\Permisos\Pages\EditPermisos;
use App\Filament\Resources\Permisos\Pages\ListPermisos;
use App\Filament\Resources\Permisos\Pages\ViewPermisos;
use App\Filament\Resources\Permisos\Schemas\PermisosForm;
use App\Filament\Resources\Permisos\Schemas\PermisosInfolist;
use App\Filament\Resources\Permisos\Tables\PermisosTable;
use App\Models\Permiso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
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
            //
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
}
