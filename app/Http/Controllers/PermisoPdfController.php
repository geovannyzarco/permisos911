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

        if ($permiso->desde && $permiso->hasta) {
            $desde = \Carbon\Carbon::parse($permiso->desde);
            $hasta = \Carbon\Carbon::parse($permiso->hasta);

            if ($hasta->greaterThan($desde)) {
                $diferencia = $desde->diff($hasta);
                
                $permiso->meses = $diferencia->m;
                $permiso->dias = $diferencia->d;
                $permiso->horas = $diferencia->h;
                $permiso->minutos = $diferencia->i;
            } else {
                $permiso->meses = 0;
                $permiso->dias = 0;
                $permiso->horas = 0;
                $permiso->minutos = 0;
            }
        } else {
            $permiso->meses = 0;
            $permiso->dias = 0;
            $permiso->horas = 0;
            $permiso->minutos = 0;
        }

    $pdf = PDF::loadView('pdf.permiso', compact('permiso'))
        ->setPaper('letter');

    return $pdf->stream('permiso-'.$id.'.pdf');
}

}
