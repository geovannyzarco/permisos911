<?php

namespace App\Filament\Resources\TipoPermisos;

use App\Filament\Resources\TipoPermisos\Pages\CreateTipoPermiso;
use App\Filament\Resources\TipoPermisos\Pages\EditTipoPermiso;
use App\Filament\Resources\TipoPermisos\Pages\ListTipoPermisos;
use App\Filament\Resources\TipoPermisos\Schemas\TipoPermisoForm;
use App\Filament\Resources\TipoPermisos\Tables\TipoPermisosTable;
use App\Models\TipoPermiso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TipoPermisoResource extends Resource
{
    protected static ?string $model = TipoPermiso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static string | UnitEnum | null $navigationGroup = 'Aministración';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'Tipos de Permisos';

    public static function form(Schema $schema): Schema
    {
        return TipoPermisoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoPermisosTable::configure($table);
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
            'index' => ListTipoPermisos::route('/'),
            'create' => CreateTipoPermiso::route('/create'),
            'edit' => EditTipoPermiso::route('/{record}/edit'),
        ];
    }
}
