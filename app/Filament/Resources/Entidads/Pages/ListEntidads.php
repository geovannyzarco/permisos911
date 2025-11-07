<?php

namespace App\Filament\Resources\Entidads\Pages;

use App\Filament\Resources\Entidads\EntidadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntidads extends ListRecords
{
    protected static string $resource = EntidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
