<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permiso extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'fecha_creacion',
        'desde',
        'hasta',
        'motivo',
        'adjunto',
        'comentarios',
        'empleado_id',
        'tipo_permiso_id',
        'id_estado_aprobacion_grupo',
        'id_jefe_grupo',
        'fecha_aprobacion',
        'id_aprobacion_unidad',
        'id_jefe_unidad',
        'fecha_aprobacion_unidad',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'fecha_creacion' => 'date',
        'desde' => 'datetime',
        'hasta' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_aprobacion_unidad' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function tipoPermiso()
    {
        return $this->belongsTo(TipoPermiso::class);
    }

    public function compensados()
    {
        return $this->hasMany(Compensado::class);
    }
}
