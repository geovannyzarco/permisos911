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
    // Define el modelo base con el que interactúa este recurso (Permisos)
    protected static ?string $model = Permiso::class;

    // Configuración visual: Ícono del menú lateral (Reloj)
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    // Etiquetas y textos para el menú lateral e interfaz de usuario
    protected static ?string $recordTitleAttribute = 'Programación de Compensados (Telefonistas)';
    protected static ?string $navigationLabel = 'Compensados (Telefonistas)';
    protected static ?string $pluralModelLabel = 'Programación de Compensados (Telefonistas)';
    
    // Grupo de navegación donde se alojará este recurso en el panel de control
    protected static string | UnitEnum | null $navigationGroup = 'Administración';
    
    // Orden de visualización en el grupo de navegación
    protected static ?int $navigationSort = 4;

    /**
     * Determina si el usuario logueado tiene permiso para ver el listado de este recurso.
     * En este caso, reutiliza el permiso administrativo global de la Gestión de Permisos.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->can('ViewAny:GestionPermisoResource');
    }

    /**
     * Determina si el usuario puede ver los detalles de un registro específico.
     */
    public static function canView($record): bool
    {
        return auth()->user()->can('View:GestionPermisoResource');
    }

    /**
     * Determina si el usuario puede acceder a la pantalla de creación.
     */
    public static function canCreate(): bool
    {
        return auth()->user()->can('Create:GestionPermisoResource');
    }

    /**
     * Determina si el usuario puede editar un permiso compensatorio de telefonista.
     * REGLA: Si el permiso ya está marcado como 'tramitado', se bloquea su edición completa.
     */
    public static function canEdit($record): bool
    {
        if ($record && $record->tramitado) {
            return false;
        }

        return auth()->user()->can('Update:GestionPermisoResource');
    }

    /**
     * Determina si el usuario puede borrar un permiso.
     */
    public static function canDelete($record): bool
    {
        return auth()->user()->can('Delete:GestionPermisoResource');
    }

    // Nombre singular del modelo para mostrar en la UI
    public static function getModelLabel(): string
    {
        return 'Compensado Telefonista';
    }

    // Nombre plural del modelo para mostrar en la UI
    public static function getPluralModelLabel(): string
    {
        return 'Compensados Telefonistas';
    }

    /**
     * Vincula la estructura del formulario a la clase Schemas independiente.
     */
    public static function form(Schema $schema): Schema
    {
        return ProgramarCompensadosForm::configure($schema);
    }

    /**
     * Vincula la estructura de la tabla a la clase Tables independiente.
     */
    public static function table(Table $table): Table
    {
        return ProgramarCompensadosTable::configure($table);
    }

    /**
     * Modifica la consulta SQL base del listado.
     * RESTRICCIÓN: Este módulo solo debe mostrar permisos que sean de tipo Compensatorio (ID 2)
     * y cuyos empleados tengan asignada la categoría 24 (Telefonista).
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tipo_permiso_id', 2) // Filtro: Solo tipo compensado (2)
            ->whereHas('empleado', function ($query) {
                $query->where('categoria_id', 24); // Filtro: Solo empleados con categoría Telefonista (24)
            });
    }

    /**
     * Mapea las rutas internas del recurso (Lista, Creación y Edición).
     */
    public static function getPages(): array
    {
        return [
            'index' => ListProgramarCompensados::route('/'),
            'create' => CreateProgramarCompensados::route('/create'),
            'edit' => EditProgramarCompensados::route('/{record}/edit'),
        ];
    }
}
