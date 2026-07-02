<?php

namespace App\Filament\Resources\AprobacionPermisos\Pages;

use App\Filament\Resources\AprobacionPermisos\AprobacionPermisoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAprobacionPermiso extends EditRecord
{
    protected static string $resource = AprobacionPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return static::getResource()::mutateFormDataBeforeSave($data);
    }

    protected function authorizeAccess(): void
    {
        $record = $this->record;
        $empleado = auth()->user()->empleado;

        // no aprobar su propio permiso
        if ($record->empleado_id == $empleado->id) {
            abort(403);
        }

        if (! static::getResource()::canAccessRecord($record)) {
            abort(403);
        }
    }
}
