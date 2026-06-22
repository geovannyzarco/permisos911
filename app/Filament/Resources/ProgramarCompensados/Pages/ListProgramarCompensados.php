<?php

namespace App\Filament\Resources\ProgramarCompensados\Pages;

use App\Filament\Resources\ProgramarCompensados\ProgramarCompensadosResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramarCompensados extends ListRecords
{
    // Vincula esta página con el recurso de compensados de telefonista correspondiente
    protected static string $resource = ProgramarCompensadosResource::class;

    /**
     * Define las acciones globales disponibles en la cabecera del listado.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Botón de creación para registrar un nuevo permiso programado
            CreateAction::make()
                ->label('Programar Compensado')
                ->icon('heroicon-o-plus'),
        ];
    }
}
