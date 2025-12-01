<?php

namespace App\Filament\Resources\Permisos\Pages;

use App\Filament\Resources\Permisos\PermisosResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermisos extends CreateRecord
{
    protected static string $resource = PermisosResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::getResource()::mutateFormDataBeforeCreate($data);
    }

public static function afterCreate($record): void
    {
        // Redirigir a editar una vez creado
        redirect()->to(PermisosResource::getUrl('edit', ['record' => $record]));
    }
}
