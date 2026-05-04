<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['nombre', 'unidad_id', 'permisos_diarios'];

    protected $searchableFields = ['*'];

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}
