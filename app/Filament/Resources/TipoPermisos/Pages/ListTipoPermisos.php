<?php

namespace App\Filament\Resources\TipoPermisos\Pages;

use App\Filament\Resources\TipoPermisos\TipoPermisoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoPermisos extends ListRecords
{
    protected static string $resource = TipoPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
