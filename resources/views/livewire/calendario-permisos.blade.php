<div class="space-y-6">
    <!-- FILTROS Y CONTROLES SUPERIORES -->
    <div class="p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm space-y-4">
        <!-- BUSCADOR Y FILTROS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Búsqueda -->
            <div>
                <label for="search" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">BUSCAR EMPLEADO</label>
                <div class="relative">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        id="search"
                        placeholder="Nombre o ONI..." 
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-transparent dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-amber-500 focus:ring-amber-500 shadow-sm pl-9"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg width="16" height="16" style="width: 16px; height: 16px;" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- División (Solo Admin/Divisiones) -->
            @if(!$isEmployeeRestricted && !$isGrupoRestricted && !$isUnidadRestricted && !$isDivisionRestricted)
                <div>
                    <label for="division" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">DIVISIÓN</label>
                    <select 
                        wire:model.live="divisionId" 
                        id="division"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-transparent dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-amber-500 focus:ring-amber-500 shadow-sm"
                    >
                        <option value="">Todas las Divisiones</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Unidad (Solo si no es Empleado o Jefe Grupo) -->
            @if(!$isEmployeeRestricted && !$isGrupoRestricted)
                <div>
                    <label for="unidad" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">UNIDAD</label>
                    <select 
                        wire:model.live="unidadId" 
                        id="unidad"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-transparent dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-amber-500 focus:ring-amber-500 shadow-sm"
                    >
                        <option value="">Todas las Unidades</option>
                        @foreach($unidades as $uni)
                            <option value="{{ $uni->id }}">{{ $uni->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Grupo (Solo si no es Empleado) -->
            @if(!$isEmployeeRestricted)
                <div>
                    <label for="grupo" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">GRUPO</label>
                    <select 
                        wire:model.live="grupoId" 
                        id="grupo"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-transparent dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-amber-500 focus:ring-amber-500 shadow-sm"
                    >
                        <option value="">Todos los Grupos</option>
                        @foreach($grupos as $gru)
                            <option value="{{ $gru->id }}">{{ $gru->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Tipo de Permiso -->
            <div>
                <label for="tipoPermiso" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">TIPO DE PERMISO</label>
                <select 
                    wire:model.live="tipoPermisoId" 
                    id="tipoPermiso"
                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-transparent dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-amber-500 focus:ring-amber-500 shadow-sm"
                >
                    <option value="">Todos los Tipos</option>
                    @foreach($tipoPermisos as $tp)
                        <option value="{{ $tp->id }}">{{ $tp->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{--
            <!-- Estado -->
            <div>
                <label for="estado" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">ESTADO APROBACIÓN</label>
                <select 
                    wire:model.live="status" 
                    id="estado"
                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-transparent dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-amber-500 focus:ring-amber-500 shadow-sm"
                >
                    <option value="">Todos los Estados</option>
                    <option value="aprobado">Aprobado Final</option>
                    <option value="pendiente">Pendiente División</option>
                    <option value="anulado">Anulado</option>
                </select>
            </div>
            --}}
        </div>
    </div>

    <!-- CALENDARIO -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <!-- BARRA NAVEGACIÓN CALENDARIO -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Controles de Flecha -->
            <div class="flex items-center gap-2">
                <button 
                    wire:click="goToToday" 
                    class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 transition-all hover:scale-105 active:scale-95"
                >
                    Hoy
                </button>
                <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-800">
                    <button 
                        wire:click="prevMonth" 
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
                        aria-label="Mes anterior"
                    >
                        <svg width="20" height="20" style="width: 20px; height: 20px;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button 
                            wire:click="nextMonth" 
                            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
                            aria-label="Mes siguiente"
                        >
                            <svg width="20" height="20" style="width: 20px; height: 20px;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                    </button>
                </div>
            </div>

            <!-- Título del Mes -->
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">
                {{ $monthName }}
            </h2>
            
            <!-- Espaciador para centrado en desktop -->
            <div class="hidden sm:block w-[116px]"></div>
        </div>

        <!-- CUADRÍCULA DEL CALENDARIO -->
        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40">
            <!-- Días de la semana -->
            <div class="p-3 text-center text-xs font-bold text-rose-500 tracking-wider uppercase">Dom</div>
            <div class="p-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Lun</div>
            <div class="p-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Mar</div>
            <div class="p-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Mié</div>
            <div class="p-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Jue</div>
            <div class="p-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Vie</div>
            <div class="p-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider uppercase">Sáb</div>
        </div>

        <div class="grid grid-cols-7 bg-gray-200 dark:bg-gray-800 gap-[1px]">
            @foreach($days as $day)
                <div 
                    wire:click="openDayModal('{{ $day['dateString'] }}')"
                    class="min-h-[130px] p-2 bg-white dark:bg-gray-900 flex flex-col justify-between transition-all hover:bg-gray-50/50 dark:hover:bg-gray-800/30 cursor-pointer select-none group relative"
                >
                    <!-- Cabecera del día -->
                    <div class="flex items-center justify-between mb-2">
                        <!-- Número de día -->
                        @if($day['isToday'])
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-amber-500 text-white font-bold text-sm shadow-sm ring-4 ring-amber-500/10">
                                {{ $day['dayNumber'] }}
                            </span>
                        @else
                            <span class="font-semibold text-sm {{ $day['isCurrentMonth'] ? 'text-gray-800 dark:text-gray-200' : 'text-gray-300 dark:text-gray-600' }}">
                                {{ $day['dayNumber'] }}
                            </span>
                        @endif

                        <!-- Contador de permisos -->
                        @if($day['permissionsCount'] > 0)
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200/50 dark:border-gray-700/50">
                                {{ $day['permissionsCount'] }} perm.
                            </span>
                        @endif
                    </div>

                    <!-- Lista resumida de permisos -->
                    <div class="space-y-1.5 flex-1 flex flex-col justify-end">
                        @php $limit = 3; $shown = 0; @endphp
                        @foreach($day['groupedPermissions'] as $group)
                            @if($shown < $limit)
                                <div class="px-2 py-1 text-[11px] font-medium rounded-lg border {{ $group['style']['border'] }} {{ $group['style']['bg'] }} {{ $group['style']['text'] }} truncate flex items-center gap-1.5 transition-all duration-200 hover:scale-[1.02] shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $group['style']['dot'] }} shrink-0"></span>
                                    <span class="truncate">{{ $group['name'] }}: {{ $group['count'] }}</span>
                                </div>
                                @php $shown++; @endphp
                            @endif
                        @endforeach

                        <!-- Mostrar más si supera el límite -->
                        @if(count($day['groupedPermissions']) > $limit)
                            <div class="text-[10px] font-bold text-amber-600 dark:text-amber-400 text-right pr-1">
                                +{{ count($day['groupedPermissions']) - $limit }} más
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL DETALLE DEL DÍA (AlpineJS) -->
    <div 
        x-data="{ isOpen: @entangle('isModalOpen') }"
        x-show="isOpen"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        style="display: none;"
        x-on:keydown.escape.window="isOpen = false; $wire.closeModal()"
    >
        <!-- Fondo de Desenfoque -->
        <div 
            x-show="isOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/80 backdrop-blur-sm transition-opacity"
            wire:click="closeModal"
        ></div>

        <!-- Contenedor del Modal -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div 
                x-show="isOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-gray-200 dark:border-gray-800"
            >
                <!-- Cabecera del modal -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        Permisos Activos para el {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d \d\e F \d\e Y') : '' }}
                    </h3>
                    <button 
                        wire:click="closeModal" 
                        class="rounded-lg p-1.5 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    >
                        <svg width="24" height="24" style="width: 24px; height: 24px;" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Contenido del modal -->
                <div class="px-6 py-6 max-h-[70vh] overflow-y-auto space-y-4">
                    @if(empty($selectedDayPermissions))
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400 space-y-2">
                            <svg width="48" height="48" style="width: 48px; height: 48px;" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm">No hay solicitudes de permiso registradas para este día.</p>
                        </div>
                    @else
                        @foreach($selectedDayPermissions as $perm)
                            <div class="p-5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 space-y-4 shadow-sm hover:border-amber-500/30 transition-colors">
                                <!-- Empleado Info -->
                                <div class="flex items-center gap-4">
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-gray-100">{{ $perm['empleado_nombre'] }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            ONI: {{ $perm['empleado_oni'] }} • 
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Unidad:</span> {{ $perm['unidad'] }} • 
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Grupo:</span> {{ $perm['grupo'] }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Detalles Permiso -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs bg-white dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800/80">
                                    <div class="space-y-1.5">
                                        <p><span class="text-gray-500">Tipo de Permiso:</span> <strong class="text-gray-800 dark:text-gray-200">{{ $perm['tipo_permiso'] }}</strong></p>
                                    </div>
                                    <div class="space-y-1.5 border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-800 md:pl-4">
                                        <p><span class="text-gray-500">Desde:</span> <strong class="text-gray-800 dark:text-gray-200">{{ $perm['desde'] }}</strong></p>
                                        <p><span class="text-gray-500">Hasta:</span> <strong class="text-gray-800 dark:text-gray-200">{{ $perm['hasta'] }}</strong></p>
                                    </div>
                                </div>

                                <!-- Flujo de Aprobación -->
                                <div class="flex flex-wrap items-center gap-3">
                                    <!-- Vo.Bo. Jefe Grupo -->
                                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold border 
                                        @if($perm['id_estado_vb'] == 3) bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/20 dark:border-emerald-800 dark:text-emerald-400
                                        @elseif($perm['id_estado_vb'] == 4) bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/20 dark:border-amber-800 dark:text-amber-400
                                        @else bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-950/20 dark:border-rose-800 dark:text-rose-400
                                        @endif"
                                    >
                                        <span>Vo.Bo. Jefe Grupo:</span>
                                        <span>{{ $perm['estado_vb'] }}</span>
                                    </div>

                                    <!-- Aprobación Jefatura -->
                                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold border 
                                        @if($perm['id_estado_aprobacion'] == 3) bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/20 dark:border-emerald-800 dark:text-emerald-400
                                        @elseif($perm['id_estado_aprobacion'] == 4) bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/20 dark:border-amber-800 dark:text-amber-400
                                        @else bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-950/20 dark:border-rose-800 dark:text-rose-400
                                        @endif"
                                    >
                                        <span>Aprob. Unidad:</span>
                                        <span>{{ $perm['estado_aprobacion'] }}</span>
                                    </div>

                                    <!-- Aprobación División -->
                                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold border 
                                        @if($perm['id_estado_aprobacion_jefe_division'] == 3) bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/20 dark:border-emerald-800 dark:text-emerald-400
                                        @elseif($perm['id_estado_aprobacion_jefe_division'] == 4) bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/20 dark:border-amber-800 dark:text-amber-400
                                        @else bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-950/20 dark:border-rose-800 dark:text-rose-400
                                        @endif"
                                    >
                                        <span>Aprob. División:</span>
                                        <span>{{ $perm['estado_division'] }}</span>
                                    </div>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="flex items-center gap-2 pt-2 justify-end">
                                    @if($perm['pdf_url'])
                                        <a 
                                            href="{{ $perm['pdf_url'] }}" 
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white shadow-sm transition-colors"
                                        >
                                            <svg width="16" height="16" style="width: 16px; height: 16px;" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Descargar Hoja de Permiso (PDF)
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Footer del modal -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex justify-end bg-gray-50 dark:bg-gray-800/40">
                    <button 
                        wire:click="closeModal" 
                        class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
