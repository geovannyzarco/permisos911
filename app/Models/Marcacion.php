<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marcacion extends Model
{
    protected $table = 'marcaciones';

    protected $fillable = [
        'codigo',
        'marcacion',
    ];

    protected $casts = [
        'codigo' => 'integer',
        'marcacion'=>'datetime',
    ];
}
