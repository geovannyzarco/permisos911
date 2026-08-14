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
        'id_oni_jefe_division',
        'fecha_aprobacion_jefe_division',
        'id_estado_aprobacion_jefe_division',
        'tramitado',
        'cargo_funcional',
        'descripcion_funcion',
        // --- NUEVOS CAMPOS PARA HISTÓRICO (SNAPSHOT) ---
        'empleado_nombre',
        'empleado_oni',
        'cargo_nombre',
        'unidad_nombre',
        'division_nombre',
        'empleado_firma',
        'jefe_vb_nombre',
        'jefe_vb_oni',
        'jefe_vb_firma',
        'jefe_aprobacion_nombre',
        'jefe_aprobacion_oni',
        'jefe_aprobacion_firma',
        'jefe_division_nombre',
        'jefe_division_oni',
        'jefe_division_firma',
    ];

    /**
     * Hook booted del modelo para registrar eventos del ciclo de vida.
     */
    protected static function booted()
    {
        // Escuchar el evento 'saving' para capturar la instantánea justo antes de guardar en la base de datos
        static::saving(function ($permiso) {
            // Disparador: Se ejecuta si el estado 'tramitado' cambió y ahora es 'true'
            if ($permiso->isDirty('tramitado') && $permiso->tramitado) {
                // Asegurar que exista la carpeta firmas_historicas en el disco local privado
                if (!\Illuminate\Support\Facades\Storage::disk('local')->exists('firmas_historicas')) {
                    \Illuminate\Support\Facades\Storage::disk('local')->makeDirectory('firmas_historicas');
                }

                // Ejecutar los snapshots de datos del empleado y jefes
                $permiso->snapshotEmpleadoData();
                $permiso->snapshotJefeData();
            }
        });
    }

    /**
     * Captura una instantánea histórica de los datos vigentes del empleado solicitante.
     */
    public function snapshotEmpleadoData(): void
    {
        // Obtener el empleado solicitante mediante su relación
        $empleado = $this->empleado;
        if ($empleado) {
            // Copiar los campos textuales básicos
            $this->empleado_nombre = $empleado->nombre;
            $this->empleado_oni = $empleado->oni;
            $this->cargo_nombre = $empleado->categoria?->nombre;
            $this->unidad_nombre = $empleado->unidad?->nombre;
            $this->division_nombre = $empleado->unidad?->division?->nombre;

            // Procesar y guardar su firma Base64 como un archivo físico privado
            if ($empleado->firma) {
                $this->empleado_firma = $this->storeSignatureFile('empleado', $empleado->firma);
            }
        }
    }

    /**
     * Captura una instantánea histórica de los jefes que aprobaron o validaron el permiso.
     */
    public function snapshotJefeData(): void
    {
        // 1. Capturar datos de Jefe de Visto Bueno (Vo.Bo. / Grupo)
        $jefeVb = $this->jefeVb;
        if ($jefeVb) {
            $this->jefe_vb_nombre = $jefeVb->nombre;
            $this->jefe_vb_oni = $jefeVb->oni;
            if ($jefeVb->firma) {
                $this->jefe_vb_firma = $this->storeSignatureFile('jefe_vb', $jefeVb->firma);
            }
        }

        // 2. Capturar datos de Jefe de Aprobación (Departamento / Unidad)
        $jefeAprobacion = $this->jefeAprobacion;
        if ($jefeAprobacion) {
            $this->jefe_aprobacion_nombre = $jefeAprobacion->nombre;
            $this->jefe_aprobacion_oni = $jefeAprobacion->oni;
            if ($jefeAprobacion->firma) {
                $this->jefe_aprobacion_firma = $this->storeSignatureFile('jefe_aprobacion', $jefeAprobacion->firma);
            }
        }

        // 3. Capturar datos de Jefe de División (Nivel 4 de la división correspondiente)
        $empleado = $this->empleado;
        if ($empleado && $empleado->unidad) {
            $jefeDivision = Empleado::where('nivel_id', 4)
                ->whereHas('unidad', function ($q) use ($empleado) {
                    $q->where('division_id', $empleado->unidad->division_id);
                })
                ->first();

            if ($jefeDivision) {
                $this->jefe_division_nombre = $jefeDivision->nombre;
                $this->jefe_division_oni = $jefeDivision->oni;
                if ($jefeDivision->firma) {
                    $this->jefe_division_firma = $this->storeSignatureFile('jefe_division', $jefeDivision->firma);
                }
            }
        }
    }

    /**
     * Decodifica una firma Base64 y la almacena físicamente en el disco local privado.
     */
    private function storeSignatureFile(string $rol, string $firmaBase64): ?string
    {
        // Si no es un Base64 válido (o ya es una ruta física de archivo guardada previamente), retornarla tal cual
        if (!str_starts_with($firmaBase64, 'data:image/')) {
            return $firmaBase64;
        }

        try {
            // Ubicar la coma que delimita la metadata base64 de los datos binarios
            $commaPos = strpos($firmaBase64, ',');
            if ($commaPos === false) {
                return null;
            }

            // Obtener y decodificar el contenido binario de la imagen
            $data = substr($firmaBase64, $commaPos + 1);
            $decodedData = base64_decode($data);

            // Determinar un ID del permiso para el nombre de archivo (usar hash si es nuevo)
            $permisoId = $this->id ?? 'nuevo_' . substr(md5(microtime()), 0, 8);
            $hash = substr(md5($firmaBase64 . microtime()), 0, 8);
            $fileName = "firmas_historicas/permiso_{$permisoId}_{$rol}_{$hash}.png";

            // Almacenar el archivo de forma privada usando Storage en local
            \Illuminate\Support\Facades\Storage::disk('local')->put($fileName, $decodedData);

            return $fileName;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al almacenar firma física de permiso: " . $e->getMessage());
            return null;
        }
    }


    protected $searchableFields = ['*'];

    protected $casts = [
        'fecha_creacion' => 'date',
        'desde' => \App\Casts\DateTimeOffsetCast::class,
        'hasta' => \App\Casts\DateTimeOffsetCast::class,
        'fecha_vb' => \App\Casts\DateTimeOffsetCast::class,
        'fecha_aprobacion' => \App\Casts\DateTimeOffsetCast::class,
        'fecha_aprobacion_jefe_division' => \App\Casts\DateTimeOffsetCast::class,
        'tramitado' => 'boolean',
    ];

    public function jefeVb()
    {
        return $this->belongsTo(Empleado::class, 'id_jefe_vb');
    }

    public function jefeAprobacion()
    {
        return $this->belongsTo(Empleado::class, 'id_jefe_aprobacion');
    }

    public function jefeDivision()
    {
        return $this->belongsTo(Empleado::class, 'id_oni_jefe_division', 'oni');
    }


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

    public function estadoAprobacionJefeDivision()
    {
        return $this->belongsTo(Estado::class, 'id_estado_aprobacion_jefe_division');
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

public function historial()
{
    return $this->hasMany(PermisoHistorial::class, 'permiso_id');
}

}
