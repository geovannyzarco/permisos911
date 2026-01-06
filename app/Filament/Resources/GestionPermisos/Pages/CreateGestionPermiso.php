<?php

namespace App\Filament\Resources\GestionPermisos\Pages;

use App\Filament\Resources\GestionPermisos\GestionPermisoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGestionPermiso extends CreateRecord
{
    protected static string $resource = GestionPermisoResource::class;

        protected function getRedirectUrl(): string
    {
        if ($this->record->tipo_permiso_id == 2) {
            return GestionPermisoResource::getUrl('edit', [
                'record' => $this->record,
            ]);
        }

        return parent::getRedirectUrl();
    }
}
