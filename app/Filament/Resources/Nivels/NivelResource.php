<?php

namespace App\Filament\Resources\Nivels;

use App\Filament\Resources\Nivels\Pages\CreateNivel;
use App\Filament\Resources\Nivels\Pages\EditNivel;
use App\Filament\Resources\Nivels\Pages\ListNivels;
use App\Filament\Resources\Nivels\Schemas\NivelForm;
use App\Filament\Resources\Nivels\Tables\NivelsTable;
use App\Models\Nivel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NivelResource extends Resource
{
    protected static ?string $model = Nivel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static ?string $navigationLabel = 'Niveles';
    protected static ?string $pluralModelLabel = 'Niveles';

    protected static ?string $recordTitleAttribute = 'Niveles';

    public static function form(Schema $schema): Schema
    {
        return NivelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NivelsTable::configure($table);
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
            'index' => ListNivels::route('/'),
            'create' => CreateNivel::route('/create'),
            'edit' => EditNivel::route('/{record}/edit'),
        ];
    }
}
