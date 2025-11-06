<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compensado extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'desde',
        'hasta',
        'justificacion',
        'adjunto',
        'permiso_id',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'desde' => 'datetime',
        'hasta' => 'datetime',
    ];

    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }
}
