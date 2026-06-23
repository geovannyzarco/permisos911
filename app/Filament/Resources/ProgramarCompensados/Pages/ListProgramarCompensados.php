<?php

namespace App\Filament\Resources\ProgramarCompensados\Pages;

use App\Filament\Resources\ProgramarCompensados\ProgramarCompensadosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramarCompensados extends ListRecords
{
    protected static string $resource = ProgramarCompensadosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Programar Compensado')
                ->icon('heroicon-o-plus'),
        ];
    }
}
