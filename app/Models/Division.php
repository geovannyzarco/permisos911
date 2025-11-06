<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['nombre', 'ubicacion', 'telefono', 'email'];

    protected $searchableFields = ['*'];

    protected $table = 'divisiones';

    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }
}
