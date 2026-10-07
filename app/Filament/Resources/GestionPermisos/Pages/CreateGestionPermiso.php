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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['id_estado_vb']) && $data['id_estado_vb'] != 4) {
            $data['fecha_vb'] = now();
        }

        if (!empty($data['id_estado_aprobacion']) && $data['id_estado_aprobacion'] != 4) {
            $data['fecha_aprobacion'] = now();
        }

        if (!empty($data['id_estado_aprobacion_jefe_division']) && $data['id_estado_aprobacion_jefe_division'] != 4) {
            $data['fecha_aprobacion_jefe_division'] = now();
        }

        return $data;
    }
}
