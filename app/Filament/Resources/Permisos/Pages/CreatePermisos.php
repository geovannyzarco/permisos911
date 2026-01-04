<?php

namespace App\Filament\Resources\Permisos\Pages;

use App\Filament\Resources\Permisos\PermisosResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\CreateAction;
use App\Services\PermisoService;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use DomainException;

class CreatePermisos extends CreateRecord
{
    protected static string $resource = PermisosResource::class;

    protected function beforeCreate(): void
    {
    // Solo permisos personales
        if (($this->data['id_tipo_permiso'] ?? null) != 1) {
            return;
        }

        $empleado = auth()->user()->empleado;

        $desde = Carbon::parse($this->data['desde']);
        $hasta = Carbon::parse($this->data['hasta']);

        /** @var PermisoService $service */
        $service = app(PermisoService::class);

        try {
            // El Service decide TODO
            $this->data['cantidad_horas'] =
                $service->validarPermisoPersonal($empleado, $desde, $hasta);

        } catch (DomainException $e) {

            Notification::make()
                ->title('No se puede crear el permiso')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }




    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::getResource()::mutateFormDataBeforeCreate($data);

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
