<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\Empleado;
use App\Models\Grupo;
use App\Models\Permiso;
use App\Models\TipoPermiso;
use App\Models\Unidad;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CalendarioPermisos extends Component
{
    // Navegación de fecha
    public $year;
    public $month;

    // Filtros
    public $divisionId = null;
    public $unidadId = null;
    public $grupoId = null;
    public $tipoPermisoId = null;
    public $status = null;
    public $search = '';

    // Estado del Modal
    public $isModalOpen = false;
    public $selectedDate = null;
    public $selectedDayPermissions = [];

    // Restricciones de interfaz según nivel
    public $isEmployeeRestricted = false;
    public $isGrupoRestricted = false;
    public $isUnidadRestricted = false;
    public $isDivisionRestricted = false;

    public function mount()
    {
        $this->year = now()->year;
        $this->month = now()->month;

        // Cargar restricciones por rol de empleado
        $user = auth()->user();
        $emp = $user->empleado;

        if ($emp) {
            // Admins y Super Admins no tienen restricciones de visualización
            if ($user->hasRole(['super_admin', 'admin'])) {
                return;
            }

            if ($emp->nivel_id == 1) {
                // Empleado regular: restringir a su grupo si tiene uno asignado (y no es ID 12)
                if ($emp->grupo_id && $emp->grupo_id != 12) {
                    $this->isEmployeeRestricted = true;
                    $this->grupoId = $emp->grupo_id;
                    $this->unidadId = $emp->unidad_id;
                    $this->divisionId = $emp->unidad?->division_id;
                } else {
                    // Si no tiene grupo asignado (es nulo o ID 12), ve los de su unidad y puede filtrar por grupo
                    $this->isUnidadRestricted = true;
                    $this->grupoId = null;
                    $this->unidadId = $emp->unidad_id;
                    $this->divisionId = $emp->unidad?->division_id;
                }
            } elseif ($emp->nivel_id == 2) {
                // Supervisor: restringir a su unidad y puede filtrar por grupos
                $this->isUnidadRestricted = true;
                $this->grupoId = null;
                $this->unidadId = $emp->unidad_id;
                $this->divisionId = $emp->unidad?->division_id;
            } elseif ($emp->nivel_id == 3) {
                // Jefe de Unidad/Departamento: restringir a su unidad y puede filtrar por grupos
                $this->isUnidadRestricted = true;
                $this->grupoId = null;
                $this->unidadId = $emp->unidad_id;
                $this->divisionId = $emp->unidad?->division_id;
            } elseif ($emp->nivel_id == 4) {
                // Jefe de División: restringir a su división y puede filtrar por unidades/grupos
                $this->isDivisionRestricted = true;
                $this->divisionId = $emp->unidad?->division_id;
            }
        }
    }

    public function updatedDivisionId()
    {
        $this->unidadId = null;
        $this->grupoId = null;
    }

    public function updatedUnidadId()
    {
        $this->grupoId = null;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function goToToday()
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    protected function getPermissionsQuery()
    {
        $user = auth()->user();
        $emp = $user->empleado;

        $query = Permiso::query()->with([
            'empleado.categoria',
            'empleado.unidad.division',
            'empleado.grupo',
            'tipoPermiso',
            'estadoVB',
            'estadoAprobado',
            'estadoAprobacionJefeDivision'
        ]);

        // Restringir el alcance inicial según jerarquía (nivel_id)
        if ($emp) {
            // Si es administrador o superadministrador, no restringimos el alcance inicial
            if ($user->hasRole(['super_admin', 'admin'])) {
                // Acceso total
            } else {
                if ($emp->nivel_id == 1) {
                    // Empleado ve permisos de su grupo (si tiene y no es ID 12) o de su unidad
                    if ($emp->grupo_id && $emp->grupo_id != 12) {
                        $query->whereHas('empleado', function ($q) use ($emp) {
                            $q->where('grupo_id', $emp->grupo_id);
                        });
                    } else {
                        $query->whereHas('empleado', function ($q) use ($emp) {
                            $q->where('unidad_id', $emp->unidad_id);
                        });
                    }
                } elseif ($emp->nivel_id == 2) {
                    // Supervisor ve permisos de su unidad
                    $query->whereHas('empleado', function ($q) use ($emp) {
                        $q->where('unidad_id', $emp->unidad_id);
                    });
                } elseif ($emp->nivel_id == 3) {
                    // Jefe de Departamento ve permisos de su unidad
                    $query->whereHas('empleado', function ($q) use ($emp) {
                        $q->where('unidad_id', $emp->unidad_id);
                    });
                } elseif ($emp->nivel_id == 4) {
                    // Jefe de División ve permisos de su división
                    $query->whereHas('empleado.unidad', function ($q) use ($emp) {
                        $q->where('division_id', $emp->unidad?->division_id);
                    });
                }
            }
        }

        // Filtros dinámicos adicionales (si el usuario tiene permisos para usarlos)
        if (!$this->isEmployeeRestricted && !$this->isGrupoRestricted && !$this->isUnidadRestricted && !$this->isDivisionRestricted) {
            $query->when($this->divisionId, function ($q) {
                $q->whereHas('empleado.unidad', fn($sub) => $sub->where('division_id', $this->divisionId));
            });
        }

        if (!$this->isEmployeeRestricted && !$this->isGrupoRestricted) {
            $query->when($this->unidadId, function ($q) {
                $q->whereHas('empleado', fn($sub) => $sub->where('unidad_id', $this->unidadId));
            });
        }

        if (!$this->isEmployeeRestricted) {
            $query->when($this->grupoId, function ($q) {
                $q->whereHas('empleado', fn($sub) => $sub->where('grupo_id', $this->grupoId));
            });
        }

        return $query
            ->when($this->tipoPermisoId, function ($q) {
                $q->where('tipo_permiso_id', $this->tipoPermisoId);
            })
            ->when($this->status, function ($q) {
                if ($this->status === 'aprobado') {
                    $q->where('id_estado_aprobacion_jefe_division', 3);
                } elseif ($this->status === 'pendiente') {
                    $q->where('id_estado_aprobacion_jefe_division', 4);
                } elseif ($this->status === 'anulado') {
                    $q->where('id_estado_aprobacion_jefe_division', 5);
                }
            })
            ->when($this->search, function ($q) {
                $q->whereHas('empleado', function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->search}%")
                        ->orWhere('oni', 'like', "%{$this->search}%");
                });
            });
    }

    public function openDayModal($dateString)
    {
        $this->selectedDate = $dateString;
        $date = Carbon::parse($dateString);

        $format = 'Y-m-d H:i:s.v P';
        $startStr = $date->copy()->startOfDay()->format($format);
        $endStr = $date->copy()->endOfDay()->format($format);

        $this->selectedDayPermissions = $this->getPermissionsQuery()
            ->where(function ($query) use ($startStr, $endStr) {
                $query->whereBetween('desde', [$startStr, $endStr])
                      ->orWhereBetween('hasta', [$startStr, $endStr])
                      ->orWhere(function ($sub) use ($startStr, $endStr) {
                          $sub->where('desde', '<=', $startStr)
                              ->where('hasta', '>=', $endStr);
                      });
            })
            ->get()
            ->map(function ($p) {
                $emp = auth()->user()->empleado;
                if ($emp) {
                    if ($emp->nivel_id == 1) {
                        $viewUrl = '/permisos/permisos/' . $p->id;
                    } elseif ($emp->nivel_id == 2 || $emp->nivel_id == 3) {
                        $viewUrl = '/permisos/aprobacion-permisos/' . $p->id;
                    } else {
                        $viewUrl = '/permisos/gestion-permisos/' . $p->id;
                    }
                } else {
                    $viewUrl = '/permisos/gestion-permisos/' . $p->id;
                }

                return [
                    'id' => $p->id,
                    'empleado_nombre' => $p->empleado?->nombre ?? 'N/A',
                    'empleado_foto' => $p->empleado?->foto,
                    'empleado_oni' => $p->empleado?->oni ?? 'N/A',
                    'unidad' => $p->empleado?->unidad?->nombre ?? 'N/A',
                    'grupo' => $p->empleado?->grupo?->nombre ?? 'N/A',
                    'tipo_permiso' => $p->tipoPermiso?->nombre ?? 'N/A',
                    'motivo' => $p->motivo,
                    'desde' => Carbon::parse($p->desde)->format('d/m/Y h:i A'),
                    'hasta' => Carbon::parse($p->hasta)->format('d/m/Y h:i A'),
                    'duracion' => $p->duracion,
                    'estado_vb' => $p->estadoVB?->nombre ?? 'PENDIENTE',
                    'id_estado_vb' => $p->id_estado_vb,
                    'estado_aprobacion' => $p->estadoAprobado?->nombre ?? 'PENDIENTE',
                    'id_estado_aprobacion' => $p->id_estado_aprobacion,
                    'estado_division' => $p->estadoAprobacionJefeDivision?->nombre ?? 'PENDIENTE',
                    'id_estado_aprobacion_jefe_division' => $p->id_estado_aprobacion_jefe_division,
                    'pdf_url' => $p->id_estado_aprobacion_jefe_division == 3 ? route('permiso.pdf', ['id' => $p->id]) : null,
                    'view_url' => $viewUrl,
                ];
            })
            ->toArray();

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedDate = null;
        $this->selectedDayPermissions = [];
    }

    public function getDaysInGrid()
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Calcular inicio (Domingo) y fin (Sábado) de la cuadrícula
        $gridStart = $startOfMonth->copy();
        if ($gridStart->dayOfWeek !== Carbon::SUNDAY) {
            $gridStart->subDays($gridStart->dayOfWeek);
        }
        $gridEnd = $endOfMonth->copy();
        if ($gridEnd->dayOfWeek !== Carbon::SATURDAY) {
            $gridEnd->addDays(6 - $gridEnd->dayOfWeek);
        }

        $format = 'Y-m-d H:i:s.v P';
        $gridStartStr = $gridStart->copy()->startOfDay()->format($format);
        $gridEndStr = $gridEnd->copy()->endOfDay()->format($format);

        $permissions = $this->getPermissionsQuery()
            ->where(function ($query) use ($gridStartStr, $gridEndStr) {
                $query->whereBetween('desde', [$gridStartStr, $gridEndStr])
                      ->orWhereBetween('hasta', [$gridStartStr, $gridEndStr])
                      ->orWhere(function ($sub) use ($gridStartStr, $gridEndStr) {
                          $sub->where('desde', '<=', $gridStartStr)
                              ->where('hasta', '>=', $gridEndStr);
                      });
            })
            ->get();

        $days = [];
        $current = $gridStart->copy();

        while ($current->lte($gridEnd)) {
            $dateStr = $current->toDateString();

            $dayPermissions = $permissions->filter(function ($permission) use ($current) {
                $desde = Carbon::parse($permission->desde)->startOfDay();
                $hasta = Carbon::parse($permission->hasta)->endOfDay();
                return $current->between($desde, $hasta);
            });

            $grouped = [];
            foreach ($dayPermissions as $p) {
                $emp = $p->empleado;
                $groupName = '';
                $type = 'unidad';

                if ($emp) {
                    if ($emp->grupo && $emp->grupo_id != 12) {
                        $groupName = $emp->grupo->nombre;
                        $type = 'grupo';
                    } elseif ($emp->unidad) {
                        $groupName = $emp->unidad->nombre;
                        $type = 'unidad';
                    } else {
                        $groupName = 'General';
                        $type = 'general';
                    }
                } else {
                    $groupName = 'General';
                    $type = 'general';
                }

                if (!isset($grouped[$groupName])) {
                    // Generar un color consistente basado en el hash del nombre
                    $colorIndex = abs(crc32($groupName)) % 6;
                    $colors = [
                        0 => ['border' => 'border-blue-200 dark:border-blue-800', 'bg' => 'bg-blue-50 dark:bg-blue-950/30', 'text' => 'text-blue-700 dark:text-blue-300', 'dot' => 'bg-blue-500'],
                        1 => ['border' => 'border-emerald-200 dark:border-emerald-800', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/30', 'text' => 'text-emerald-700 dark:text-emerald-300', 'dot' => 'bg-emerald-500'],
                        2 => ['border' => 'border-amber-200 dark:border-amber-800', 'bg' => 'bg-amber-50 dark:bg-amber-950/30', 'text' => 'text-amber-700 dark:text-amber-300', 'dot' => 'bg-amber-500'],
                        3 => ['border' => 'border-purple-200 dark:border-purple-800', 'bg' => 'bg-purple-50 dark:bg-purple-950/30', 'text' => 'text-purple-700 dark:text-purple-300', 'dot' => 'bg-purple-500'],
                        4 => ['border' => 'border-rose-200 dark:border-rose-800', 'bg' => 'bg-rose-50 dark:bg-rose-950/30', 'text' => 'text-rose-700 dark:text-rose-300', 'dot' => 'bg-rose-500'],
                        5 => ['border' => 'border-cyan-200 dark:border-cyan-800', 'bg' => 'bg-cyan-50 dark:bg-cyan-950/30', 'text' => 'text-cyan-700 dark:text-cyan-300', 'dot' => 'bg-cyan-500'],
                    ];
                    $style = $colors[$colorIndex];

                    $grouped[$groupName] = [
                        'name' => $groupName,
                        'count' => 0,
                        'type' => $type,
                        'style' => $style,
                    ];
                }
                $grouped[$groupName]['count']++;
            }

            $days[] = [
                'date' => $current->copy(),
                'dateString' => $dateStr,
                'dayNumber' => $current->day,
                'isCurrentMonth' => $current->month === $this->month,
                'isToday' => $current->isToday(),
                'permissionsCount' => $dayPermissions->count(),
                'groupedPermissions' => array_values($grouped),
            ];

            $current->addDay();
        }

        return $days;
    }

    public function render(): View
    {
        $days = $this->getDaysInGrid();
        $monthName = Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');

        return view('livewire.calendario-permisos', [
            'days' => $days,
            'monthName' => ucfirst($monthName),
            'divisions' => $this->isEmployeeRestricted || $this->isGrupoRestricted || $this->isUnidadRestricted || $this->isDivisionRestricted ? [] : Division::all(),
            'unidades' => $this->isEmployeeRestricted || $this->isGrupoRestricted ? [] : ($this->divisionId ? Unidad::where('division_id', $this->divisionId)->get() : Unidad::all()),
            'grupos' => $this->isEmployeeRestricted ? [] : ($this->unidadId ? Grupo::where('unidad_id', $this->unidadId)->get() : Grupo::all()),
            'tipoPermisos' => TipoPermiso::all(),
        ]);
    }
}
