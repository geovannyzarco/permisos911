<?php

namespace App\Filament\Resources\Entidads;

use App\Filament\Resources\Entidads\Pages\CreateEntidad;
use App\Filament\Resources\Entidads\Pages\EditEntidad;
use App\Filament\Resources\Entidads\Pages\ListEntidads;
use App\Filament\Resources\Entidads\Schemas\EntidadForm;
use App\Filament\Resources\Entidads\Tables\EntidadsTable;
use App\Models\Entidad;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EntidadResource extends Resource
{
    protected static ?string $model = Entidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?string $recordTitleAttribute = 'Entidades';
    protected static ?string $navigationLabel = 'Entidades';
    protected static ?string $pluralModelLabel = 'Entidades';
    protected static ?string $modelLabel = 'Entidad';

    public static function form(Schema $schema): Schema
    {
        return EntidadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntidadsTable::configure($table);
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
            'index' => ListEntidads::route('/'),
            'create' => CreateEntidad::route('/create'),
            'edit' => EditEntidad::route('/{record}/edit'),
        ];
    }
}
