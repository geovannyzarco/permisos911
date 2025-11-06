<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Facades\File;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ruta del archivo CSV
        $path = database_path('seeders/datos/categorias.csv');
        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo: $path");
            return;
        }

         // Cargar el CSV
         $csv = Reader::createFromPath($path, 'r');
         $csv->setHeaderOffset(0); // La primera fila contiene los encabezados
        $records = (new Statement())->process($csv);
        foreach ($records as $record) {
            // Crear una nueva categoría
            Categoria::create([
                'nombre' => $record['nombre'],
            ]);
        }
       $this->command->info('Categorías sembradas correctamente desde el archivo CSV.');
    }
}
