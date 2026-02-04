<?php

namespace App\Filament\Resources\Marcacions\Pages;

use App\Filament\Resources\Marcacions\MarcacionResource;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use App\Models\Marcacion;
use Carbon\Carbon;


class ListMarcacions extends ListRecords
{
    protected static string $resource = MarcacionResource::class;

    protected function getHeaderActions(): array
    {
       return [

       ];
    }
}
