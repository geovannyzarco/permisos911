<?php

namespace App\Filament\Resources\GestionPermisos\Pages;

use App\Filament\Resources\GestionPermisos\GestionPermisoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGestionPermiso extends EditRecord
{
    protected static string $resource = GestionPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['id_estado_vb']) && empty($this->record->fecha_vb)) {
            $data['fecha_vb'] = now();
        }

        if (!empty($data['id_estado_aprobacion']) && empty($this->record->fecha_aprobacion)) {
            $data['fecha_aprobacion'] = now();
        }

        return $data;
    }
}
