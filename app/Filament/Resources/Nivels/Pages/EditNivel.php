<?php

namespace App\Filament\Resources\Nivels\Pages;

use App\Filament\Resources\Nivels\NivelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNivel extends EditRecord
{
    protected static string $resource = NivelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
