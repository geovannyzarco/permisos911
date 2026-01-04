<?php

namespace App\Filament\Resources\Permisos\Pages;

use App\Filament\Resources\Permisos\PermisosResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Services\PermisoService;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class EditPermisos extends EditRecord
{
    protected static string $resource = PermisosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
     {
          if (($this->data['id_tipo_permiso'] ?? null) != 1) {
        return;
    }

    $empleado = $this->record->empleado;

    $desde = Carbon::parse($this->data['desde']);
    $hasta = Carbon::parse($this->data['hasta']);



    dump($desde);
    // VALIDAR RANGO
    if ($hasta->lessThanOrEqualTo($desde)) {
        throw ValidationException::withMessages([
            'hasta' => 'La fecha/hora "hasta" debe ser mayor que "desde".',
        ]);
    }

    $horasSolicitadas = $desde->diffInMinutes($hasta) / 60;

    $service = app(PermisoService::class);

    if (! $service->puedeGuardarPermisoPersonal(
        $empleado,
        $horasSolicitadas,
        $this->record
    )) {
        throw ValidationException::withMessages([
            'hasta' => 'El tiempo solicitado excede el saldo de horas personales disponibles.',
        ]);
    }

    $this->data['cantidad_horas'] = $horasSolicitadas;
    }
}
