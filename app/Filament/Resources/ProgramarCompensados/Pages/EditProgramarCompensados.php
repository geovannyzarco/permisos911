<?php

namespace App\Filament\Resources\ProgramarCompensados\Pages;

use App\Filament\Resources\ProgramarCompensados\ProgramarCompensadosResource;
use Filament\Resources\Pages\EditRecord;

class EditProgramarCompensados extends EditRecord
{
    protected static string $resource = ProgramarCompensadosResource::class;

    protected function getRedirectUrl(): string
    {
        return ProgramarCompensadosResource::getUrl('index');
    }
}
