<?php

namespace App\Filament\Resources\Permisos\Pages;

use App\Filament\Resources\Permisos\PermisosResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\CreateAction;

class CreatePermisos extends CreateRecord
{
    protected static string $resource = PermisosResource::class;




    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::getResource()::mutateFormDataBeforeCreate($data);

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
