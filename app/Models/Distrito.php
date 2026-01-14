<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $fillable = [
        'municipio_id',
        'nombre',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}

