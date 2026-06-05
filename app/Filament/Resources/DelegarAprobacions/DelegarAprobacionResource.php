<?php

namespace App\Filament\Resources\DelegarAprobacions;

use App\Filament\Resources\DelegarAprobacions\Pages\CreateDelegarAprobacion;
use App\Filament\Resources\DelegarAprobacions\Pages\EditDelegarAprobacion;
use App\Filament\Resources\DelegarAprobacions\Pages\ListDelegarAprobacions;
use App\Filament\Resources\DelegarAprobacions\Schemas\DelegarAprobacionForm;
use App\Filament\Resources\DelegarAprobacions\Tables\DelegarAprobacionsTable;
use App\Models\DelegarAprobacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DelegarAprobacionResource extends Resource
{
    protected static ?string $model = DelegarAprobacion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;
    protected static string|UnitEnum|null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'Delegar Aprobación';
    protected static ?string $navigationLabel = 'Delegar Aprobación';
    protected static ?string $pluralModelLabel = 'Delegar Aprobaciones';
    protected static ?string $modelLabel = 'Delegar Aprobación';

    public static function form(Schema $schema): Schema
    {
        return DelegarAprobacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DelegarAprobacionsTable::configure($table);
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
            'index' => ListDelegarAprobacions::route('/'),
            'create' => CreateDelegarAprobacion::route('/create'),
            'edit' => EditDelegarAprobacion::route('/{record}/edit'),
        ];
    }
}
