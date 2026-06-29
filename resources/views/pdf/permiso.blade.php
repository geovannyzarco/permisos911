<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Permiso</title>
    <style>
        @page {
            margin: 0;
            size: letter portrait;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            text-transform: uppercase;
        }
        .container {
            position: relative;
            width: 215.9mm;
            height: 279.4mm;
        }
        .background-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .field {
            position: absolute;
            color: #000;
            /* background: rgba(255,0,0,0.1); // Descomenta para depurar posiciones */
        }

        /* --- MAPEO DE CAMPOS (Ajustar según necesidad) --- */

        .id { top: 127px; left: 33px; width: 600px; }
        .fecha { top: 150px; left: 110px; width: 600px; }
        .senor { top: 180px; left: 140px; width: 600px; }
        .yo { top: 208px; left: 100px; width: 450px; }
        .oni { top: 208px; left: 640px; width: 100px; }
        .cargo { top: 238px; left: 140px; width: 250px; }
        .unidad { top: 238px; left: 568px; width: 210px; }
        .unidad-continuation { top: 270px; left: 33px; width: 350px; }
        .departamento { top: 270px; left: 615px; width: 400px; }
        .motivo_texto { top: 720px; left: 450px; width: 400px; }

        /* Periodos */
        .meses { top: 330px; left: 460px; }
        .dias { top: 330px; left: 660px; }
        .horas { top: 360px; left: 115px; }
        .minutos { top: 360px; left: 280px; }
        .desde { top: 360px; left: 415px; }
        .hasta { top: 360px; left: 615px; }
        .dia_letras { top: 393px; left: 120px; width: 250px; }

        /* Checkboxes (X) */
        .check { font-size: 16px; font-weight: bold; }
        .check-personal { top: 430px; left: 240px; }
        .check-compensatorio { top: 460px; left: 240px; }
        .check-cumple { top: 490px; left: 240px; }
        .check-maternidad { top: 523px; left: 240px; }
        .check-delegacion { top: 566px; left: 240px; }
        .check-tratamiento { top: 607px; left: 240px; }
        .check-consulta { top: 435px; left: 501px; }
        .check-enfermedad { top: 493px; left: 501px; }
        .check-estudios { top: 561px; left: 501px; }
        .check-diligencias { top: 596px; left: 501px; }
        .check-marcacion { top: 633px; left: 501px; }
        .check-licencia { top: 430px; left: 758px; }
        .check-mision { top: 475px; left: 758px; }
        .check-paternidad { top: 508px; left: 760px; }
        .check-lactancia { top: 538px; left: 760px; }
        .check-impartir { top: 568px; left: 760px; }
        .check-matrimonio { top: 530px; left: 501px; }
        .check-singocesueldo { top: 600px; left: 572px; width: 230px; }


        .comentarios { top: 765px; left: 150px; width: 400px; }
        .observacion { top: 780px; left: 170px; width: 600px; }

        /* Firmas */
        .firma-solicitante { top: 843px; left: 60px; width: 220px; text-align: center; }
        .firma-vb { top: 843px; left: 520px; width: 220px; text-align: center; }
        .firma-autoriza { top: 900px; left: 224px; width: 220px; text-align: center; }
        .firma-img {
            width: 140px;
            height: 70px;
            object-fit: contain;
            /* Intentamos forzar transparencia si el motor lo permite */
        }

        .page-break {
            page-break-before: always;
        }

        /* --- MAPEO DE CAMPOS COMPENSADO --- */
        .c-fecha { top: 73px; left: 585px; width: 180px; }
        .c-senor { top: 270px; left: 160px; width: 500px; }
        .c-yo { top: 380px; left: 75px; width: 440px; text-align: left; }
        .c-oni { top: 378px; left: 575px; width: 140px; text-align: left; }
        .c-cargo { top: 410px; left:135px; width: 330px; text-align: left; font-size: 11px; }
        .c-dia { top: 430px; left: 755px; width: 40px; text-align: left; }
        .c-mes { top: 464px; left: 70px; width: 120px; text-align: left; font-size: 11px; }
        .c-anio { top: 464px; left: 150px; width: 60px; text-align: left; }
        .c-desde-hora { top: 464px; left: 405px; width: 75px; text-align: left; }
        .c-hasta-hora { top: 462px; left: 600px; width: 75px; text-align: left; }
        .c-tareas { top: 500px; left: 40px; width: 740px; height: 110px; font-size: 11px; line-height: 29.5px; text-transform: uppercase; }
        .c-duracion-horas { top: 708px; left: 755px; width: 45px; text-align: left; }
        .c-duracion-minutos { top: 740px; left: 120px; width: 45px; text-align: left; }
        .c-firma-empleado { top: 780px; left: 120px; width: 220px; text-align: left; }
        .c-firma-jefe-dept { top: 782px; left: 560px; width: 220px; text-align: left; }
        .c-firma-jefe-div { top: 880px; left: 300px; width: 220px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Imagen de fondo -->
        <img src="{{ public_path('formatos/permisos.jpg') }}" class="background-img">

        @php
            // NUEVO: Intentar obtener el Jefe de División desde el snapshot histórico del permiso.
            // Si no está registrado en el histórico, buscarlo de forma dinámica como fallback.
            $jefeDivision = null;
            if (blank($permiso->jefe_division_nombre)) {
                $jefeDivision = \App\Models\Empleado::where('nivel_id', 4)
                    ->whereHas('unidad', function($q) use ($permiso) {
                        $q->where('division_id', $permiso->empleado->unidad?->division_id);
                    })->first();
            }

            // Nombre del jefe de división (histórico o dinámico)
            $jefeNombre = $permiso->jefe_division_nombre ?: ($jefeDivision?->nombre ?? '');
            $jefeFontSize = '12px';
            if (strlen($jefeNombre) > 35) {
                $jefeFontSize = '9px';
            } elseif (strlen($jefeNombre) > 25) {
                $jefeFontSize = '10px';
            }

            // Nombre del empleado solicitante (histórico o dinámico)
            $nombreEmpleado = $permiso->empleado_nombre ?: ($permiso->empleado->nombre ?? '');
            $nombreFontSize = '12px';
            if (strlen($nombreEmpleado) > 35) {
                $nombreFontSize = '9px';
            } elseif (strlen($nombreEmpleado) > 25) {
                $nombreFontSize = '10px';
            }

            // ONI del empleado solicitante (histórico o dinámico)
            $oniEmpleado = $permiso->empleado_oni ?: ($permiso->empleado->oni ?? '');

            // Cargo o categoría del solicitante (histórico o dinámico)
            $cargoNombre = $permiso->cargo_nombre ?: ($permiso->empleado->categoria?->nombre ?? '');
            $cargoFontSize = '12px';
            if (strlen($cargoNombre) > 30) {
                $cargoFontSize = '9px';
            } elseif (strlen($cargoNombre) > 22) {
                $cargoFontSize = '10px';
            }

            // Unidad nombre (que en el diseño del PDF se mapea al nombre de la División)
            $unidadNombre = $permiso->division_nombre ?: ($permiso->empleado->unidad?->division?->nombre ?? '');
            $unidadPart1 = $unidadNombre;
            $unidadPart2 = '';
            // Si tiene más de 24 caracteres, dividirlo en dos líneas
            if (strlen($unidadNombre) > 24) {
                $pos = strrpos(substr($unidadNombre, 0, 24), ' ');
                if ($pos !== false) {
                    $unidadPart1 = substr($unidadNombre, 0, $pos);
                    $unidadPart2 = substr($unidadNombre, $pos + 1);
                } else {
                    $unidadPart1 = substr($unidadNombre, 0, 24);
                    $unidadPart2 = substr($unidadNombre, 24);
                }
            }

            $unidadFontSize = '12px';
            if (strlen($unidadPart1) > 22) {
                $unidadFontSize = '10px';
            }

            // Departamento nombre (que en el diseño del PDF se mapea al nombre de la Unidad/Departamento)
            $deptNombre = $permiso->unidad_nombre ?: ($permiso->empleado->unidad?->nombre ?? '');
            $deptFontSize = '12px';
            if (strlen($deptNombre) > 30) {
                $deptFontSize = '8px';
            } elseif (strlen($deptNombre) > 22) {
                $deptFontSize = '10px';
            }

            // NUEVO HELPER: Función para obtener la representación Base64 de las firmas.
            // Si el campo apunta a un archivo físico del disco privado, se lee y convierte.
            // Si no, se hace fallback a la firma en Base64 directa de las relaciones dinámicas.
            $getFirmaBase64 = function ($firmaPath, $fallbackFirmaBase64) {
                if (filled($firmaPath)) {
                    // Si ya viene codificado como data URI
                    if (str_starts_with($firmaPath, 'data:image/')) {
                        return $firmaPath;
                    }
                    // Si es una ruta de archivo local privado, leer y codificar en base64
                    if (\Illuminate\Support\Facades\Storage::disk('local')->exists($firmaPath)) {
                        try {
                            $fileData = \Illuminate\Support\Facades\Storage::disk('local')->get($firmaPath);
                            return 'data:image/png;base64,' . base64_encode($fileData);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Error cargando firma física en PDF: " . $e->getMessage());
                        }
                    }
                }
                // Fallback dinámico si no hay snapshot o falló la carga
                if (filled($fallbackFirmaBase64) && str_starts_with($fallbackFirmaBase64, 'data:image/')) {
                    return $fallbackFirmaBase64;
                }
                return null;
            };

            // Resolver los Base64 de las firmas para renderizar en el HTML
            $firmaEmpleado = $getFirmaBase64($permiso->empleado_firma, $permiso->empleado?->firma);
            $firmaJefeAprobacion = $getFirmaBase64($permiso->jefe_aprobacion_firma, $permiso->jefeAprobacion?->firma);
            $firmaJefeDivision = $getFirmaBase64($permiso->jefe_division_firma, $jefeDivision?->firma ?? $permiso->jefeDivision?->firma);
        @endphp

        <!-- Datos -->
        <div class="field id">ID: {{ $permiso->id }}</div>
        <div class="field fecha">{{ $permiso->fecha_creacion?->format('d/m/Y') }}</div>
        <div class="field senor" style="font-size: {{ $jefeFontSize }};">{{ $jefeNombre ?: '________________________________________________' }}</div>
        <div class="field yo" style="font-size: {{ $nombreFontSize }};">{{ $nombreEmpleado }}</div>
        <div class="field oni">{{ $oniEmpleado }}</div>
        <div class="field cargo" style="font-size: {{ $cargoFontSize }};">{{ $cargoNombre }}</div>
        <div class="field unidad" style="font-size: {{ $unidadFontSize }};">{{ $unidadPart1 }}</div>
        @if(filled($unidadPart2))
            <div class="field unidad-continuation" style="font-size: {{ $unidadFontSize }};">{{ $unidadPart2 }}</div>
        @endif
        <div class="field departamento" style="font-size: {{ $deptFontSize }};">{{ $deptNombre }}</div>

        <div class="field meses">{{ $permiso->meses ?? '0' }}</div>
        <div class="field dias">{{ $permiso->dias ?? '0' }}</div>
        <div class="field horas">{{ $permiso->horas ?? '0' }}</div>
        <div class="field minutos">{{ $permiso->minutos ?? '0' }}</div>
        <div class="field desde">{{ $permiso->desde?->format('H:i') }}</div>
        <div class="field hasta">{{ $permiso->hasta?->format('H:i') }}</div>
        <div class="field dia_letras">{{ $permiso->desde?->format('d/m/Y') }}</div>

        <!-- Checkboxes (X) tipo permisos-->
        @if($permiso->tipo_permiso_id == 1) {{-- PERMISO PERSONAL --}}
            <div class="field check check-personal">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 2) {{-- POR TIEMPO COMPENSATORIO --}}
            <div class="field check check-compensatorio">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 3) {{-- CUMPLEAÑOS --}}
            <div class="field check check-cumple">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 4) {{-- LICENCIA DE 8 DIAS POR MATERNIDAD --}}
            <div class="field check check-maternidad">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 5) {{-- DELEGACIONES DEPORTIVAS, CULTURAL O CIENTIFICAS --}}
            <div class="field check check-delegacion">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 6) {{-- TRATAMIENTO DE ENFERMEDAD EN EL EXTRANJERO --}}
            <div class="field check check-tratamiento">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 7) {{-- CONSULTA MEDICA --}}
            <div class="field check check-consulta">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 8) {{-- ENFERMEDAD O DUELO --}}
            <div class="field check check-enfermedad">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 9) {{-- ESTUDIOS/HORAS SOCIALES --}}
            <div class="field check check-estudios">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 10) {{-- DILIGENCIAS JUDICIALES/EXTRAJUDICIALES --}}
            <div class="field check check-diligencias">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 11) {{-- FALTA DE MARCACION --}}
            <div class="field check check-marcacion">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 12) {{-- LICENCIA POR ENFERMEDAD SIN INCAPACIDAD --}}
            <div class="field check check-licencia">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 13) {{-- MISION OFICIAL --}}
            <div class="field check check-mision">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 14) {{-- PATERNIDAD --}}
            <div class="field check check-paternidad">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 15) {{-- POR LACTANCIA --}}
            <div class="field check check-lactancia">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 16) {{-- POR IMPARTIR CLASES --}}
            <div class="field check check-impartir">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 17) {{-- MATRIMONIO --}}
            <div class="field check check-matrimonio">X</div>
        @endif

        @if($permiso->tipo_permiso_id == 18) {{-- LICENCIA SIN GOCE DE SUELDO --}}
            <div class="field check check-singocesueldo" style="font-size: 12px; font-weight: normal;">
                Licencia sin goce </br>de sueldo
                <span style="border: 1px solid black; width: 32px; height: 15px; display: inline-block; text-align: center; line-height: 14px; font-weight: bold; font-size: 16px; margin-left: 102px; padding-top: 4px;">X</span>
            </div>
        @endif

        <div class="field motivo_texto">{{ $permiso->motivo }}</div>
        <div class="field comentarios">comentarios{{ $permiso->comentarios }}</div>


        <!-- Firmas -->
        <!-- Firma del solicitante -->
        @if($firmaEmpleado)
            <div class="field firma-solicitante">
                <img src="{{ $firmaEmpleado }}" class="firma-img">
            </div>
        @endif

        <!-- Firma del jefe de departamento -->
        @if($firmaJefeAprobacion)
            <div class="field firma-vb">
               <img src="{{ $firmaJefeAprobacion }}" class="firma-img">
            </div>
        @endif

        <!-- Firma del jefe de división -->
        @if($firmaJefeDivision)
            <div class="field firma-autoriza">
               {{-- CORREGIDO: Anteriormente renderizaba la firma de aprobación aquí por error ($permiso->jefeAprobacion->firma) --}}
               <img src="{{ $firmaJefeDivision }}" class="firma-img">
            </div>
        @endif
    </div>

    @if($permiso->tipo_permiso_id == 2 && $permiso->compensados->isNotEmpty())
        @foreach($permiso->compensados as $comp)
            <div class="container page-break">
                <!-- Imagen de fondo de la hoja de compensado -->
                <img src="{{ public_path('formatos/hoja_compensados.png') }}" class="background-img">

                <!-- Campos de la hoja de compensado -->
                <div class="field c-fecha">{{ $permiso->fecha_creacion?->format('d/m/Y') }}</div>
                <div class="field c-senor">{{ $jefeNombre }}</div>

                <div class="field c-yo">{{ $nombreEmpleado }}</div>
                <div class="field c-oni">{{ $oniEmpleado }}</div>
                <div class="field c-cargo">{{ $cargoNombre }}</div>

                @php
                    $desdeComp = \Carbon\Carbon::parse($comp->desde);
                    $hastaComp = \Carbon\Carbon::parse($comp->hasta);
                    $compMeses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                    $compMesNombre = $compMeses[$desdeComp->month - 1];

                    // Calcular la duración en horas y minutos
                    $diferenciaComp = $desdeComp->diff($hastaComp);
                    $compHoras = $diferenciaComp->h + ($diferenciaComp->d * 24);
                    $compMinutos = $diferenciaComp->i;
                @endphp

                <div class="field c-dia">{{ $desdeComp->format('d') }}</div>
                <div class="field c-mes">{{ $compMesNombre }}</div>
                <div class="field c-anio">{{ $desdeComp->format('Y') }}</div>

                <div class="field c-desde-hora">{{ $desdeComp->format('H:i') }}</div>
                <div class="field c-hasta-hora">{{ $hastaComp->format('H:i') }}</div>

                <div class="field c-tareas">{{ $comp->justificacion }}</div>

                <div class="field c-duracion-horas">{{ $compHoras }}</div>
                <div class="field c-duracion-minutos">{{ $compMinutos }}</div>

                <!-- Firmas -->
                @if($firmaEmpleado)
                    <div class="field c-firma-empleado">
                        <img src="{{ $firmaEmpleado }}" class="firma-img">
                    </div>
                @endif

                @if($firmaJefeAprobacion)
                    <div class="field c-firma-jefe-dept">
                        <img src="{{ $firmaJefeAprobacion }}" class="firma-img">
                    </div>
                @endif

                @if($firmaJefeDivision)
                    <div class="field c-firma-jefe-div">
                        <img src="{{ $firmaJefeDivision }}" class="firma-img">
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</body>
</html>
