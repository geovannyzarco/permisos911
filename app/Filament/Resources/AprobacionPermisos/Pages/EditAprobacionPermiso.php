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

    protected function authorizeAccess(): void
    {
        $record = $this->record;
        $empleado = auth()->user()->empleado;

        // no aprobar su propio permiso
        if ($record->empleado_id == $empleado->id) {
            abort(403);
        }

        if ($empleado->nivel_id == 2) {
            if ($record->empleado->grupo_id !== $empleado->grupo_id) {
                abort(403);
            }
        }

        if ($empleado->nivel_id == 3) {
            if ($record->empleado->unidad_id !== $empleado->unidad_id) {
                abort(403);
            }
        }
    }
}
