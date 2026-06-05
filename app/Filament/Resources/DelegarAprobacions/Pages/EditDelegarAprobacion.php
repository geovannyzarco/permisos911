<?php

namespace App\Filament\Resources\DelegarAprobacions\Pages;

use App\Filament\Resources\DelegarAprobacions\DelegarAprobacionResource;
use Filament\Resources\Pages\EditRecord;

class EditDelegarAprobacion extends EditRecord
{
    protected static string $resource = DelegarAprobacionResource::class;

    public function getTitle(): string
    {
        return 'Editar Delegación de Aprobación';
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()->label('Guardar Cambios');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }
}
