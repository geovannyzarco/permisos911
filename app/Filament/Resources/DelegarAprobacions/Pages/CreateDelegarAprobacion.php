<?php

namespace App\Filament\Resources\DelegarAprobacions\Pages;

use App\Filament\Resources\DelegarAprobacions\DelegarAprobacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDelegarAprobacion extends CreateRecord
{
    protected static string $resource = DelegarAprobacionResource::class;

    public function getTitle(): string
    {
        return 'Crear Delegación de Aprobación';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Crear Delegación');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Cancelar');
    }
}
