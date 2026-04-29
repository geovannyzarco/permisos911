<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoHistorial extends Model
{
    protected $table = 'historial_permisos';

   protected $fillable = [
    'permiso_id',
    'tipo_evento',
    'empleado_id',
    'empleado_oni',
    'empleado_nombre',
    'division_id',
    'division_nombre',
    'unidad_id',
    'unidad_nombre',
    'grupo_id',
    'grupo_nombre',
    'tipo_permiso_id',
    'tipo_permiso_nombre',
    'desde',
    'hasta',
    'duracion',
    'motivo',
    'adjunto',
    'estado_id',
    'estado_nombre',
    'comentario',
    'fecha_evento',
    'ip',
    'user_agent',
];

    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }
}
