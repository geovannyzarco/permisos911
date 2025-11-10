<?php

namespace App\Filament\Resources\Compensados\Pages;

use App\Filament\Resources\Compensados\CompensadoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompensado extends EditRecord
{
    protected static string $resource = CompensadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
