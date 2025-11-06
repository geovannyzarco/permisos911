<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'oni',
        'nombre',
        'foto',
        'firma',
        'grupo_id',
        'categoria_id',
        'horario_id',
        'unidad_id',
        'nivel_id',
        'estado_id',
    ];

    protected $searchableFields = ['*'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class);
    }
}
