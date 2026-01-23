<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Permiso extends Model
{
    use HasFactory;
    use Searchable;

    protected $appends = ['duracion'];

    protected $fillable = [
        'fecha_creacion',
        'desde',
        'hasta',
        'motivo',
        'adjunto',
        'comentarios',
        'empleado_id',
        'tipo_permiso_id',
        'id_estado_vb',
        'id_jefe_vb',
        'fecha_vb',
        'id_estado_aprobacion',
        'id_jefe_aprobacion',
        'fecha_aprobacion',
    ];


    protected $searchableFields = ['*'];

    protected $casts = [
        'fecha_creacion' => 'date',
        'desde' => 'datetime',
        'hasta' => 'datetime',
        'fecha_vb' => 'datetime',
        'fecha_aprobacion' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function tipoPermiso()
    {
        return $this->belongsTo(TipoPermiso::class);
    }

    public function compensados()
    {
        return $this->hasMany(Compensado::class);
    }

    public function estadoVB()
    {
        return $this->belongsTo(Estado::class, 'id_estado_vb');
    }

    public function estadoAprobado()
    {
        return $this->belongsTo(Estado::class, 'id_estado_aprobacion');
    }

    public function getDuracionAttribute(): string
    {
        if (!$this->desde || !$this->hasta) {
            return '';
        }

        $desde = Carbon::parse($this->desde);
        $hasta = Carbon::parse($this->hasta);

        if ($hasta->lessThanOrEqualTo($desde)) {
            return '0 días 0 horas 0 minutos';
        }

        $totalMinutes = $desde->diffInMinutes($hasta);

        $dias = intdiv($totalMinutes, 1440);
        $horas = intdiv($totalMinutes % 1440, 60);
        $minutos = $totalMinutes % 60;

        return "{$dias} días {$horas} horas {$minutos} minutos";
    }

}
