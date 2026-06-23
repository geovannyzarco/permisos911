<?php

namespace App\Filament\Resources\ProgramarCompensados;

use App\Filament\Resources\ProgramarCompensados\Pages\CreateProgramarCompensados;
use App\Filament\Resources\ProgramarCompensados\Pages\EditProgramarCompensados;
use App\Filament\Resources\ProgramarCompensados\Pages\ListProgramarCompensados;
use App\Filament\Resources\ProgramarCompensados\Schemas\ProgramarCompensadosForm;
use App\Filament\Resources\ProgramarCompensados\Tables\ProgramarCompensadosTable;
use App\Models\Permiso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProgramarCompensadosResource extends Resource
{
    protected static ?string $model = Permiso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $recordTitleAttribute = 'Programación de Compensados (Telefonistas)';
    protected static ?string $navigationLabel = 'Compensados (Telefonistas)';
    protected static ?string $pluralModelLabel = 'Programación de Compensados (Telefonistas)';
    
    protected static string | UnitEnum | null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 4;

    /**
     * Sobrescribimos el control de acceso para usar los de ProgramarCompensadosResource.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:ProgramarCompensadosResource');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('View:ProgramarCompensadosResource');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:ProgramarCompensadosResource');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('Update:ProgramarCompensadosResource');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:ProgramarCompensadosResource');
    }

    public static function getModelLabel(): string
    {
        return 'Compensado Telefonista';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Compensados Telefonistas';
    }

    public static function form(Schema $schema): Schema
    {
        return ProgramarCompensadosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramarCompensadosTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        // El listado solo debe mostrar permisos del tipo "Por tiempo compensatorio" (ID 2)
        // y de empleados de la categoría "TELEFONISTA" (ID 24)
        return parent::getEloquentQuery()
            ->where('tipo_permiso_id', 2)
            ->whereHas('empleado', function ($query) {
                $query->where('categoria_id', 24);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgramarCompensados::route('/'),
            'create' => CreateProgramarCompensados::route('/create'),
            'edit' => EditProgramarCompensados::route('/{record}/edit'),
        ];
    }
}
