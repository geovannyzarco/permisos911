<?php

namespace App\Filament\Resources\ProgramarCompensados\Pages;

use App\Filament\Resources\ProgramarCompensados\ProgramarCompensadosResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramarCompensados extends CreateRecord
{
    protected static string $resource = ProgramarCompensadosResource::class;

    protected function getRedirectUrl(): string
    {
        return ProgramarCompensadosResource::getUrl('edit', [
            'record' => $this->record,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Fuerza el tipo a compensatorio (2)
        $data['tipo_permiso_id'] = 2;
        
        // Define estados por defecto en pendiente (4)
        if (empty($data['id_estado_vb'])) {
            $data['id_estado_vb'] = 4;
        }
        if (empty($data['id_estado_aprobacion'])) {
            $data['id_estado_aprobacion'] = 4;
        }

        return $data;
    }
}
