<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['nombre'];

    protected $searchableFields = ['*'];

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
