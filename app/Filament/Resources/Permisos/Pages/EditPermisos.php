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

    // Personalizar el título de la página de edición a español
    public function getTitle(): string
    {
        return 'Editar Permiso';
    }

    // Personalizar las acciones de cabecera (Ver y Eliminar)
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver')
                ->icon('heroicon-o-eye'),
            DeleteAction::make()
                ->label('Eliminar')
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return static::getResource()::mutateFormDataBeforeSave($data);
    }

    // Personalizar el botón de guardar cambios del formulario
    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Guardar Cambios');
    }

    // Personalizar el botón de cancelación a español
    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar');
    }
}
