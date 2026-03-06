<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CsvDatabaseSeeder extends Seeder
{
    private $batchSize = 50;

    public function run(): void
    {
        $path = database_path('seeders/datos');

        $files = glob($path . '/*.csv');

        if (!$files) {
            $this->command->warn('No hay CSV para importar');
            return;
        }

        sort($files);

        $this->disableFK();

        foreach ($files as $file) {

            $table = basename($file, '.csv');

            $this->command->info("Importando tabla: $table");

            $handle = fopen($file, 'r');

            if (!$handle) {
                $this->command->error("No se pudo abrir $file");
                continue;
            }

            $header = fgetcsv($handle);

            if (!$header) {
                fclose($handle);
                continue;
            }

            $idIndex = array_search('id', $header);

            if ($idIndex !== false) {
                unset($header[$idIndex]);
            }

            $header = array_values($header);

            $batch = [];

            while (($row = fgetcsv($handle)) !== false) {

                if ($idIndex !== false) {
                    unset($row[$idIndex]);
                }

                $row = array_values($row);

                $data = array_combine($header, $row);

                foreach ($data as $k => $v) {

                    if ($v === '' || $v === null) {
                        $data[$k] = null;
                        continue;
                    }

                    if ($this->isDate($v)) {

                        try {

                            $data[$k] = Carbon::createFromFormat(
                                'd/m/Y H:i:s',
                                $v
                            )->format('Y-m-d H:i:s');

                        } catch (\Exception $e) {

                            try {

                                $data[$k] = Carbon::createFromFormat(
                                    'd/m/Y',
                                    $v
                                )->format('Y-m-d');

                            } catch (\Exception $e) {}
                        }
                    }
                }

                $batch[] = $data;

                if (count($batch) >= $this->batchSize) {

                    DB::table($table)->insert($batch);

                    $batch = [];
                }
            }

            if (!empty($batch)) {
                DB::table($table)->insert($batch);
            }

            fclose($handle);

            $this->command->info("✔ $table completado");
        }

        $this->enableFK();

        $this->command->info("Importación completa.");
    }

    private function disableFK()
    {
        try {
            DB::statement('EXEC sp_msforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT ALL"');
        } catch (\Exception $e) {}
    }

    private function enableFK()
    {
        try {
            DB::statement('EXEC sp_msforeachtable "ALTER TABLE ? WITH CHECK CHECK CONSTRAINT ALL"');
        } catch (\Exception $e) {}
    }

    private function isDate($value)
    {
        return preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $value);
    }
}
