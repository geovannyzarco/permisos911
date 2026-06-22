<?php

namespace App\Filament\Resources\ProgramarCompensados\Pages;

use App\Filament\Resources\ProgramarCompensados\ProgramarCompensadosResource;
use Filament\Resources\Pages\EditRecord;

class EditProgramarCompensados extends EditRecord
{
    // Vincula la página con el recurso principal
    protected static string $resource = ProgramarCompensadosResource::class;

    /**
     * Define a qué URL se redirecciona al usuario tras guardar los cambios del registro editado.
     * En este caso, se le redirige de vuelta al listado general (index).
     */
    protected function getRedirectUrl(): string
    {
        return ProgramarCompensadosResource::getUrl('index');
    }
}
