<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permiso;
use Barryvdh\DomPDF\Facade\Pdf;

class PermisoPdfController extends Controller
{
    public function generar($id)
{
        $permiso = Permiso::with(['empleado.categoria', 'empleado.unidad.division', 'tipoPermiso', 'estadoVB', 'estadoAprobado', 'jefeVb', 'jefeAprobacion'])
        ->findOrFail($id);

    $pdf = PDF::loadView('pdf.permiso', compact('permiso'))
        ->setPaper('letter');

    return $pdf->stream('permiso-'.$id.'.pdf');
}

}
