<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UpdatePassCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-pass-csv{path=database/seeders/dui/personal911.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa contraseñas desde CSV usando ONI y DUI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = base_path($this->argument('path'));
        if (! file_exists($path)) {
            $this->error("Archivo no encontrado: $path");

            return 1;
        }
        $rows = array_map('str_getcsv', file($path));

        $updated = 0;
        $notFound = 0;
        $invalid = 0;

        foreach ($rows as $index => $row) {

            // Saltar encabezado
            if ($index === 0) {
                continue;
            }

            $oni = trim($row[0] ?? '');
            $dui = trim($row[1] ?? '');

            if (! $oni || ! $dui) {
                $invalid++;
                $this->warn("Fila inválida en índice $index");

                continue;
            }

            $user = User::where('oni', $oni)->first();

            if ($user) {
                $user->password = Hash::make($dui);
                $user->save();
                $updated++;
            } else {
                $notFound++;
            }
        }
        $this->info('Proceso finalizado:');
        $this->line("✔ Actualizados: $updated");
        $this->line("✖ No encontrados: $notFound");
        $this->line("⚠ Inválidos: $invalid");

        // Mostrar detalle de no encontrados
        if (! empty($notFoundList)) {
            $this->warn("\nListado de ONI no encontrados:");

            $this->table(
                ['ONI'],
                array_map(fn ($oni) => [$oni], $notFoundList)
            );
        }

        return 0;
    }
}
