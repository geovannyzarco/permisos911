<?php

namespace App\Filament\Resources\Marcacions;

use App\Filament\Resources\Marcacions\Pages\CreateMarcacion;
use App\Filament\Resources\Marcacions\Pages\EditMarcacion;
use App\Filament\Resources\Marcacions\Pages\ListMarcacions;
use App\Filament\Resources\Marcacions\Schemas\MarcacionForm;
use App\Filament\Resources\Marcacions\Tables\MarcacionsTable;
use App\Models\Marcacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MarcacionResource extends Resource
{
    protected static ?string $model = Marcacion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $recordTitleAttribute = 'Marcacion';
    protected static ?string $navigationLabel = 'Marcaciones';
    protected static ?string $pluralLabel = 'Marcaciones';
    protected static string | UnitEnum | null $navigationGroup = 'Administración';
     protected static ?int $navigationSort = 3;


    public static function form(Schema $schema): Schema
    {
        return MarcacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarcacionsTable::configure($table);
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
            'index' => ListMarcacions::route('/'),
            'create' => CreateMarcacion::route('/create'),
            'edit' => EditMarcacion::route('/{record}/edit'),
        ];
    }

}
