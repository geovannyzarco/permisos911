<?php

namespace App\Filament\Resources\Permisos\Pages;

use App\Filament\Resources\Permisos\PermisosResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermisos extends ViewRecord
{
    protected static string $resource = PermisosResource::class;

    // Personalizar el título de la página de visualización a español
    public function getTitle(): string
    {
        return 'Detalles del Permiso';
    }

    // Personalizar las acciones de cabecera (Editar)
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar Permisos')
                ->icon('heroicon-o-pencil')
                ->visible(fn ($record) => 
                    $record->id_estado_vb == 4 && 
                    $record->id_estado_aprobacion == 4 && 
                    ($record->id_estado_aprobacion_jefe_division === null || $record->id_estado_aprobacion_jefe_division == 4)
                ),
        ];
    }
}
