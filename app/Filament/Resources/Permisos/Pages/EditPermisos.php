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


}
