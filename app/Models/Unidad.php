<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unidad extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['nombre', 'division_id'];

    protected $searchableFields = ['*'];

    protected $table = 'unidades';

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
