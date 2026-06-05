<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DelegarAprobacion extends Model
{
    use HasFactory;

    protected $table = 'delegar_aprobaciones';

    protected $fillable = [
        'jefe_id',
        'tipo_delegacion',
        'entidad_delegada_id',
        'fecha_desde',
        'fecha_hasta',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
    ];

    /**
     * Relación con el jefe/empleado al que se delega.
     */
    public function jefe()
    {
        return $this->belongsTo(Empleado::class, 'jefe_id');
    }

    /**
     * Scope para obtener las delegaciones actualmente activas y vigentes por fecha.
     */
    public function scopeActivas($query)
    {
        $hoy = Carbon::today()->toDateString();

        return $query->where('activo', true)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_desde')
                  ->orWhere('fecha_desde', '<=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_hasta')
                  ->orWhere('fecha_hasta', '>=', $hoy);
            });
    }
}
