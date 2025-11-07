<?php

namespace App\Filament\Resources\Entidads\Pages;

use App\Filament\Resources\Entidads\EntidadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEntidad extends EditRecord
{
    protected static string $resource = EntidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
