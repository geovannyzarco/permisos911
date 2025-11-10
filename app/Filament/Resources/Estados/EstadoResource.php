<?php

namespace App\Filament\Resources\Estados;

use App\Filament\Resources\Estados\Pages\CreateEstado;
use App\Filament\Resources\Estados\Pages\EditEstado;
use App\Filament\Resources\Estados\Pages\ListEstados;
use App\Filament\Resources\Estados\Schemas\EstadoForm;
use App\Filament\Resources\Estados\Tables\EstadosTable;
use App\Models\Estado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EstadoResource extends Resource
{
    protected static ?string $model = Estado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Estados';
    protected static string | UnitEnum | null $navigationGroup = 'Mantenimiento';
    protected static ?int $navigationSort = 3;
    protected static ?string $pluralModelLabel = 'Estados';
    protected static ?string $modelLabel = 'Entidad';

    protected static ?string $recordTitleAttribute = 'Estados';

    public static function form(Schema $schema): Schema
    {
        return EstadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EstadosTable::configure($table);
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
            'index' => ListEstados::route('/'),
            'create' => CreateEstado::route('/create'),
            'edit' => EditEstado::route('/{record}/edit'),
        ];
    }
}
