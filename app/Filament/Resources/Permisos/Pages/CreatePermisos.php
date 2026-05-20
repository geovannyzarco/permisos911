<?php

namespace App\Filament\Resources\Permisos\Pages;

use App\Filament\Resources\Permisos\PermisosResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\CreateAction;
use App\Services\PermisoService;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use DomainException;

class CreatePermisos extends CreateRecord
{
    protected static string $resource = PermisosResource::class;

    // Personalizar el título de la página a español
    public function getTitle(): string
    {
        return 'Crear Permiso';
    }

    // Personalizar el botón de envío del formulario (Crear)
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Crear Permiso');
    }

    // Personalizar el botón de "Crear y crear otro"
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Crear y crear otro');
    }

    // Personalizar el botón de cancelación a español
    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::getResource()::mutateFormDataBeforeCreate($data);

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
