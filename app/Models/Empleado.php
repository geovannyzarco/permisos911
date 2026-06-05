<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{
    use HasFactory;
    use Searchable;

    const NIVEL_EMPLEADO     = 1;
    const NIVEL_JEFE_GRUPO   = 2;
    const NIVEL_JEFE_UNIDAD  = 3;
    const NIVEL_JEFE_DIV     = 4;

    protected $fillable = [
        'oni',
        'nombre',
        'foto',
        'firma',
        'fecha_ingreso',
        'fecha_nacimiento',
        'codigo_huella',
        'estado_civil_id',
        'nombre_conyuge',
        'numero_hijos',
        'email',
        'telefono',
        'direccion',
        'genero',
        'grupo_id',
        'categoria_id',
        'horario_id',
        'unidad_id',
        'nivel_id',
        'estado_id',
        'departamento_id',
        'municipio_id',
        'distrito_id',
        'permiso_portacion_arma',
        'numero_permiso_arma',
        'licencia_conducir',
        'tipo_licencia',
        'numero_licencia',
        'licencia_moto',
        'numero_licencia_moto',
        'permiso_estudio',
    ];

    protected $searchableFields = ['*'];

    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class);
    }

    public function hijos()
    {
        return $this->hasMany(EmpleadoHijo::class);
    }

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
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    /**
     * Retorna un array con el ID de su grupo principal y los IDs de grupos delegados activos.
     */
    public function obtenerGruposAsignados(): array
    {
        $grupoPrincipal = $this->grupo_id ? [$this->grupo_id] : [];
        
        $gruposDelegados = DelegarAprobacion::where('jefe_id', $this->id)
            ->where('tipo_delegacion', 'grupo')
            ->activas()
            ->pluck('entidad_delegada_id')
            ->toArray();

        return array_unique(array_merge($grupoPrincipal, array_map('intval', $gruposDelegados)));
    }

    /**
     * Retorna un array con el ID de su unidad principal y los IDs de unidades delegadas activas.
     */
    public function obtenerUnidadesAsignadas(): array
    {
        $unidadPrincipal = $this->unidad_id ? [$this->unidad_id] : [];
        
        $unidadesDelegadas = DelegarAprobacion::where('jefe_id', $this->id)
            ->where('tipo_delegacion', 'unidad')
            ->activas()
            ->pluck('entidad_delegada_id')
            ->toArray();

        return array_unique(array_merge($unidadPrincipal, array_map('intval', $unidadesDelegadas)));
    }
}
