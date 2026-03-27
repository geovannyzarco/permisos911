<?php

namespace App\Filament\Resources\AprobacionPermisos\Pages;

use App\Filament\Resources\AprobacionPermisos\AprobacionPermisoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAprobacionPermiso extends ViewRecord
{
    protected static string $resource = AprobacionPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
            ->label('Aprobar/Rechazar'),
        ];
    }
}
