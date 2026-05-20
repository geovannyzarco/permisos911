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
                ->label('Editar')
                ->icon('heroicon-o-pencil'),
        ];
    }
}
