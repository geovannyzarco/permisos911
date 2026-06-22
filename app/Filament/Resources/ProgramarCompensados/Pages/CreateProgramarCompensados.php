<?php

namespace App\Filament\Resources\ProgramarCompensados\Pages;

use App\Filament\Resources\ProgramarCompensados\ProgramarCompensadosResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramarCompensados extends CreateRecord
{
    // Vincula la página con el recurso principal
    protected static string $resource = ProgramarCompensadosResource::class;

    /**
     * Define a qué URL se redirecciona al usuario tras guardar el nuevo registro.
     * En este caso, se le redirige a la pantalla de edición para que complete o valide detalles adicionales.
     */
    protected function getRedirectUrl(): string
    {
        return ProgramarCompensadosResource::getUrl('edit', [
            'record' => $this->record,
        ]);
    }

    /**
     * Permite manipular los datos del formulario antes de que se inserten en la base de datos.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Fuerza el ID del tipo de permiso a Compensatorio (2) por seguridad del módulo
        $data['tipo_permiso_id'] = 2;
        
        // Define estados iniciales en Pendiente (4) para el flujo de firmas e historial
        if (empty($data['id_estado_vb'])) {
            $data['id_estado_vb'] = 4; // Pendiente de Vo.Bo.
        }
        if (empty($data['id_estado_aprobacion'])) {
            $data['id_estado_aprobacion'] = 4; // Pendiente de Aprobación
        }

        return $data;
    }
}
