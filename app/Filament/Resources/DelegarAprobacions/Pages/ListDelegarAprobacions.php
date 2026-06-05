<?php

namespace App\Filament\Resources\DelegarAprobacions\Pages;

use App\Filament\Resources\DelegarAprobacions\DelegarAprobacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDelegarAprobacions extends ListRecords
{
    protected static string $resource = DelegarAprobacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Delegación')
                ->icon('heroicon-o-plus'),
        ];
    }
}
