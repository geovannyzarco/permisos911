<?php

namespace App\Filament\Resources\GestionPermisos\Pages;

use App\Filament\Resources\GestionPermisos\GestionPermisoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGestionPermiso extends EditRecord
{
    protected static string $resource = GestionPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
