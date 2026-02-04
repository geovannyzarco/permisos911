<?php

namespace App\Filament\Resources\Marcacions\Pages;

use App\Filament\Resources\Marcacions\MarcacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMarcacion extends EditRecord
{
    protected static string $resource = MarcacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
