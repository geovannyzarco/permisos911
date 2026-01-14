<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpleadoHijo extends Model
{
    protected $table = 'empleado_hijos';

    protected $fillable = [
        'empleado_id',
        'nombre',
        'fecha_nacimiento',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}

