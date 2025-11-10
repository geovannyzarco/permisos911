<?php

namespace App\Filament\Resources\Compensados\Pages;

use App\Filament\Resources\Compensados\CompensadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompensados extends ListRecords
{
    protected static string $resource = CompensadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
