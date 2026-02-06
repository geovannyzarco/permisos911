<?php

namespace App\Filament\Imports;

use App\Models\Marcacion;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Carbon;
use EightyNine\ExcelImport\Concerns\ToModel;
use EightyNine\ExcelImport\Concerns\WithHeadingRow;

class MarcacionImporter extends Importer
{
    protected static ?string $model = Marcacion::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('codigo')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('marcacion')
                ->requiredMapping()
                ->rules(['required', 'datetime']),
        ];
    }

    public function resolveRecord(): Marcacion
    {
        return Marcacion::firstOrNew([
            'codigo' => $this->data['codigo'],
            'marcacion' => Carbon::parse($this->data['marcacion'] ),
        ],
        [
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your marcacion import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
