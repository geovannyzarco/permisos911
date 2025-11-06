<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nivel extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['nivel'];

    protected $searchableFields = ['*'];

    protected $table = 'niveles';

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
