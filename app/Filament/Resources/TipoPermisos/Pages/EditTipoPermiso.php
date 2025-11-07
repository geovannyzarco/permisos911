<?php

namespace App\Filament\Resources\TipoPermisos\Pages;

use App\Filament\Resources\TipoPermisos\TipoPermisoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoPermiso extends EditRecord
{
    protected static string $resource = TipoPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
