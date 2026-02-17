<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Horario extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'nombre',
        'horas_jornada',
        'horas_personales',
        'hora_entrada',
        'hora_salida',
        'cruza_medianoche',
        ];

    protected $searchableFields = ['*'];

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
