<?php

namespace App\Filament\Resources\Compensados;

use App\Filament\Resources\Compensados\Pages\CreateCompensado;
use App\Filament\Resources\Compensados\Pages\EditCompensado;
use App\Filament\Resources\Compensados\Pages\ListCompensados;
use App\Filament\Resources\Compensados\Schemas\CompensadoForm;
use App\Filament\Resources\Compensados\Tables\CompensadosTable;
use App\Models\Compensado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CompensadoResource extends Resource
{
    protected static ?string $model = Compensado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Solicitudes';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'Compensados';

    public static function form(Schema $schema): Schema
    {
        return CompensadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompensadosTable::configure($table);
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
            'index' => ListCompensados::route('/'),
            'create' => CreateCompensado::route('/create'),
            'edit' => EditCompensado::route('/{record}/edit'),
        ];
    }
}
