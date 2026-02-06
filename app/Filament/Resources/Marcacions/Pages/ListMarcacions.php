<?php

namespace App\Filament\Resources\Marcacions\Pages;

use App\Filament\Resources\Marcacions\MarcacionResource;
use Filament\Resources\Pages\ListRecords;
//use EightyNine\ExcelImport\ExcelImportAction;
use App\Models\Marcacion;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use App\Services\MarcacionImportService;
//use App\Filament\Imports\MarcacionImporter;
//use Filament\Actions\ImportAction;

class ListMarcacions extends ListRecords
{
    protected static string $resource = MarcacionResource::class;

    protected function getHeaderActions(): array
    {
       return [
         //importacion usando controlador y servicio
            Action::make('Importar')
                ->label('Importar Marcaciones')

                ->modalHeading('Imporar Archivo TXT')
                ->modalSubmitActionLabel('Importar')
                ->form([
                    FileUpload::make('archivo')
                        ->label('Archivo TXT')
                        ->required()
                        ->acceptedFileTypes(['text/plain'])
                        ->storeFile(false),
                ])
                ->action(function (array $data, MarcacionImportService $service) {
                   $path = $data['archivo']->getRealPath();
                   $result = $service->importFromTxt($path);
                   Notification::make()
                    ->title('Importación completada')
                    ->body(
                        "Importadas: {$result['importadas']} | " .
                        "Duplicadas: {$result['duplicadas']}"
                    )
                    ->success()
                    ->send();
                })
/*
                 ExcelImportAction::make()
                    ->label('Importar Marcaciones')
                    ->color('primary')
                    ->modalHeading('Importar Marcaciones desde Archivo TXT')
                    ->modalSubmitActionLabel('Importar')
                    ->model(Marcacion::class)
                    ->beforeImport(function (array $row) {

                        $codigo = $row[0] ?? null;
                        $fecha  = $row[1] ?? null;

                        if (! $codigo || ! $fecha) {
                            return null;
                        }

                        try {
                            $marcacion = Carbon::createFromFormat('d/m/Y H:i', $fecha)->setSeconds(0);
                        } catch (\Exception $e) {
                            return null; // omite filas con fecha inválida
                        }

                        if (
                            Marcacion::where('codigo', $codigo)
                                ->where('marcacion', $marcacion)
                                ->exists()
                        ) {
                            return null;
                        }

                        return [
                            'codigo'    => $codigo,
                            'marcacion' => $marcacion,
                        ];
                    })*/

                /*ImportAction::make()
                ->importer(MarcacionImporter::class)
                ->csvDelimiter(";")*/

       ];
    }
}
