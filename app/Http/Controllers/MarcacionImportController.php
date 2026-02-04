<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class MarcacionImportController extends Controller
{
    function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt,csv',
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');

        //leer el encabezado
        $header = fgetcsv($file, 0, "\t");

        while(($row = fgetcsv($file, 0, "\t")) !== false) {

                if(count($row)!== count($header)) {
                    // Manejar error de formato
                    continue;
                }

                $data = array_combine($header, $row);

                if(empty($data['EnNo']) || empty($data['DateTime']))
                    continue;


                $codigo = (int) trim($data['EnNo']);

              // Procesar cada fila de datos
            // Por ejemplo, guardar en la base de datos
            // Marcacion::create([
            //     'empleado_id' => $data['empleado_id'],
            //     'fecha_hora' => $data['fecha_hora'],
            //     'tipo' => $data['tipo'],
            // ]);
        }
    }
}
