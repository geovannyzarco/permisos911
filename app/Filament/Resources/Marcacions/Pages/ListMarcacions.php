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
use App\Filament\Imports\MarcacionImporter;
use Filament\Actions\ImportAction;

class ListMarcacions extends ListRecords
{
    protected static string $resource = MarcacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Importar')
                ->label('Importar Marcaciones')
                ->color('primary')
                ->modalHeading('Importar Archivo de Marcaciones (.dat / .txt / .csv)')
                ->modalSubmitActionLabel('Importar')
                ->form([
                    FileUpload::make('archivo')
                        ->label('Archivo de datos')
                        ->required()
                        ->acceptedFileTypes(['text/plain', 'application/octet-stream', '.dat', '.txt', '.csv'])
                        ->disk('local')
                        ->directory('imports-temp')
                ])
                ->action(function (array $data, MarcacionImportService $service) {
                    $fileName = $data['archivo'];
                    $disk = \Illuminate\Support\Facades\Storage::disk('local');
                    $path = $disk->path($fileName);

                    $result = $service->importFromTxt($path);

                    // Eliminar el archivo temporal
                    $disk->delete($fileName);

                    Notification::make()
                        ->title('Importación completada')
                        ->body(
                            "Importadas: {$result['importadas']} | " .
                            "Duplicadas/Omitidas: {$result['duplicadas']}"
                        )
                        ->success()
                        ->send();
                })
        ];
    }
}
