# Plan de Implementación: Delegar Aprobaciones e Integración de Calendario

Este plan detalla el diseño técnico para la creación del módulo **DelegarAprobacion** y su integración en el sistema, permitiendo a los jefes y supervisores aprobar solicitudes y visualizar permisos en el calendario para grupos y unidades adicionales asignados.

## Especificaciones de Visibilidad e Integración

- **Modelo y Tabla:** Se llamarán **`DelegarAprobacion`** (tabla `delegar_aprobaciones`).
- **Calendario de Permisos:** Los jefes y supervisores de Nivel 2 y 3 verán las ausencias de su grupo/unidad principal y de todas las entidades delegadas de forma activa.
- **Filtros de Unidad y Grupo:** En el Calendario se limitarán dinámicamente a aquellas entidades sobre las cuales el usuario tiene acceso (principal + delegadas).

---

## Cambios Propuestos

### 1. Base de Datos (Nueva Tabla `delegar_aprobaciones`)

Se creará una nueva migración:
```php
Schema::create('delegar_aprobaciones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('jefe_id')->constrained('empleados')->onDelete('cascade');
    $table->string('tipo_delegacion'); // 'grupo' o 'unidad'
    $table->unsignedBigInteger('entidad_delegada_id'); // ID de la unidad o grupo
    $table->date('fecha_desde')->nullable();
    $table->date('fecha_hasta')->nullable();
    $table->boolean('activo')->default(true);
    $table->timestamps();
});
```

---

### 2. Modelo `DelegarAprobacion`

Se creará el modelo `app/Models/DelegarAprobacion.php` con el scope de vigencia activa:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DelegarAprobacion extends Model
{
    protected $table = 'delegar_aprobaciones';
    protected $fillable = ['jefe_id', 'tipo_delegacion', 'entidad_delegada_id', 'fecha_desde', 'fecha_hasta', 'activo'];

    public function jefe()
    {
        return $this->belongsTo(Empleado::class, 'jefe_id');
    }

    public function scopeActivas($query)
    {
        $hoy = Carbon::today()->toDateString();
        return $query->where('activo', true)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_desde')->orWhere('fecha_desde', '<=', $hoy);
            })
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_hasta')->orWhere('fecha_hasta', '>=', $hoy);
            });
    }
}
```

---

### 3. Métodos en [Empleado.php](file:///c:/xampp/htdocs/permisos/app/Models/Empleado.php)

Se incorporarán métodos para resolver dinámicamente las entidades asignadas:
```php
public function obtenerGruposAsignados(): array
{
    $grupoPrincipal = $this->grupo_id ? [$this->grupo_id] : [];
    
    $gruposDelegados = DelegarAprobacion::where('jefe_id', $this->id)
        ->where('tipo_delegacion', 'grupo')
        ->activas()
        ->pluck('entidad_delegada_id')
        ->toArray();

    return array_unique(array_merge($grupoPrincipal, $gruposDelegados));
}

public function obtenerUnidadesAsignadas(): array
{
    $unidadPrincipal = $this->unidad_id ? [$this->unidad_id] : [];
    
    $unidadesDelegadas = DelegarAprobacion::where('jefe_id', $this->id)
        ->where('tipo_delegacion', 'unidad')
        ->activas()
        ->pluck('entidad_delegada_id')
        ->toArray();

    return array_unique(array_merge($unidadPrincipal, $unidadesDelegadas));
}
```

---

### 4. Integración en el Calendario ([CalendarioPermisos.php](file:///c:/xampp/htdocs/permisos/app/Livewire/CalendarioPermisos.php))

Modificaremos el componente Livewire para reflejar las delegaciones en la consulta de permisos y en las opciones de filtrado:

#### A. Consulta de Permisos (`getPermissionsQuery()`):
Para los Niveles 2 y 3, en lugar de limitarse a su unidad fija, consultarán todas las unidades y grupos a los que tienen acceso:
```php
if ($emp->nivel_id == 2 || $emp->nivel_id == 3) {
    $grupoIds = $emp->obtenerGruposAsignados();
    $unidadIds = $emp->obtenerUnidadesAsignadas();
    
    $query->whereHas('empleado', function ($q) use ($grupoIds, $unidadIds) {
        $q->whereIn('grupo_id', $grupoIds)
          ->orWhereIn('unidad_id', $unidadIds);
    });
}
```

#### B. Filtros de Unidad y Grupo (`render()`):
Los selectores de filtros del calendario se poblarán únicamente con las unidades y grupos autorizados (principal + delegados) para evitar que vean o filtren información ajena:
```php
$unidadesSelect = Unidad::whereIn('id', $emp->obtenerUnidadesAsignadas())->get();
$gruposSelect = Grupo::whereIn('id', $emp->obtenerGruposAsignados())
    ->orWhereIn('unidad_id', $emp->obtenerUnidadesAsignadas())
    ->get();
```

---

### 5. Modificaciones en Recursos y Políticas
- **Filtro de Consulta:** En `AprobacionPermisoResource::getEloquentQuery()` y `canAccessRecord()` para usar `obtenerGruposAsignados()` and `obtenerUnidadesAsignadas()`.
- **Políticas de Seguridad:** En `AprobacionPermisoPolicy.php` para validar aprobaciones usando `in_array()` sobre las listas dinámicas.

---

### 6. Interfaz CRUD de Filament (`DelegarAprobacionResource`)
Se creará un recurso Filament con formulario reactivo para permitir al Superadmin gestionar estas delegaciones.

---

## Plan de Verificación

### Verificación Manual
- Crear una delegación para un Supervisor (Nivel 2) asignándole un grupo de otra Unidad.
- Iniciar sesión como ese Supervisor.
- **Calendario:** Confirmar que ahora ve las ausencias de sus compañeros de grupo principal y también de los del grupo delegado.
- **Aprobaciones:** Verificar que en la bandeja de aprobación masiva y detalles aparezcan las solicitudes de ambos grupos y que pueda darles Visto Bueno (VB).
- **Filtros:** Comprobar que en los filtros de unidad/grupo del calendario solo se listen las opciones autorizadas.
```
