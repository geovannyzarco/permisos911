<?php

namespace App\Filament\Resources\AprobacionPermisos\Pages;

use App\Filament\Resources\AprobacionPermisos\AprobacionPermisoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAprobacionPermisos extends ListRecords
{
    protected static string $resource = AprobacionPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // CreateAction::make(),
        ];
    }
}
